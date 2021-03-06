<?php

//namespace app\commands; // Basic
namespace console\controllers; // Advanced

use yii\{
    helpers\Inflector,
    base\InvalidConfigException
};
use andy87\yii2_generator\components\Generator;

/**
 *  Class `GenerateController`
 *
 *  Вариант с таблицами в константе
 *
 * @package console\controllers
 */
class GenerateController extends \andy87\yii2_generator\controllers\GeneratorController
{
    // Constants

    /** @var string[] Список таблиц учавствующих в генерации */
    const TABLE_LIST = [
        'user',
        'order',
        'product'
        //...
    ];


    /**
     *
     */
    public function init()
    {
        parent::init();

        // Список того что надо заменить в нгенерируемом файле
        $this->generator->replace['from'] = [
            '::className()',
            'public static function tableName()',
            'public function rules()',
            'public function attributeLabels()',
        ];

        // Список того на что надо заменить в нгенерируемом файле
        $this->generator->replace['to'] = [
            '::class',
            'public static function tableName(): string',
            'public function rules(): array',
            'public function attributeLabels(): array',
        ];
    }

    // Methods

    /**
     * Генерация одной Model
     *
     * php yii generate/model
     *
     * @throws InvalidConfigException
     */
    public function actionModel( string $tableName )
    {
        /** Advanced */
        $this->generator->generateModel( 'common\\models', $tableName );
        //Аналогичная запись без хардкода
        $this->generator->generateModel( Generator::DEFAULT_NS_MODELS, $tableName );

        /** Basic */
        $this->generator->generateModel( 'app\\models', $tableName );
    }

    /**
     * Генерация одного CRUD
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
     * Пакетная генерация Model's
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
            ( $baseModelClass = CommonClass::class ) // Default: ActiveRecord::class
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
            ( $baseViewPath = '@backend/views/custom/path/' ), // Default: @backend/views
            ( $nameSpaceControllerClass = 'backend\\controllers\\custom\\dir' ), // Default: @backend/views
            ( $baseControllerClass = \backend\components\controllers\WebController::class ) // Default: yii\web\Controller::class;
        );
    }

}