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
            'linkIdAttribute' => 'id', // the id attribute name for the generated link url, 'id' by default (...?id=...)
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

Acronym Formatter
-----

Formats a string into it's Acronym and show's the complete string as a tooltip title.
Load the behavior in the `config/web.php`:

    'formatter' => [
        'class' => \yii\i18n\Formatter::className(),
        'as acronymFormatter' => \sateler\util\formatters\AcronymFormatBehavior::className(),
    ],

Or if you use another formatter class, add the behavior:

    public function behaviors() {
        return [ \sateler\util\formatters\AcronymFormatBehavior::className() ];
    }


Then you can use `Yii::$app->formatter->asAcronym()`, or specify the `acronym` format in `GridView` or `DetailView`.
