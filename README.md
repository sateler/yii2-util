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
            // if some links may not be accessible
            //'createLink' => function ($model, $key, $index) { return Yii::$app->user->can('view', ['model' => $model]); },
        ],
        // other columns...
    ]
]);
```

Acronym Formatter
-----

Formats a string into it's Acronym and show's the complete string as a tooltip title.
Load the behavior in the `config/web.php`:

```php
    'formatter' => [
        'class' => \yii\i18n\Formatter::className(),
        'as acronymFormatter' => \sateler\util\formatters\AcronymFormatBehavior::className(),
    ],
```

Or if you use another formatter class, add the behavior:

```php
    public function behaviors() {
        return [ \sateler\util\formatters\AcronymFormatBehavior::className() ];
    }
```

Then you can use `Yii::$app->formatter->asAcronym()`, or specify the `acronym` format in `GridView` or `DetailView`.

Select2SearchBehavior
-----
Makes it easy to generate ajax Select2 based queries and widgets.

Add the behavior to the model and configure it:

```php
    public function behaviors() {
        return [
            'select2' => [
                'class' => Select2SearchBehavior::className(),
                
                // Common configurations:
                'select2SearchAttributes' => ['attr1', 'attr2'], // Required. These are the attributes that will be searched
                'select2SortAttributes' => ['attr3' => SORT_ASC], // To sort the results. Defaults to ['id' => SORT_ASC].
                'select2FilterQuery' => function($query, $params) { $query->joinWith(['other_table'])->andWhere(['attr4' => 'constant']); }, // To modify the query. Defaults to null
                'select2ShowTextFunction' => function($model) { return "{$model->attr1} / ({$model->attr2})"; }, // To build the text property. Defaults to implode the search attributes.
                'select2ExplodeSearchTermChar' => ' ', // Explode the search query using this chars, false to disable. Defaults to ' '.

                // Param names and other config (with default values):
                'select2IdProperty' => 'id', // The property to be used as id
                'select2idParameter' => 'id',
                'select2SearchParameter' => 'search',
                'select2IdParameter' => 'page',
                'select2PageParameter' => 20,
            ],
        ];
    }
```

Enable the controller that provides the data

```php
    public function actionSelect2()
    {
        $model = new Model();
        Yii::$app->response->format = 'json';
        return $model->select2Search(Yii::$app->request->queryParams);
    }
```

In the view use the select2 widget with the url and merge the default config:

```php
    $form->field($formModel, 'id')->widget(Select2::className(), 
        Select2SearchBehavior::getSelect2DefaultOptions(Url::to(['model-controller/select2']),
        [
            'language' => 'es',
            'options' => [
                'placeholder' => 'Seleccionar ítem...',
            ],
        ]
    ))
```

EnumNameBehavior
-----
Autocreate names properties from an array for model attributes.

Add the behavior to the model and configure it:

```php
class User extends ActiveRecord
{
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';

    public static $types = [
        self::TYPE_ADMIN => 'Administrator',
        self::TYPE_USER => 'User',
    ];

    public function behaviors() {
        return [
            [
                'class' => EnumNameBehavior::className(),
                'properties' => [
                    [
                        'values' => self::$types,
                        'property' => 'type',
                        'name' => 'typeName',
                    ],
                ],
            ],
        ];
    }
```

Use the new property:
```php
    echo "The user has a property {$user->type} and a name for it: {$user->typeName}";
```

UuidColumnBehavior
-----
Autocreate names properties from an array for model attributes.

Add the behavior to the model and configure it:

```php
class User extends ActiveRecord
{
    public function behaviors() {
        return [
            [
                'class' => UuidColumnBehavior::className(),
                'attribute' => 'uuid_column',
                // 'value' => a Uuid v4 by default
            ],
        ];
    }
```

UnixTimestampStringBehavior
-----
Creates a virtual attribute with a string representation of a timestamp

Add the behavior to the model and configure it:

```php
class User extends ActiveRecord
{
    public function behaviors() {
        return [
            [
                'class' => UnixTimestampStringBehavior::className(),
                'underlying' => 'timestamp_column',
                'virtual' => 'string_attribute',
                // 'format' => 'Y-m-d', by default
            ],
        ];
    }
```

