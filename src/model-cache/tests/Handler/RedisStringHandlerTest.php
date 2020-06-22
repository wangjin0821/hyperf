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
namespace HyperfTest\ModelCache\Handler;

use Hyperf\ModelCache\Handler\RedisStringHandler;

/**
 * @internal
 * @coversNothing
 */
class RedisStringHandlerTest extends RedisHandlerTest
{
    protected $handler = RedisStringHandler::class;
}
