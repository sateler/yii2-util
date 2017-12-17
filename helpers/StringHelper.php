<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace sateler\util\helpers;

use yii\helpers\StringHelper as BaseStringHelper;

/**
 * Description of StringHelper
 *
 * @author rsateler
 */
class StringHelper extends BaseStringHelper
{
    public static function Acronym($string)
    {
        return implode('', array_map(function($word) { return mb_strtoupper(mb_substr($word, 0, 1)); }, preg_split("/[\s]+/", preg_replace("/[^A-Za-z]/",' ',$string))));
    }
}