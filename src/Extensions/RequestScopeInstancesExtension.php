<?php

namespace Eplightning\RoadRunnerLumen\Extensions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Extension which clears specified container instances after request is processed
 *
 * This basically gives each HTTP request a unique instance of those bindings
 *
 * @package Eplightning\RoadrunnerLumen\Extensions
 */
class RequestScopeInstancesExtension extends AbstractExtension
{
    /**
     * @var array
     */
    protected $abstracts;

    /**
     * RequestLifecycleInstancesExtension constructor.
     *
     * @param array $abstracts
     */
    public function __construct(array $abstracts)
    {
        $this->abstracts = $abstracts;
    }

    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Application $application, Request $request, Response $response): void
    {
        $this->forgetInstances($application);
    }

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param Throwable $e
     * @return null
     */
    public function error(Application $application, ServerRequestInterface $request, Throwable $e)
    {
        $this->forgetInstances($application);
        return null;
    }

    /**
     * Forget instances
     *
     * @param Application $application
     */
    protected function forgetInstances(Application $application): void
    {
        foreach ($this->abstracts as $abstract) {
            $application->forgetInstance($abstract);
        }
    }
}
