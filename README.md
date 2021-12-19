<p align="center">
    <img src="https://raw.githubusercontent.com/andy87/yii2-generator/master/logo.png" alt="Yii2">
    <h1 align="center">yii2-generator</h1>
</p>

Класс для генерации моделей & крудов(crud). С простой настройкой для пакетной генерации.  

<hr>
  
***Решаемые задачи/цели:*** 
* быстрая генерация Models & CRUD для таблиц в массиве  
* возможность кастомизации параметров генерации  

<small>*Мне всегда не нравилось по одной модельке генерировать поэтому было создано "это расширение".*</small>

<hr>

## Пример использования.
### Код
Пример контроллера для выполнения консольных команд(advanced)  
```php

namespace console\controllers;

use common\components\services\ModuleService;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

/**
 *
 */
class GenerateController extends andy87\yii2_generator\controller\GeneratorController
{
    const TABLES = [
        'order',
        'member'
    ];

    public function actionModels()
    {
        foreach ( self::TABLES as $tableName )
        {
            $this->generateModel( 'common\\models', $tableName );
        }
    }

    public function actionCrud()
    {
        foreach ( self::TABLES as $tableName )
        {
            $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
            $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

            $this->generateCrud(
                "common\\models\\{$tableNameCamelCase}",
                "backend\\controllers\\{$tableNameCamelCase}Controller",
                "@backend/views/{$tableNameKebabCase}"
            );
        }
    }
}
```
### Консоль
Далее в консоле выполняются команды:
* php yii generate/models
* php yii generate/crud

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
 * *bool* **INFO** - Статус вывода информации о процессе генерации (по умолчанию: **TRUE** ).  
 * *string* **DEFAULT_CRUD_BASE_CONTROLLER** - Полный путь родительского класса для контроллера используемого в CRUD (по умолчанию: **yii\web\Controller** )

 
в контроллере создаются `actions`, которые вызывающие методы:
* `generateModel()`
* `generateCrud()`  
  
#### generateModel( *string* $ns, *string* $tableName, *?string* $modelClass = null ): void
* *string* **$ns** - `namespace` модели
* *string* **$tableName** - имя таблицы для которой создаётся модель
* *?string* **$modelClass** - имя создаваемой модели (*необязательный*)

для реализации пакетной генерации метод надо обернуть в цикл, пример:
```php
    //TODO: тут должно быть описание использование встроенного метода пакетной генерации
    /** блок кода консольной команды `php yii xxx/models` */
    public function actionModels()
    {
        foreach ( self::TABLES as $tableName )
        {
            /** Advanced */
            $this->generateModel( 'common\\models', $tableName ); 
            //Аналогичная запись без хардкода
            $this->generateModel( static::DEFAULT_CRUD_NS_MODELS, $tableName ); // Advanced
            //Используется статик для возможности переназначить константу

            /** Basic */
            $this->generateModel( 'app\\models', $tableName );
        }
    }
```  
  
#### generateCrud( *string* $modelClass, *string* $searchModelClass, *string* $controllerClass, *string* $viewPath, *string* $baseControllerClass ): void
* *string* **$modelClass** - имя класса/модели для которого генерируется CRUD
* *string* **$searchModelClass** - полный путь класса для модели реализующей поиск сущностей в таблице
* *string* **$viewPath** -  путь для генерирования шаблонов
* *string* **$controllerClass** - полный путь класса для генерируемого контроллера
* *string* **$baseControllerClass** - полный путь родительского класса генерируемого контроллера (*необязательный*, по умолчанию: **yii\web\Controller** )

для реализации пакетной генерации метода надо обернуть в цикл, пример:
```php
    //TODO: тут должно быть описание использование встроенного метода пакетной генерации
    /** Выполнение консольной команды `php yii generator/crud` */
    public function actionCrud()
    {
        foreach ( self::TABLES as $tableName )
        {
            $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
            $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

            $this->generateCrud(
                "common\\models\\{$tableNameCamelCase}",
                "common\\models\\search\\{$tableNameCamelCase}Search",
                "backend\\controllers\\{$tableNameCamelCase}Controller",
                "@backend/views/{$tableNameKebabCase}"
            );
            
            //Аналогичная запись без хардкода
            $this->generateCrud(
                static::DEFAULT_CRUD_NS_MODELS .'\\'. $tableNameCamelCase,
                static::DEFAULT_CRUD_NS_SEARCH_MODELS .'\\'. "{$tableNameCamelCase}Search",
                static::DEFAULT_CRUD_NS_CONTROLLER .'\\'. "{$tableNameCamelCase}Controller",
                static::DEFAULT_CRUD_VIEW_BASE_PATH .'/'. $tableNameKebabCase
            );
            //Используется статик для возможности переназначить константу
        }
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

##Log
* ***1.1.3*** 
  * Обновил описание
  * Добавил пакетную генерацию(из коробки)
