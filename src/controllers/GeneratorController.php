<?php

namespace andy87\yii2_generator\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\gii\generators\{
    model\Generator as GeneratorModel,
    crud\Generator as GeneratorCrud
};
use yii\helpers\Inflector;

/**
 *  Class `GeneratorController`
 *
 * Класс для консольной генерации моделей и крудов
 *
 */
abstract class GeneratorController extends Controller
{
    // Constants

    /** @var bool Вывод прогресса генерации */
    const IS_DISPLAY_INFO = true;

    /** @var string Данные для генерации по умолчанию */
    const DEFAULT_NS_MODELS              = "common\\models";
    const DEFAULT_CRUD_NS_SEARCH_MODELS  = "common\\models\\search";
    const DEFAULT_CRUD_NS_CONTROLLER     = "backend\\controllers";
    const DEFAULT_CRUD_VIEW_BASE_PATH    = "@backend/views";
    const DEFAULT_CRUD_PARENT_CONTROLLER = yii\web\Controller::class;



    // Methods

    /**
     * Пакетная генерация моделей по таблицам в массиве
     *
     * @param string[] $tableNameList массив таблиц для которых будут сгенерированы модели
     * @param string $ns namespace генерируемой модели
     * @param ?string $modelClass (опционально) Имя класса/модели
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function generateModelArray( array $tableNameList, string $ns, ?string $modelClass = null  )
    {
        foreach ( $tableNameList as $tableName )
        {
            $this->generateModel( $ns, $tableName, $modelClass );
        }
    }

    /**
     * Пакетная генерация крудов по таблицам в массиве
     *
     * @param string[] $tableNameList массив таблиц для которых будут сгенерированы круды(crud)
     * @param string $nameSpaceModelClass namespace класса/модели для которого генерируется CRUD
     * @param string $nameSpaceSearchModelClass namespace класса для модели реализующей поиск сущностей в таблице
     * @param string $baseViewPath базовый путь для дирректории с шаблонами
     * @param string $nameSpaceControllerClass namespace класса для генерируемого контроллера круда
     * @param string $baseControllerClass Полный путь Родительского класса для генерируемого контроллера круда
     *
     * @return void
     * @throws InvalidConfigException
     */
    public function generateCrudArray(
        array $tableNameList,
        string $nameSpaceModelClass = self::DEFAULT_NS_MODELS,
        string $nameSpaceSearchModelClass = self::DEFAULT_CRUD_NS_SEARCH_MODELS,
        string $baseViewPath = self::DEFAULT_CRUD_VIEW_BASE_PATH,
        string $nameSpaceControllerClass = self::DEFAULT_CRUD_NS_CONTROLLER,
        string $baseControllerClass = self::DEFAULT_CRUD_PARENT_CONTROLLER
    ): void
    {
        foreach ( $tableNameList as $tableName )
        {
            $this->generateCrudByTable(
                $tableName,
                $nameSpaceModelClass,
                $nameSpaceSearchModelClass,
                $baseViewPath,
                $nameSpaceControllerClass,
                $baseControllerClass
            );
        }
    }


    /**
     * Генерация модели
     *
     * @param string $ns namespace генерируемой модели
     * @param string $tableName имя таблицы для которой генерируется модель
     * @param ?string $modelClass (опционально) Имя класса/модели
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    protected function generateModel( string $ns, string $tableName, ?string $modelClass = null ): void
    {
        /** @var GeneratorModel $generator */
        $generator = Yii::createObject(['class' => GeneratorModel::class ]);

        $generator->ns = $ns;
        $generator->tableName = $tableName;

        if ( $modelClass ) $generator->modelClass = $modelClass;

        $this->displayIndo("\r\nGenerate `model` for table `$tableName` : ");

        $this->processGenerate( $generator->generate() );
    }

    /**
     * Генерация круда
     *
     * @param string $modelClass  Имя класса/модели для которого генерируется CRUD
     * @param string $searchModelClass Полный путь класса для модели реализующей поиск сущностей в таблице
     * @param string $viewPath путь для генерирования шаблонов
     * @param string $controllerClass Полный путь класса для генерируемого контроллера
     * @param string $baseControllerClass Полный путь Родительского класса для генерируемого
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    protected function generateCrud(
        string $modelClass,
        string $searchModelClass,
        string $viewPath,
        string $controllerClass,
        string $baseControllerClass = self::DEFAULT_CRUD_PARENT_CONTROLLER
    ): void
    {
        /** @var GeneratorCrud $generator */
        $generator = Yii::createObject(['class' => GeneratorCrud::class ]);

        $generator->modelClass = $modelClass;
        $generator->searchModelClass = $searchModelClass;
        $generator->controllerClass = $controllerClass;
        $generator->viewPath = $viewPath;
        $generator->baseControllerClass = $baseControllerClass;

        $this->displayIndo("\r\nGenerate `crud` for modelClass `$modelClass` : ");

        $this->processGenerate( $generator->generate() );
    }

    /**
     * @param string $tableName название таблицы для которой будет сгенерирован круд(crud)
     * @param string $nameSpaceModelClass namespace класса/модели для которого генерируется CRUD
     * @param string $nameSpaceSearchModelClass namespace класса для модели реализующей поиск сущностей в таблице
     * @param string $baseViewPath базовый путь для дирректории с шаблонами
     * @param string $nameSpaceControllerClass namespace класса для генерируемого контроллера круда
     * @param string $baseControllerClass Полный путь Родительского класса для генерируемого контроллера круда
     *
     * @return void
     * @throws InvalidConfigException
     */
    protected function generateCrudByTable(
        string $tableName,
        string $nameSpaceModelClass = self::DEFAULT_NS_MODELS,
        string $nameSpaceSearchModelClass = self::DEFAULT_CRUD_NS_SEARCH_MODELS,
        string $baseViewPath = self::DEFAULT_CRUD_VIEW_BASE_PATH,
        string $nameSpaceControllerClass = self::DEFAULT_CRUD_NS_CONTROLLER,
        string $baseControllerClass = self::DEFAULT_CRUD_PARENT_CONTROLLER
    )
    {
        $tableNameCamelCase = Inflector::id2camel( $tableName, '_' );
        $tableNameKebabCase = Inflector::camel2id( $tableNameCamelCase );

        $this->generateCrud(
            "$nameSpaceModelClass\\$tableNameCamelCase",
            "$nameSpaceSearchModelClass\\{$tableNameCamelCase}Search",
            "$baseViewPath/$tableNameKebabCase",
            "$nameSpaceControllerClass\\{$tableNameCamelCase}Controller",
            $baseControllerClass
        );
    }


    /**
     * Генерация файлов и уведомление в консоль о результате
     *
     * @param array $files Список файлов для генерации
     */
    protected function processGenerate(array $files )
    {
        foreach ( $files as $file ) {
            $this->displayIndo( ( ( $file->save() ) ? ' + OK ' : ' - ERROR ' ) . $file->path );
        }
    }

    /**
     * Вывод текста в конволь.
     * при static::INFO === TRUE
     *
     * @param string $text
     */
    protected function displayIndo( string $text ): void
    {
        if ( static::IS_DISPLAY_INFO === true ) echo PHP_EOL . $text;
    }
}