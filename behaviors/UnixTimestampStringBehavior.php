<?php

namespace sateler\util\behaviors;

use DateTime;
use Yii;
use DateTimeZone;
use yii\base\InvalidValueException;

/**
 * Creates a virtual attribute that renders an underlying unix timestamp as a string date
 */
class UnixTimestampStringBehavior extends CastAttributeBehavior
{
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';
    /** @var string The format to use. Must be in php format (not intl) */
    public $format = self::DEFAULT_FORMAT;

    function init() {
        $this->toVirtual = function ($v) {
            if (!$v) {
                return null;
            }
            $d = DateTime::createFromFormat("U", $v, $this->getTimezone());
            return $d->format($this->format);
        };
        $this->toUnderlying = function ($v) {
            if (!$v) {
                return null;
            }
            $d = DateTime::createFromFormat($this->format, $v, $this->getTimezone());
            if ($d === false) {
                throw new InvalidValueException("Could not parse '$v' with format '{$this->format}'");
            }
            return $d->getTimestamp();
        };
        parent::init();
    }

    private function getTimezone() {
        return new DateTimeZone(Yii::$app->timeZone);
    }
}