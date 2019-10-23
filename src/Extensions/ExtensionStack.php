<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadRunnerLumen\Config;
use Eplightning\RoadrunnerLumen\WorkerError;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\PSR7Client;
use Symfony\Component\HttpFoundation\Response;

class ExtensionStack implements ExtensionInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions;

    /**
     * ExtensionStack constructor.
     *
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @param Container $application
     * @param Config $config
     */
    public function init(Container $application, Config $config): void
    {
        foreach ($this->extensions as $extension) {
            $extension->init($application, $config);
        }
    }

    /**
     * @param Container $application
     * @param PSR7Client $client
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function handleRequest(Container $application, PSR7Client $client, ServerRequestInterface $request): bool
    {
        foreach ($this->extensions as $extension) {
            if ($extension->handleRequest($application, $client, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     */
    public function beforeRequest(Container $application, ServerRequestInterface $request): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeRequest($application, $request);
        }
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function afterRequest(
        Container $application,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): bool {
        $terminate = false;

        foreach ($this->extensions as $extension) {
            $terminate = ($extension->afterRequest($application, $request, $response) or $terminate);
        }

        return $terminate;
    }

    /**
     * @param Container $application
     * @param Request $request
     */
    public function beforeHandle(Container $application, Request $request): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeHandle($application, $request);
        }
    }

    /**
     * @param Container $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Container $application, Request $request, Response $response): void
    {
        foreach ($this->extensions as $extension) {
            $extension->afterHandle($application, $request, $response);
        }
    }

    /**
     * @param Container $application
     * @return mixed
     */
    public function beforeLoop(Container $application): void
    {
        foreach ($this->extensions as $extension) {
            $extension->beforeLoop($application);
        }
    }

    /**
     * @param Container $application
     * @return mixed
     */
    public function afterLoop(Container $application): void
    {
        foreach ($this->extensions as $extension) {
            $extension->afterLoop($application);
        }
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param WorkerError $e
     * @return WorkerError
     */
    public function error(Container $application, ServerRequestInterface $request, WorkerError $e): WorkerError
    {
        foreach ($this->extensions as $extension) {
            $e = $extension->error($application, $request, $e);
        }

        return $e;
    }
}
