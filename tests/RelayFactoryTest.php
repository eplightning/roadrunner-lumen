<?php

use Eplightning\RoadRunnerLumen\RelayFactory;
use PHPUnit\Framework\TestCase;
use Spiral\Goridge\SocketRelay;
use Spiral\Goridge\StreamRelay;

class RelayFactoryTest extends TestCase
{
    public function testPipe()
    {
        $pipe = RelayFactory::create('pipe');

        $this->assertInstanceOf(StreamRelay::class, $pipe);
    }

    public function testTcpValid()
    {
        /**
         * @var $tcp1 SocketRelay
         * @var $tcp2 SocketRelay
         * @var $tcp3 SocketRelay
         */
        $tcp1 = RelayFactory::create('tcp://:5000');
        $tcp2 = RelayFactory::create('tcp://localhost:1');
        $tcp3 = RelayFactory::create('tcp://10.10.10.10:60000');

        $this->assertInstanceOf(SocketRelay::class, $tcp1);
        $this->assertInstanceOf(SocketRelay::class, $tcp2);
        $this->assertInstanceOf(SocketRelay::class, $tcp3);

        $this->assertEquals('localhost', $tcp1->getAddress());
        $this->assertEquals('localhost', $tcp2->getAddress());
        $this->assertEquals('10.10.10.10', $tcp3->getAddress());

        $this->assertEquals(5000, $tcp1->getPort());
        $this->assertEquals(1, $tcp2->getPort());
        $this->assertEquals(60000, $tcp3->getPort());

        $this->assertEquals(SocketRelay::SOCK_TCP, $tcp1->getType());
        $this->assertEquals(SocketRelay::SOCK_TCP, $tcp2->getType());
        $this->assertEquals(SocketRelay::SOCK_TCP, $tcp3->getType());
    }

    public function testUnixValid()
    {
        /**
         * @var $unix1 SocketRelay
         * @var $unix2 SocketRelay
         */
        $unix1 = RelayFactory::create('unix://test.sock');
        $unix2  = RelayFactory::create('unix:///var/run/test.sock');

        $this->assertInstanceOf(SocketRelay::class, $unix1);
        $this->assertInstanceOf(SocketRelay::class, $unix2);

        $this->assertEquals('test.sock', $unix1->getAddress());
        $this->assertEquals('/var/run/test.sock', $unix2->getAddress());

        $this->assertEquals(SocketRelay::SOCK_UNIX, $unix1->getType());
        $this->assertEquals(SocketRelay::SOCK_UNIX, $unix2->getType());
    }
}
