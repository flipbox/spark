<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\elements;

use Craft;
use craft\base\Element;
use craft\validators\HandleValidator;
use flipbox\spark\helpers\ElementHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ElementWithHandle extends Element
{

    /**
     * @var string Handle
     */
    public $handle;

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'handle'
                    ],
                    HandleValidator::class
                ],
                [
                    [
                        'handle'
                    ],
                    'required'
                ],
                [
                    [
                        'handle'
                    ],
                    'string',
                    'max' => 255
                ],
                [
                    [
                        'handle'
                    ],
                    'safe',
                    'on' => [
                        ElementHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return array_merge(
            parent::attributeLabels(),
            [
                'handle' => Craft::t('app', 'Handle')
            ]
        );

    }

}
