<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen;

use RuntimeException;
use Spiral\Goridge\RelayInterface;

class Config
{
    /**
     * @var string
     */
    protected $bootstrapFilePath;

    /**
     * @var RelayInterface|null
     */
    protected $rpcRelay;

    /**
     * @var RelayInterface|null
     */
    protected $workerRelay;

    /**
     * Config constructor.
     *
     * @param string $bootstrapFilePath
     * @param RelayInterface $workerRelay
     * @param null|RelayInterface $rpcRelay
     */
    public function __construct(
        string $bootstrapFilePath,
        RelayInterface $workerRelay,
        ?RelayInterface $rpcRelay = null
    ) {
        $this->bootstrapFilePath = $bootstrapFilePath;
        $this->workerRelay = $workerRelay;
        $this->rpcRelay = $rpcRelay;
    }

    public function getBootstrapFilePath(): string
    {
        return $this->bootstrapFilePath;
    }

    /**
     * @return null|RelayInterface
     */
    public function getRpcRelay(): ?RelayInterface
    {
        return $this->rpcRelay;
    }

    /**
     * @return RelayInterface
     */
    public function getWorkerRelay(): RelayInterface
    {
        return $this->workerRelay;
    }

    /**
     * Creates instance and environment variables
     *
     * @throws RuntimeException
     * @return Config
     */
    public static function createFromGlobals(): Config
    {
        // TODO: add option to pass config via argv
        $bootstrapFile = env('RR_BOOTSTRAP_PATH', 'bootstrap/app.php');
        $workerRelay = env('RR_WORKER_RELAY', 'pipe');
        $rpcRelay = env('RR_RPC', null);

        if (!is_file($bootstrapFile)) {
            throw new RuntimeException('Bootstrap file ' . $bootstrapFile . ' not found');
        }

        $workerRelay = RelayFactory::create($workerRelay);

        if (!empty($rpcRelay)) {
            $rpcRelay = RelayFactory::create($rpcRelay);
        } else {
            $rpcRelay = null;
        }

        return new Config($bootstrapFile, $workerRelay, $rpcRelay);
    }
}
