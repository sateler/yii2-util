<?php

namespace sateler\util\behaviors;

use yii\base\InvalidConfigException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use Ramsey\Uuid\Uuid;

/**
 * Generates a UUID before insert. By default a UUIDv4 will be generated.
 *
 * To use UuidColumnBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use sateler\util\behaviors\UuidColumnBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         UuidColumnBehavior::className(),
 *         'attribute' => 'my_uuid_column',
 *     ];
 * }
 * ```
 */
class UuidColumnBehavior extends AttributeBehavior
{

    /**
     * @var string the attribute where a UUID will be stored.
     */
    public $attribute;
    
    public function init()
    {
        parent::init();
        if (empty($this->attribute)) {
            throw new InvalidConfigException("attribute property is required");
        }
        if (!is_string($this->attribute)) {
            throw new InvalidConfigException("attribute must be a string");
        }
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->attribute],
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Uuid::uuid4()->toString();
        }

        return parent::getValue($event);
    }

}
