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
namespace Hyperf\Nsq\Pool;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Pool;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;

class NsqPool extends Pool
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    public function __construct(ContainerInterface $container, string $name)
    {
        $this->name = $name;
        $config = $container->get(ConfigInterface::class);
        $key = sprintf('nsq.%s', $this->name);
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        $this->config = $config->get($key);
        $options = Arr::get($this->config, 'pool', []);

        parent::__construct($container, $options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function createConnection(): ConnectionInterface
    {
        return make(NsqConnection::class, [
            $this->container,
            $this,
            $this->config,
        ]);
    }
}
