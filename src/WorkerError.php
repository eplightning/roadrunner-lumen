<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class WorkerError
{
    /**
     * @var Throwable
     */
    protected $originalException;

    /**
     * @var string|Throwable|ResponseInterface
     */
    protected $result;

    /**
     * @var bool
     */
    protected $terminate = false;

    /**
     * WorkerError constructor.
     *
     * @param Throwable $originalException
     */
    public function __construct(Throwable $originalException)
    {
        $this->originalException = $originalException;
        $this->result = $originalException;
    }

    /**
     * @return mixed|ResponseInterface
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Throwable
     */
    public function getOriginalException(): Throwable
    {
        return $this->originalException;
    }

    /**
     * @return bool
     */
    public function shouldTerminate(): bool
    {
        return $this->terminate;
    }

    /**
     * @param string|Throwable|ResponseInterface $result
     * @return WorkerError
     */
    public function withResult($result): WorkerError
    {
        $newResult = clone $this;
        $newResult->result = $result;

        return $newResult;
    }

    /**
     * @param bool $terminate
     * @return WorkerError
     */
    public function withTermination($terminate = false): WorkerError
    {
        $newResult = clone $this;
        $newResult->terminate = $terminate;

        return $newResult;
    }
}
