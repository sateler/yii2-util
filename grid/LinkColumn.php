<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace sateler\util\grid;

use yii\grid\DataColumn;
use yii\helpers\Html;
use Yii;

/**
 * Description of LinkColumn
 *
 * @author felipe
 */
class LinkColumn extends DataColumn
{
    /** @var string the id property of each model. Defaults to id */
    public $idAttribute = 'id';

    /** @var string the id attribute name for the generated link url. Defaults to id (...?id=...) */
    public $linkIdAttribute = 'id';
    
    /** @var string the action to link to. Defaults to 'view' */
    public $action = 'view';

    /** @var callable|null If custom url is needed. Parameters are `$model`, `$key`, `$index` */
    public $urlCreator = null;

    /** @var callable|array Html options for the `<a>` tag. If callable, parameters are `$model`, `$key`, `$index`*/
    public $linkOptions = [];
    
    /** @var string Controller to link to. Defaults to the current one */
    public $controller = null;
    
    public function init()
    {
        parent::init();
        if (!$this->urlCreator) {
            $this->urlCreator = [$this, 'defaultUrlCreator'];
        }
        if (!is_callable($this->linkOptions)) {
            $orig = $this->linkOptions;
            $this->linkOptions = function () use ($orig) { return $orig; };
        }
    }
    
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        $url = call_user_func($this->urlCreator, $model, $key, $index);
        $options = call_user_func($this->linkOptions, $model, $key, $index);
        return Html::a($content, $url, $options);
    }
    
    private function defaultUrlCreator($model, $key, $index) {
        $action = $this->controller ? "{$this->controller}/{$this->action}" : $this->action;
        return [$action, $this->linkIdAttribute => $model->{$this->idAttribute}];
    }
}
