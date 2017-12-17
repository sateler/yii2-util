<?php

namespace sateler\util\formatters;

use yii\helpers\Html;
use sateler\util\helpers\StringHelper;

/**
 * Behavior that adds an Acronym formatter
 *
 * @author rsateler
 */
class AcronymFormatBehavior extends \yii\base\Behavior
{
    public function asAcronym($val, $dataToggle = 'tooltip')
    {
        if ($val === null) {
            return $this->owner->nullDisplay;
        }
        
        return Html::tag('span', $this->owner->asText(StringHelper::Acronym($val)), ['title' => Html::encode($val), 'data-toggle' => $dataToggle]);
    }
}
