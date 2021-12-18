<?php

//namespace app\commands; // Basic
namespace console\controllers; // Advanced

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
    /** @var string[] Список таблиц учавствующих в генерации */
    const TABLE_LIST = [
        'user',
        'order',
        'product'
        //...
    ];

    /**
     * Генерация одной модели
     */
    public function actionModels( string $tableName )
    {
        /** Advanced */
        $this->generateModel( 'common\\models', $tableName );
        //Аналогичная запись без хардкода
        $this->generateModel( static::DEFAULT_NS_MODELS, $tableName ); // Advanced

        /** Basic */
        $this->generateModel( 'app\\models', $tableName );
    }

    /**
     * Генерация одного круд'а (CRUD)
     */
    public function actionCrud( string $tableName )
    {
        $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
        $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

        $this->generateCrud(
            static::DEFAULT_NS_MODELS . '\\' . "{$tableNameCamelCase}",
            "backend\\controllers\\{$tableNameCamelCase}Controller",
            "@backend/views/{$tableNameKebabCase}"
        );
    }
}