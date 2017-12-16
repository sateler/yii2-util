Utilities for developing yii2 apps
==================================
Various utilities for yii2 development

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist sateler/yii2-util "*"
```

or add

```
"sateler/yii2-util": "*"
```

to the require section of your `composer.json` file.


Grid LinkColumn
-----

Usage

```php
<?= GridView::widget([
    // other options...
    'columns' => [

        [
            'class' => LinkColumn::class,
            'attribute' => 'name',
            'idAttribute' => 'id', // 'id' by default,
            'action' => 'update', // 'view' by default
            'controller' => 'models', // null by default (current controller)
            //  if you need something more complex than a simple link
            // 'urlCreator' => function ($model, $key, $index) { return ['controller/action', 'id' => $model->id]; }
            'linkOptions' => [], // or a callable for each row
        ],
        // other columns...
    ]
]);
```