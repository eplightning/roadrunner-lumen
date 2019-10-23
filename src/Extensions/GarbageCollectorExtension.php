<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen\Extensions;

use Illuminate\Container\Container;
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
        $this->handledRequests++;

        if ($this->handledRequests >= $this->requestLimit) {
            gc_collect_cycles();
            $this->handledRequests = 0;
        }

        return false;
    }

    /**
     * @param Container $application
     */
    public function afterLoop(Container $application): void
    {
        $this->handledRequests = 0;
    }
}
