<?php

namespace andy87\yii2_generator\controllers;

use andy87\yii2_generator\components\Generator;
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

        $this->generator = Yii::createObject(['class' => Generator::class, static::DISPLAY_INFO ]);
    }
}