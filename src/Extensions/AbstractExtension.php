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

abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * @param Container $application
     * @param PSR7Client $client
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function handleRequest(Container $application, PSR7Client $client, ServerRequestInterface $request): bool
    {
        return false;
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     */
    public function beforeRequest(Container $application, ServerRequestInterface $request): void
    {
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
        return false;
    }

    /**
     * @param Container $application
     * @param Request $request
     */
    public function beforeHandle(Container $application, Request $request): void
    {
    }

    /**
     * @param Container $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Container $application, Request $request, Response $response): void
    {
    }

    /**
     * @param Container $application
     * @param Config $config
     * @return mixed
     */
    public function init(Container $application, Config $config): void
    {
    }

    /**
     * @param Container $application
     * @return mixed
     */
    public function beforeLoop(Container $application): void
    {
    }

    /**
     * @param Container $application
     * @return mixed
     */
    public function afterLoop(Container $application): void
    {
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param WorkerError $e
     * @return WorkerError
     */
    public function error(Container $application, ServerRequestInterface $request, WorkerError $e): WorkerError
    {
        return $e;
    }
}
