<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\web\Controller;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\JsonParser;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Craft::$app->getRequest()->parsers = array_merge(
            Craft::$app->getRequest()->parsers,
            [
                'application/json' => JsonParser::class
            ]
        );
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verbFilter' => [
                    'class' => VerbFilter::class,
                    'actions' => $this->verbs(),
                ],
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                        'application/xml' => Response::FORMAT_XML,
                        'text/html' => Response::FORMAT_RAW
                    ]
                ]
            ]
        );
    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        return [];
    }
}
