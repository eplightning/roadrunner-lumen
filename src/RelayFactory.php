<?php declare(strict_types=1);

namespace Eplightning\RoadRunnerLumen;

use RuntimeException;
use Spiral\Goridge\RelayInterface;
use Spiral\Goridge\SocketRelay;
use Spiral\Goridge\StreamRelay;

class RelayFactory
{
    /**
     * Creates RelayInterface using URI
     *
     * @param string $uri
     * @return RelayInterface
     */
    public static function create(string $uri): RelayInterface
    {
        if (in_array($uri, ['pipe', 'pipes'])) {
            return new StreamRelay(STDIN, STDOUT);
        }

        // TODO: this is really hacky but parse_url doesn't really work well here, maybe consider some URI parsing lib
        if (preg_match('#^unix://(.+)$#', $uri, $matches)) {
            return new SocketRelay($matches[1], null, SocketRelay::SOCK_UNIX);
        }

        if (preg_match('#^tcp://(.*):([0-9]{1,5})$#', $uri, $matches)) {
            $host = !empty($matches[1]) ? $matches[1] : 'localhost';
            $port = (int)$matches[2];

            return new SocketRelay($host, $port, SocketRelay::SOCK_TCP);
        }

        throw new RuntimeException('Could not match any known socket types: ' . $uri);
    }
}
