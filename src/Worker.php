<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen;

use Eplightning\RoadRunnerLumen\Extensions\ExtensionInterface;
use Eplightning\RoadRunnerLumen\Extensions\ExtensionStack;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Psr\Http\Message\ResponseInterface;
use Spiral\Goridge\RPC;
use Spiral\RoadRunner\Metrics;
use Spiral\RoadRunner\PSR7Client;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Throwable;

class Worker
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ExtensionInterface
     */
    protected $extensionStack;

    /**
     * Worker constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initialize worker
     *
     * Loads Lumen application and initializes extensions
     */
    public function init(): void
    {
        $this->app = $this->createApplication();

        $this->extensionStack = $this->createExtensionStack();
        $this->extensionStack->init($this->app, $this->config);
    }

    /**
     * Serves HTTP requests
     */
    public function serve(): void
    {
        // create all the objects
        $worker = new \Spiral\RoadRunner\Worker($this->config->getWorkerRelay());
        $client = $this->createPsr7Client($worker);
        $requestBridge = new HttpFoundationFactory;
        $responseBridge = $this->createResponseBridge();
        $rpc = $this->createRpc();

        // expose some of the objects to the application
        if ($rpc != null) {
            $this->app->instance(RPC::class, $rpc);
            $this->app->instance(Metrics::class, new Metrics($rpc));
        }

        // main loop
        $this->extensionStack->beforeLoop($this->app);

        while ($psrRequest = $client->acceptRequest()) {
            try {
                // allows full interception of requests by extensions
                $handled = $this->extensionStack->handleRequest($this->app, $client, $psrRequest);
                if ($handled) {
                    continue;
                }

                $this->extensionStack->beforeRequest($this->app, $psrRequest);
                $request = Request::createFromBase($requestBridge->createRequest($psrRequest));

                $this->extensionStack->beforeHandle($this->app, $request);
                $response = $this->app->handle($request);
                $this->extensionStack->afterHandle($this->app, $request, $response);

                $psrResponse = $responseBridge->createResponse($response);
                $client->respond($psrResponse);

                $this->extensionStack->afterRequest($this->app, $psrRequest, $psrResponse);
            } catch (Throwable $e) {
                $result = $this->extensionStack->error($this->app, $psrRequest, $e);

                if (is_string($result)) {
                    $worker->error((string)$result);
                } else if ($result instanceof ResponseInterface) {
                    $client->respond($result);
                } else {
                    $worker->error((string)$e);
                }
            }
        }

        $this->extensionStack->afterLoop($this->app);
    }

    /**
     * @return Application
     */
    protected function createApplication(): Application
    {
        return require $this->config->getBootstrapFilePath();
    }

    /**
     * @return ExtensionInterface
     */
    protected function createExtensionStack(): ExtensionInterface
    {
        $extensions = $this->app->tagged(ExtensionInterface::class);
        $extensions = is_array($extensions) ? $extensions : iterator_to_array($extensions);

        return new ExtensionStack($extensions);
    }

    /**
     * @return RPC|null
     */
    protected function createRpc(): ?RPC
    {
        if ($this->config->getRpcRelay() != null) {
            return new RPC($this->config->getRpcRelay());
        }

        return null;
    }

    /**
     * @param \Spiral\RoadRunner\Worker $worker
     * @return PSR7Client
     */
    protected function createPsr7Client(\Spiral\RoadRunner\Worker $worker): PSR7Client
    {
        // TODO: Try other PSR17 implementations
        return new PSR7Client($worker);
    }

    /**
     * @return HttpMessageFactoryInterface
     */
    protected function createResponseBridge(): HttpMessageFactoryInterface
    {
        // TODO: Try other PSR17 implementations
        return new DiactorosFactory();
    }
}
