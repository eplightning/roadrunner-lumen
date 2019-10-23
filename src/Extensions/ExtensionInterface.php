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

interface ExtensionInterface
{
    /**
     * Executed right after receiving request from the RR server, allows full interception of requests.
     *
     * If this method returns true, it MUST either throw an exception or correctly handle request by using $client
     *
     * @param Container $application
     * @param PSR7Client $client
     * @param ServerRequestInterface $request
     * @return bool True if this extension handled the request
     */
    public function handleRequest(Container $application, PSR7Client $client, ServerRequestInterface $request): bool;

    /**
     * Executed before request is transformed into HttpFoundation-style request
     *
     * @param Container $application
     * @param ServerRequestInterface $request
     */
    public function beforeRequest(Container $application, ServerRequestInterface $request): void;

    /**
     * Executed after response is sent to RR server.
     *
     * It doesn't get called when either unhandled exception was thrown or request was handled by handleRequest extension
     *
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return bool Should worker terminate after this request?
     */
    public function afterRequest(
        Container $application,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): bool;

    /**
     * Executed before passing request to Lumen
     *
     * @param Container $application
     * @param Request $request
     */
    public function beforeHandle(Container $application, Request $request): void;

    /**
     * Executed after receiving response from Lumen
     *
     * @param Container $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Container $application, Request $request, Response $response): void;

    /**
     * Executed when worker's init method is called
     *
     * @param Container $application
     * @param Config $config
     * @return mixed
     */
    public function init(Container $application, Config $config): void;

    /**
     * Executed when unhandled exception was thrown.
     *
     * Returning PSR-7 response or throwable object allows to override default behavior of passing thrown error to RR
     *
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param WorkerError $e
     * @return WorkerError
     */
    public function error(Container $application, ServerRequestInterface $request, WorkerError $e): WorkerError;

    /**
     * Executed before request loop is started
     *
     * @param Container $application
     * @return mixed
     */
    public function beforeLoop(Container $application): void;

    /**
     * Executed after request loop has ended
     *
     * @param Container $application
     * @return mixed
     */
    public function afterLoop(Container $application): void;
}
