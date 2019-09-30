<?php

namespace Eplightning\RoadRunnerLumen\Extensions;

use Laravel\Lumen\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Extension for running gc_collect_cycles
 *
 * @package Eplightning\RoadrunnerLumen\Extensions
 */
class GarbageCollectorExtension extends AbstractExtension
{
    /**
     * @var int
     */
    protected $handledRequests = 0;

    /**
     * @var int
     */
    protected $requestLimit;

    /**
     * GarbageCollectorExtension constructor.
     *
     * @param int $requestLimit How many requests before running gc_collect_cycles
     */
    public function __construct($requestLimit = 1)
    {
        $this->requestLimit = $requestLimit;
    }

    /**
     * @param Application $application
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function afterRequest(
        Application $application,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {
        $this->handledRequests++;

        if ($this->handledRequests >= $this->requestLimit) {
            gc_collect_cycles();
            $this->handledRequests = 0;
        }
    }

    /**
     * @param Application $application
     */
    public function afterLoop(Application $application): void
    {
        $this->handledRequests = 0;
    }
}
