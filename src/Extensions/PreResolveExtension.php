<?php

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadRunnerLumen\Config;
use Laravel\Lumen\Application;

/**
 * Extension which resolves specified container bindings before starting worker loop.
 *
 * Usually used to initialize objects in order to speedup first requests
 *
 * @package Eplightning\RoadrunnerLumen\Extensions
 */
class PreResolveExtension extends AbstractExtension
{
    /**
     * @var array
     */
    protected $abstracts;

    /**
     * PreResolveExtension constructor.
     *
     * @param array $abstracts
     */
    public function __construct(array $abstracts)
    {
        $this->abstracts = $abstracts;
    }

    /**
     * @param Application $application
     * @param Config $config
     */
    public function init(Application $application, Config $config): void
    {
        foreach ($this->abstracts as $abstract) {
            $application->make($abstract);
        }
    }
}
