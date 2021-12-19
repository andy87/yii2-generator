<p align="center">
    <img src="https://raw.githubusercontent.com/andy87/yii2-generator/master/logo.png" alt="Yii2">
    <h1 align="center">yii2-generator</h1>
</p>

Класс для генерации моделей & крудов(crud). С простой настройкой для пакетной генерации.  

<hr>
  
***Решаемые задачи/цели:*** 
* быстрая генерация Models & CRUD для таблиц в массиве  
* возможность кастомизации параметров генерации  

<small>*Мне всегда не нравилось по одной модельке генерировать поэтому содал этот компонент.*</small>

<hr>

## Пример использования.
### Код
Пример контроллера для выполнения консольных команд(advanced)  
```php

//namespace app\commands; // Basic
namespace console\controllers; // Advanced

use andy87\yii2_generator\components\Generator;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use andy87\yii2_generator\controllers\GeneratorController;

/**
 *  Class `GenerateController`
 *
 *  Вариант с таблицами в константе
 *
 * @package console\controllers
 */
class GenerateController extends GeneratorController
{
    // Constants

    /** @var string[] Список таблиц учавствующих в генерации */
    const TABLE_LIST = [
        'user',
        'order',
        'product'
        //...
    ];



    // Methods

    /**
     * Генерация одной модели
     *
     * php yii generate/model
     *
     * @throws InvalidConfigException
     */
    public function actionModel( string $tableName )
    {
        /** Advanced */
        $this->generator->generateModel( 'common\\models', $tableName, ( $baseClass = yii\db\ActiveRecord::class ) );
        //Аналогичная запись без хардкода
        $this->generator->generateModel( Generator::DEFAULT_NS_MODELS, $tableName, Generator::DEFAULT_MODEL_PARENT_CLASS );

        /** Basic */
        $this->generator->generateModel( 'app\\models', $tableName );
    }

    /**
     * Генерация одного круд'а (CRUD)
     *
     * php yii generate/crud
     *
     * @throws InvalidConfigException
     */
    public function actionCrud( string $tableName )
    {
        $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
        $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

        $this->generator->generateCrud(
            Generator::DEFAULT_NS_MODELS . '\\' . $tableNameCamelCase,
            "common\\models\\search\\{$tableNameCamelCase}Search",
            "@backend/views/$tableNameKebabCase",
            "backend\\controllers\\{$tableNameCamelCase}Controller",
            \backend\components\controllers\WebController::class  // Default: \yii\web\Controller::class
        );
    }

    /**
     * Пакетная генерация Model'ей
     *
     * php yii generate/list-models
     *
     * @throws InvalidConfigException
     */
    public function actionListModels()
    {
        // Generate with default params
        $this->generator->generateModelArray( self::TABLE_LIST );

        //Generate with custom params
        $this->generator->generateModelArray(
            self::TABLE_LIST,
            ( $nameSpaceModelClass = 'common\\models\\some\\dir' ), // Default: common\\models
            ( $baseClass = yii\db\ActiveRecord::class ) // Default: yii\db\ActiveRecord::class
        );
    }
    
    /**
     * Пакетная генерация CRUD'ов
     *
     * php yii generate/list-cruds
     *
     * @throws InvalidConfigException
     */
    public function actionListCruds()
    {
        // Generate with default params
        $this->generator->generateCrudArray( self::TABLE_LIST );

        //Generate with custom params
        $this->generator->generateCrudArray(
            self::TABLE_LIST,
            ( $nameSpaceModelClass = 'common\\models\\custom\\folder' ), // Default: common\\models
            ( $nameSpaceSearchModelClass = 'common\\models\\custom\\folder\\search' ), // Default: common\\models\\search
            ( $baseViewPath = '@backend/views/custom/path/' ), // Default: backend\\controllers
            ( $nameSpaceControllerClass = 'backend\\controllers\\custom\\dir' ), // Default: @backend/views
            ( $baseControllerClass = \backend\components\controllers\WebController::class ) // Default: yii\web\Controller::class;
        );
    }
}
```
### Консоль
Далее в консоле выполняются команды:
* php yii generate/model table_name
* php yii generate/crud table_name
* php yii generate/list-models
* php yii generate/list-cruds

<hr>

# Детальнее.
Создаётся контроллер выполнения консольных команд, наследуемый от `andy87\yii2_generator\controller\GeneratorController`.  
*Имя контроллера и комманд на ваше усмотрение.*
```php
//namespace app\commands; // Basic
namespace console\controllers; // Advanced

use andy87\yii2_generator\controller\GeneratorController;

class GeneratorController extends GeneratorController
{
    //some code
}
```

