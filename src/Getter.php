<?php

declare(strict_types=1);

namespace unify\connector;

use Roave\BetterReflection\Reflection\Exception\PropertyDoesNotExist;

abstract class Getter
{
    /**
     * @var array 对象属性
     */
    protected $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } else {
            throw PropertyDoesNotExist::fromName($name);
        }
    }
}
