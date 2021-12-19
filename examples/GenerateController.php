<?php

//namespace app\commands; // Basic
namespace console\controllers; // Advanced

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
     * php yii generate/models
     *
     * @throws InvalidConfigException
     */
    public function actionModels( string $tableName )
    {
        /** Advanced */
        $this->generateModel( 'common\\models', $tableName );
        //Аналогичная запись без хардкода
        $this->generateModel( static::DEFAULT_NS_MODELS, $tableName );

        /** Basic */
        $this->generateModel( 'app\\models', $tableName );
    }

    /**
     * Генерация одного круд'а (CRUD)
     *
     * php yii generate/cruds
     *
     * @throws InvalidConfigException
     */
    public function actionCrud( string $tableName )
    {
        $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
        $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

        $this->generateCrud(
            static::DEFAULT_NS_MODELS . '\\' . $tableNameCamelCase,
            "common\\models\\search\\{$tableNameCamelCase}Search",
            "@backend/views/$tableNameKebabCase",
            "backend\\controllers\\{$tableNameCamelCase}Controller",
            \backend\components\controllers\WebController::class  // Default: \yii\web\Controller::class
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
        $this->generateCrudArray( self::TABLE_LIST );

        //Generate with custom params
        $this->generateCrudArray(
            self::TABLE_LIST,
            ( $nameSpaceModelClass = 'common\\models\\custom\\folder' ), // Default: common\\models
            ( $nameSpaceSearchModelClass = 'common\\models\\custom\\folder\\search' ), // Default: common\\models\\search
            ( $baseViewPath = '@backend/views/custom/path/' ), // Default: backend\\controllers
            ( $nameSpaceControllerClass = 'backend\\controllers\\custom\\dir' ), // Default: @backend/views
            ( $baseControllerClass = \backend\components\controllers\WebController::class ) // Default: yii\web\Controller::class;
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
        $this->generateModelArray( self::TABLE_LIST );

        //Generate with custom params
        $this->generateModelArray(
            self::TABLE_LIST,
            ( $nameSpaceModelClass = 'common\\models\\some\\dir' ) // Default: common\\models
        );
    }

}