Константы контроллера:
 * *bool* **DISPLAY_INFO** - Статус вывода информации о процессе генерации (по умолчанию: **TRUE** ).  
 
в контроллере создаются `actions`, в которых вызываются методы компонента `Generator`:
* `generateModel()`
* `generateCrud()`  
* `generateModelArray()`  
* `generateCrudArray()`  
  
### Генерация модели
#### ->generateModel()
* *string* **$ns** - `namespace` модели
* *string* **$tableName** - имя таблицы для которой создаётся модель
* *?string* **$modelClass** - имя создаваемой модели (*необязательный*)
```php
public function actionModels( string $tableName )
{
    $this->generator->generateModel( 'common\\models', $tableName );
}
```
  
### Генерация одного круд'а (CRUD)
#### ->generateCrud()
* *string* **$modelClass** - Имя класса/модели для которого генерируется CRUD
* *string* **$searchModelClass** - полный путь класса для модели реализующей поиск сущностей в таблице
* *string* **$viewPath** - путь для генерирования шаблонов
* *string* **$controllerClass** - полный путь класса для генерируемого контроллера
* *string* **$baseControllerClass** - полный путь Родительского класса для генерируемого контроллера (*необязательный*, по умолчанию: **yii\web\Controller** )
```php
public function actionCrud( string $tableName )
{
    $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
    $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

    $this->generator->generateCrud(
        Generator::DEFAULT_NS_MODELS . '\\' . $tableNameCamelCase,
        "common\\models\\search\\{$tableNameCamelCase}Search",
        "@backend/views/$tableNameKebabCase",
        "backend\\controllers\\{$tableNameCamelCase}Controller",
        \yii\web\Controller::class
    );
}
```

### Пакетная генерация Model'ей
#### ->generateModelArray()
* *string* **$tableNameList** - массив таблиц для которых будут сгенерированы модели
* *string* **$ns** - namespace генерируемой модели
```php
    public function actionListModels()
    {
        $this->generator->generateModelArray(
            self::TABLE_LIST, // Либо другйо источник
            ( $nameSpaceModelClass = 'common\\models' )
        );
    }
```

### Пакетная генерация CRUD'ов
#### ->generateCrudArray()
* *string[]* **$tableNameList** - массив таблиц для которых будут сгенерированы круды(crud)
* *string* **$nameSpaceModelClass** - namespace класса/модели для которого генерируется CRUD
* *string* **$nameSpaceSearchModelClass** - namespace класса для модели реализующей поиск сущностей в таблице
* *string* **$baseViewPath** - базовый путь для дирректории с шаблонами
* *string* **$nameSpaceControllerClass** - namespace класса для генерируемого контроллера круда
* *string* **$baseControllerClass** - Полный путь Родительского класса для генерируемого контроллера круда
```php
public function actionListCruds()
{
    $this->generator->generateCrudArray(
        self::TABLE_LIST, // Либо другйо источник
        ( $nameSpaceModelClass = 'common\\models' ),
        ( $nameSpaceSearchModelClass = 'common\\models\\search' ),
        ( $baseViewPath = '@backend/views' ),
        ( $nameSpaceControllerClass = 'backend\\controllers' ),
        ( $baseControllerClass = \yii\web\Controller::class )
    );
}
```

<hr>

# Установка

## Зависимости
- php ( >= 7.4 )
- yii2 

## composer.json
Установка с помощью [composer](https://getcomposer.org/download/)

Добавить в `composer.json`  
<small>require</small>
```
"require": {
    ...
    "andy87/yii2-generator" : ">=1.1.3"
},
```
<small>repositories</small>
```
"repositories": [
    ...,
    {
        "type"                  : "package",
        "package"               : {
            "name"                  : "andy87/yii2-generator",
            "version"               : "1.1.3",
            "source"                : {
                "type"                  : "git",
                "reference"             : "master",
                "url"                   : "https://github.com/andy87/yii2-generator"
            },
            "autoload": {
                "psr-4": {
                    "andy87\\yii2_generator\\" : "src",
                    "andy87\\yii2_generator\\controllers\\" : "src/controllers"
                }
            }
        }
    }
]
```

## Log
* ***1.1.3*** 
  * Обновил описание
  * Добавил пакетную генерацию(из коробки)
