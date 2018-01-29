<?php

namespace sateler\util\behaviors;

use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\base\BaseObject;
use Yii;

/**
 * Description of EnumNameBehavior
 *
 * @author felipe
 */
class EnumNameBehavior extends \yii\base\Behavior
{
    public $properties;
    
    /** @var EnumNameBehaviorConfig[] */
    private $configs;
    
    public function init()
    {
        parent::init();
        if (!is_array($this->properties) || empty($this->properties)) {
            throw new InvalidConfigException("properties must be set to an array");
        }
        $this->configs = [];
        foreach($this->properties as $prop) {
            $prop['owner'] = $this;
            $prop['class'] = EnumNameBehaviorConfig::className();
            $this->configs[] = Yii::createObject($prop);
        }
    }
    
    public function __get($name)
    {
        foreach ($this->configs as $config) {
            if ($config->isMatch($name)) {
                return $config->getValue();
            }
        }
        return parent::__get($name);
    }
    
    public function canGetProperty($name, $checkVars = true)
    {
        foreach ($this->configs as $config) {
            if ($config->isMatch($name)) {
                return true;
            }
        }
        return parent::canGetProperty($name, $checkVars);
    }
}

class EnumNameBehaviorConfig extends BaseObject
{
    public $values;
    public $property;
    public $name;
    
    /** @var EnumNameBehavior */
    public $owner;
    
    public function init()
    {
        parent::init();
        if (!$this->name) throw new InvalidConfigException("Name config is required");
        if (!$this->property) throw new InvalidConfigException("Property config is required");
        if (!$this->values) throw new InvalidConfigException("Values config is required");
        if (!$this->owner) throw new InvalidConfigException("Owner config is required");
    }
    
    public function isMatch($name)
    {
        return strcasecmp($name, $this->name) === 0;
    }
    
    public function getValue()
    {
        $propVal = $this->owner->owner->{$this->property};
        return ArrayHelper::getValue($this->values, $propVal, $propVal);
    }
}