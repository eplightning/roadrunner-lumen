<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen\Extensions;

use Eplightning\RoadRunnerLumen\Config;
use Illuminate\Container\Container;

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
     * @param Container $application
     * @param Config $config
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function init(Container $application, Config $config): void
    {
        foreach ($this->abstracts as $abstract) {
            $application->make($abstract);
        }
    }
}
