<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadrunnerLumen\WorkerError;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Container $application
     * @param Request $request
     * @param Response $response
     */
    public function afterHandle(Container $application, Request $request, Response $response): void
    {
        $this->forgetInstances($application);
    }

    /**
     * @param Container $application
     * @param ServerRequestInterface $request
     * @param WorkerError $e
     * @return WorkerError
     */
    public function error(Container $application, ServerRequestInterface $request, WorkerError $e): WorkerError
    {
        $this->forgetInstances($application);
        return $e;
    }

    /**
     * Forget instances
     *
     * @param Container $application
     */
    protected function forgetInstances(Container $application): void
    {
        foreach ($this->abstracts as $abstract) {
            $application->forgetInstance($abstract);
        }
    }
}
