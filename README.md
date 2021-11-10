Класс для генерации моделей & крудов(crud). С простой настройкой для пакетной генерации.  
  
Мне всегда не нравилось по одной модельке генерировать поэтому было создано "это расширение".  
  
***Решаемые задачи:*** 
<br> 1. быстрая генерация Models & CRUD для заданных в массиве таблиц
<br> 2. возможность кастомизации параметров генерации

## Код использования.
```php

namespace console\controllers;

use common\components\services\ModuleService;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

/**
 *
 */
class GeneratorController extends andy87\yii2_generator\controller\GeneratorController
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
далее в консоле выполняются команды:
* php yii generator/models
* php yii generator/crud



# Использование.
Создаётся контроллер для выполнения консольных команд, который наследуется от `andy87\yii2_generator\controller\GeneratorController`.

```php
use andy87\yii2_generator\controller\GeneratorController;

class GeneratorController extends GeneratorController
{
    ...
}
```
Имя контроллера и комманд на ваше усмотрение.

константы контроллера:
 * *bool* **INFO** - при значении TRUE методы будут выводить через echo информацию о процессе генерации (по умолчанию: TRUE )
 * *string* **DEFAULT_CRUD_BASE_CONTROLLER** - Полный путь родительского класса для генерируемого CRUD контроллера используемого по умолчанию (по умолчанию: yii\web\Controller )

 
в контроллере создаются `actions`, которые вызывают методы:
* `generateModel()`
* `generateCrud()`  
  
#### generateModel( *string* $ns, *string* $tableName, *?string* $modelClass = null ): void
* *string* **$ns** - **обязательный**, `namespace` модели
* *string* **$tableName** - **обязательный**, имя таблицы для которой создаётся модель
* *?string* **$modelClass** - ***необязательный***, имя создаваемой модели

для реализации пакетной генерации надо вызов метода обернуть в цикл, пример:
```php
    /** Выполнение консольной команды `php yii generator/models` */
    public function actionModels()
    {
        foreach ( self::TABLES as $tableName )
        {
            $this->generateModel( 'common\\models', $tableName );
        }
    }
```  
  
#### generateCrud( *string* $modelClass, *string* $searchModelClass, *string* $controllerClass, *string* $viewPath, *string* $baseControllerClass ): void
* *string* **$modelClass** - **обязательный**, имя класса/модели для которого генерируется CRUD
* *string* **$searchModelClass** - **обязательный**, полный путь класса для модели реализующей поиск сущностей в таблице
* *string* **$controllerClass** - **обязательный**, полный путь класса для генерируемого контроллера
* *string* **$viewPath** - **обязательный**, путь для генерирования шаблонов
* *string* **$baseControllerClass** - ***необязательный***, полный путь родительского класса для генерируемого контроллера

для реализации пакетной генерации надо вызов метода обернуть в цикл, пример:
```php
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
        }
    }
```


# Установка
Установка с помощью [composer](https://getcomposer.org/download/)  

Добавить в `composer.json`  
<small>require</small>
```
"require": {
    ...
    "andy87/yii2-generator" : ">=1.0.2"
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
            "version"               : "1.0.2",
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
