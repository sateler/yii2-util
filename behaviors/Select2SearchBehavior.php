<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace sateler\util\behaviors;

use yii\base\InvalidConfigException;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

class Select2SearchBehavior extends \yii\base\Behavior
{
    /** @var string The id property of each model. Defaults to 'id'. */
    public $select2IdProperty = 'id';
    
    /** @var string[] The list of attributes to search in. Required, no default value. */
    public $select2SearchAttributes;
    
    /** @var string[] The list of attributes to sort the results by. Defaults to 'id'. */
    public $select2SortAttributes = ['id' => SORT_ASC];
    
    /** @var string Explode the query into multiple search terms using this char(s). Set to false or empty to disable exploding. Defaults to ' ' (space). */
    public $select2ExplodeSearchTermChar = ' ';
    
    /** @var string Select2 widget search query param name. Defaults to 'search'. */
    public $select2SearchParameter = 'search';
    
    /** @var string Select2 widget item id param name. Defaults to 'id'. */
    public $select2IdParameter = 'id';
    
    /** @var string Select2 widget page number param name. Defaults to 'page'. */
    public $select2PageParameter = 'page';
    
    /** @var integer Number of items to return per page. Defaults to 20. */
    public $select2PageSize = 20;
    
    /** @var callable Callable that will be called passing the query to be modified at will. Defaults to null. */
    public $select2FilterQuery = null;
    
    /** @var string Callable which will receive each model to generate the 'text' property. Defaults to null, in which case all search attributes will be imploded. */
    public $select2ShowTextFunction = null;
    
    public function init()
    {
        parent::init();
        if (is_string($this->select2SearchAttributes)) {
            $this->select2SearchAttributes = [$this->select2SearchAttributes];
        }
        else if (is_array($this->select2SearchAttributes)) {
            // nothing
        }
        else {
            throw new InvalidConfigException("You need to set the select2SearchAttributes parameter to use Select2SearchBehavior");
        }
    }
    
    /**
     * Generates the default select2 configuration array with the given url. Optional custom configuration array can be merged.
     * @param string $url The select 2 controller url.
     * @param array $config Aditional config options that will be merged.
     * @return array
     */
    public static function getSelect2DefaultOptions($url, $config = [])
    {
        $initScript = <<< SCRIPT
function (element, callback) {
    var id=\$(element).val();
    if (id) {
        \$.ajax("{$url}?" + $.param({id: id}), {
            dataType: "json"
        }).done(function(data) { callback(data.results);});
    }
    else {
        setTimeout(function () { callback([]); }, 0);
    }
}
SCRIPT;
        return ArrayHelper::merge([
            'pluginOptions' => [
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {search:params.term, page:params.page}; }'),
                ],
                'initSelection' => new JsExpression($initScript),
            ]
        ], $config);
    }
    
    public function select2Search($params)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query = $this->owner->find();
        
        if(!$this->select2ShowTextFunction) {
            $this->select2ShowTextFunction = function ($row) {
                $values = array_filter(array_map(function($col) use($row) {
                    return ArrayHelper::getValue($row, $col, '');
                }, $this->select2SearchAttributes));
                return implode(' ', $values);
            };
        }
        
        $id = ArrayHelper::getValue($params, $this->select2IdParameter, null);
        if (is_numeric($id)) {
            $id = [$id];
        }
        elseif (is_string($id) && strlen($id)){
            $id = [$id];
        }
        if(is_array($id)) {
            $row = $query->where([$this->select2IdProperty => $id])->one();
            return [
                'results' => [[
                    $this->select2IdParameter => ArrayHelper::getValue($row, $this->select2IdProperty),
                    'text' => call_user_func($this->select2ShowTextFunction, $row),
                ]],
            ];
        }
        $search = ArrayHelper::getValue($params, $this->select2SearchParameter, null);
        $terms = [$search];
        if($this->select2ExplodeSearchTermChar) {
            $terms = explode($this->select2ExplodeSearchTermChar, $search);
        }
        foreach($terms as $term) {
            $or = [];
            foreach ($this->select2SearchAttributes as $prop) {
                $or[] = ['like', $prop, $term];
            }
            if(count($or)) {
                $query->andFilterWhere(array_merge(['or'], $or));
            }
        }
        
        if ($this->select2FilterQuery != null) {
            call_user_func($this->select2FilterQuery, $query, $params);
        }
        
        $query->orderBy($this->select2SortAttributes);
        
        $page = ArrayHelper::getValue($params, $this->select2PageParameter, 1);
        if (is_numeric($page)) {
            $query->offset( ($page-1) * $this->select2PageSize );
        }
        $query->limit($this->select2PageSize);
        
        $rows = $query->all();
        
        $results = array_map(function($row) {
            return [
                $this->select2IdParameter => ArrayHelper::getValue($row, $this->select2IdProperty),
                'text' => call_user_func($this->select2ShowTextFunction, $row),
            ];
        }, $rows);
        
        return [
            'results' => $results,
            'pagination' => [
                'more' => count($rows) == $this->select2PageSize,
            ],
        ];
    }
}
