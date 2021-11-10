<?php

namespace andy87\yii2_generator\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\gii\generators\{
    model\Generator as GeneratorModel,
    crud\Generator as GeneratorCrud
};

/**
 *  Class `GeneratorController`
 *
 * Класс для консольной генерации моделей и крудов
 *
 */
abstract class GeneratorController extends Controller
{
    /** @var bool Отключение уведомлений */
    const INFO = true;

    /** @var string Полный путь родительского класса для генерируемого CRUD контроллера используемого по умолчанию */
    const DEFAULT_CRUD_BASE_CONTROLLER = 'yii\web\Controller';

    /**
     * Генерация моделей
     *
     * @param string $ns namespace генерируемой модели
     * @param string $tableName имя таблицы для которой генерируется модель
     * @param ?string $modelClass (опционально) Имя класса/модели
     * @return void
     * @throws InvalidConfigException
     */
    protected function generateModel( string $ns, string $tableName, ?string $modelClass = null ): void
    {
        /** @var GeneratorModel $generator */
        $generator = Yii::createObject(['class' => GeneratorModel::class ]);

        $generator->ns = $ns;
        $generator->tableName = $tableName;

        if ( $modelClass ) $generator->modelClass = $modelClass;

        $this->displayIndo("\r\nGenerate `model` for table `{$tableName}` : ");

        $this->processGenerate( $generator->generate() );
    }

    /**
     * Генерация крудов
     *
     * @param string $modelClass  Имя класса/модели для которого генерируется CRUD
     * @param string $controllerClass Полный путь класса для генерируемого контроллера
     * @param string $viewPath путь для генерирования шаблонов
     * @param string $baseControllerClass Полный путь Родительского класса для генерируемого
     * @return void
     * @throws InvalidConfigException
     */
    protected function generateCrud(
        string $modelClass,
        string $controllerClass,
        string $viewPath,
        string $baseControllerClass = self::DEFAULT_CRUD_BASE_CONTROLLER
    ): void
    {
        /** @var GeneratorCrud $generator */
        $generator = Yii::createObject(['class' => GeneratorCrud::class ]);

        $generator->modelClass = $modelClass;
        $generator->controllerClass = $controllerClass;
        $generator->viewPath = $viewPath;
        $generator->baseControllerClass = $baseControllerClass;

        $this->displayIndo("\r\nGenerate `crud` for modelClass `{$modelClass}` : ");

        $this->processGenerate( $generator->generate() );
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
        if ( static::INFO === true ) echo PHP_EOL . $text;
    }
}