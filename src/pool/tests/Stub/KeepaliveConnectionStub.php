<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Pool\Stub;

use Hyperf\Pool\KeepaliveConnection;
use Hyperf\Utils\Context;

class KeepaliveConnectionStub extends KeepaliveConnection
{
    protected $activeConnection;

    public function setActiveConnection($connection)
    {
        $this->activeConnection = $connection;
    }

    protected function getActiveConnection()
    {
        return $this->activeConnection;
    }

    protected function sendClose($connection): void
    {
        $data = Context::get('test.pool.heartbeat_connection', []);
        $data['close'] = 'close protocol';
        Context::set('test.pool.heartbeat_connection', $data);
    }

    protected function heartbeat(): void
    {
        $data = Context::get('test.pool.heartbeat_connection', []);
        $data['heartbeat'] = 'heartbeat protocol';
        Context::set('test.pool.heartbeat_connection', $data);
    }
}
