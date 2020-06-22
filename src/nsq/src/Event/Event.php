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
namespace Hyperf\Nsq\Event;

use Hyperf\Nsq\AbstractConsumer;

abstract class Event
{
    /**
     * @var AbstractConsumer
     */
    protected $consumer;

    public function __construct(AbstractConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function getConsumer(): AbstractConsumer
    {
        return $this->consumer;
    }
}
