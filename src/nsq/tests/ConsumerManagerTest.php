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
namespace HyperfTest\Nsq;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Nsq\AbstractConsumer;
use Hyperf\Nsq\Annotation\Consumer;
use Hyperf\Nsq\ConsumerManager;
use Hyperf\Process\ProcessManager;
use HyperfTest\Nsq\Stub\ContainerStub;
use HyperfTest\Nsq\Stub\DemoConsumer;
use HyperfTest\Nsq\Stub\DisabledDemoConsumer;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ConsumerManagerTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        ProcessManager::clear();
    }

    public function testConsumerAnnotation()
    {
        $container = ContainerStub::getContainer();
        AnnotationCollector::collectClass(DemoConsumer::class, Consumer::class, new Consumer([
            'topic' => $topic = uniqid(),
            'channel' => $channel = uniqid(),
            'name' => $name = uniqid(),
            'nums' => $nums = rand(1, 10),
        ]));

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'nsq' => [
                'default' => [
                    'enable' => true,
                ],
            ],
        ]));
        $manager = new ConsumerManager($container);
        $manager->run();
        $hasRegisted = false;
        foreach (ProcessManager::all() as $item) {
            if (method_exists($item, 'getConsumer')) {
                $hasRegisted = true;
                /** @var AbstractConsumer $consumer */
                $consumer = $item->getConsumer();
                $this->assertTrue($item->isEnable());
                $this->assertSame($name, $consumer->getName());
                $this->assertSame($channel, $consumer->getChannel());
                $this->assertSame($topic, $consumer->getTopic());
                $this->assertSame($nums, $item->nums);
                break;
            }
        }
        $this->assertTrue($hasRegisted);
    }

    public function testConsumerAnnotationNotEnableByConfig()
    {
        $container = ContainerStub::getContainer();
        AnnotationCollector::collectClass(DemoConsumer::class, Consumer::class, new Consumer([
            'topic' => $topic = uniqid(),
            'channel' => $channel = uniqid(),
            'name' => $name = uniqid(),
            'nums' => $nums = rand(1, 10),
        ]));

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'nsq' => [
                'default' => [
                    'enable' => false,
                ],
            ],
        ]));

        $manager = new ConsumerManager($container);
        $manager->run();

        foreach (ProcessManager::all() as $item) {
            if (method_exists($item, 'getConsumer')) {
                /* @var AbstractConsumer $consumer */
                $this->assertFalse($item->isEnable());
                break;
            }
        }
    }

    public function testConsumerAnnotationNotEnableByConsumer()
    {
        $container = ContainerStub::getContainer();
        AnnotationCollector::collectClass(DisabledDemoConsumer::class, Consumer::class, new Consumer([
            'topic' => $topic = uniqid(),
            'channel' => $channel = uniqid(),
            'name' => $name = uniqid(),
            'nums' => $nums = rand(1, 10),
        ]));

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'nsq' => [
                'default' => [
                    'enable' => true,
                ],
            ],
        ]));
        $manager = new ConsumerManager($container);
        $manager->run();
        foreach (ProcessManager::all() as $item) {
            if (method_exists($item, 'getConsumer') && ($item->getConsumer() instanceof DisabledDemoConsumer)) {
                $this->assertFalse($item->isEnable());
                break;
            }
        }
    }
}
