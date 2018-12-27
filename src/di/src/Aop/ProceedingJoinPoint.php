<?php

namespace Hyperflex\Di\Aop;


use Closure;

class ProceedingJoinPoint
{

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $method;

    /**
     * @var mixed[]
     */
    public $arguments;

    /**
     * @var mixed
     */
    public $result;

    /**
     * @var Closure
     */
    public $originalMethod;

    /**
     * @var Closure
     */
    public $pipe;

    public function __construct(Closure $originalMethod, string $className, string $method, array $arguments)
    {
        $this->originalMethod = $originalMethod;
        $this->className = $className;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    public function process()
    {
        $closure = $this->pipe;
        return $closure($this);
    }

    public function processOriginalMethod()
    {
        $this->pipe = null;
        $closure = $this->originalMethod;
        if (count($this->arguments['keys']) > 1) {
            $arguments = value(function () {
                $result = [];
                foreach ($this->arguments['order'] as $order) {
                    $result[] = $this->arguments['keys'][$order];
                }
                return $result;
            });
        } else {
            $arguments = array_values($this->arguments['keys']);
        }
        return $closure(...$arguments);
    }

}