<?php

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadRunnerLumen\Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\PSR7Client;
use Throwable;

interface ExtensionInterface
{
    /**
     * @param Application $application
     * @param PSR7Client $client
     * @param ServerRequestInterface $request
     * @return bool Should continue processing request?
     */
    public function beforeRequest(Application $application, PSR7Client $client, ServerRequestInterface $request): bool;

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function afterRequest(
        Application $application,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void;

    /**
     * @param Application $application
     * @param Request $request
     */
    public function beforeHandle(Application $application, Request $request): void;

    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Application $application, Request $request, Response $response): void;

    /**
     * @param Application $application
     * @param Config $config
     * @return mixed
     */
    public function init(Application $application, Config $config): void;

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param Throwable $e
     */
    public function error(Application $application, ServerRequestInterface $request, Throwable $e): void;

    /**
     * @param Application $application
     * @return mixed
     */
    public function beforeLoop(Application $application): void;

    /**
     * @param Application $application
     * @return mixed
     */
    public function afterLoop(Application $application): void;
}
