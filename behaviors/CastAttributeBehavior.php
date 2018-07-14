<?php

namespace sateler\util\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;

/** Generates a virtual attribute that converts an underlying value into a different format
 * 
 */
class CastAttributeBehavior extends Behavior
{
    /** @var string The underlying attribute name */
    public $underlying;
    /** @var string The virtual attribute name */
    public $virtual;

    /** @var Closure|Callable The method to convert from the underlying to the virtual value */
    public $toVirtual;
    /** @var Closure|Callable The method to convert from the virtual to the underlying value. Optional, in which case the virtual property is readonly */
    public $toUnderlying;

    public function init()
    {
        parent::init();
        if (!$this->underlying) {
            throw new InvalidConfigException('underlying is required');
        }
        if (!$this->virtual) {
            throw new InvalidConfigException('virtual is required');
        }
        if (!$this->toVirtual) {
            throw new InvalidConfigException('toVirtual is required');
        }
    }

    public function canGetProperty($name, $checkVars = true)
    {
        if ($name == $this->virtual) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }
    public function canSetProperty($name, $checkVars = true)
    {
        if ($name == $this->virtual) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars);
    }

    public function __isset($name)
    {
        if ($name == $this->virtual) {
            return isset($this->owner->{$this->underlying});
        }
        return parent::__isset($name);
    }

    public function __get($name)
    {
        if ($name == $this->virtual) {
            return call_user_func($this->toVirtual, $this->owner->{$this->underlying});
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if ($this->toUnderlying && $name === $this->virtual) {
            $value = call_user_func($this->toUnderlying, $value);
            $this->owner->{$this->underlying} = $value;
            return;
        }
        return parent::__set($name, $value);
    }
}
