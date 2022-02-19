<?php

namespace andy87\yii2_generator\components;

use Yii;
use yii\{gii\CodeFile, helpers\Inflector, base\InvalidConfigException, web\Controller, db\ActiveRecord};
use yii\gii\generators\{
    model\Generator as GeneratorModel,
    crud\Generator as GeneratorCrud
};

/**
 *  Class `Generator`
 *
 * @package andy87\yii2_generator\components
 */
class Generator
{
    // Constants

    /** @var string Данные для генерации по умолчанию */
    const DEFAULT_NS_MODELS             = "common\\models";
    const DEFAULT_NS_CRUD_SEARCH_MODELS = "common\\models\\search";
    const DEFAULT_NS_CRUD_CONTROLLER    = "backend\\controllers";

    const DEFAULT_VIEW_CRUD_PATH        = "@backend/views";

    const DEFAULT_CRUD_PARENT_CONTROLLER = Controller::class;
    const DEFAULT_MODEL_PARENT_CLASS     = ActiveRecord::class;

    /** @var array Замена в контенте генерируемого файла */
    public array $replace = [];



    // Params

    /** @var bool $is_display_info Вывод прогресса генерации */
    private bool $is_display_info;



    // Magic

    /**
     * @param bool $is_display_info Вывод прогресса генерации
     */
    public function __construct( bool $is_display_info = false )
    {
        $this->is_display_info = $is_display_info;
    }



    // Methods

    /**
     * Генерация модели
     *
     * @param string $ns namespace генерируемой модели
     * @param string $tableName имя таблицы для которой генерируется модель
     * @param ?string $baseClass (опционально) Родительский класс
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function generateModel( string $ns, string $tableName, ?string $baseClass = self::DEFAULT_MODEL_PARENT_CLASS ): void
    {
        /** @var GeneratorModel $generator */
        $generator = Yii::createObject(['class' => GeneratorModel::class ]);

        $generator->ns = $ns;
        $generator->tableName = $tableName;
        $generator->baseClass = $baseClass;

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
     * @param string $baseControllerClass Полный путь Родительского класса для генерируемого контроллера
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function generateCrud(
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
    public function generateCrudByTable(
        string $tableName,
        string $nameSpaceModelClass = self::DEFAULT_NS_MODELS,
        string $nameSpaceSearchModelClass = self::DEFAULT_NS_CRUD_SEARCH_MODELS,
        string $baseViewPath = self::DEFAULT_VIEW_CRUD_PATH,
        string $nameSpaceControllerClass = self::DEFAULT_NS_CRUD_CONTROLLER,
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
     * Пакетная генерация моделей по таблицам в массиве
     *
     * @param string[] $tableNameList массив таблиц для которых будут сгенерированы модели
     * @param string $ns namespace генерируемой модели
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function generateModelArray( array $tableNameList, string $ns = self::DEFAULT_NS_MODELS, ?string $baseClass = self::DEFAULT_MODEL_PARENT_CLASS  )
    {
        foreach ( $tableNameList as $tableName )
        {
            $this->generateModel( $ns, $tableName, $baseClass );
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
        string $nameSpaceSearchModelClass = self::DEFAULT_NS_CRUD_SEARCH_MODELS,
        string $baseViewPath = self::DEFAULT_VIEW_CRUD_PATH,
        string $nameSpaceControllerClass = self::DEFAULT_NS_CRUD_CONTROLLER,
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
     * Генерация файлов и уведомление в консоль о результате
     *
     * @param CodeFile[] $files Список файлов для генерации
     */
    protected function processGenerate(array $files )
    {
        foreach ( $files as $file )
        {
            if ( !empty($this->replace['from']) && !empty($this->replace['to']) ) {
                $file->content = str_replace( $this->replace['from'], $this->replace['to'], $file->content );
            }

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
        if ( $this->is_display_info ) echo PHP_EOL . $text;
    }

}