<?php

namespace andy87\yii2_generator\controllers;

use Yii;
use yii\{
    helpers\Inflector,
    base\InvalidConfigException,
    console\Controller
};
use yii\gii\generators\{
    model\Generator as GeneratorModel,
    crud\Generator as GeneratorCrud
};
use andy87\yii2_generator\components\Generator;

/**
 *  Class `GeneratorController`
 *
 * Класс для консольной генерации моделей и крудов
 *
 */
abstract class GeneratorController extends Controller
{
    const DISPLAY_INFO = true;

    // Property

    /** @var Generator $generator */
    protected Generator $generator;



    // Methods

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->generator = Yii::createObject( Generator::class,[ self::DISPLAY_INFO ]);

    }
}