<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models\traits;

use Craft;
use craft\validators\HandleValidator;
use flipbox\spark\helpers\ModelHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
trait ModelWithHandle
{

    /**
     * @var string Handle
     */
    public $handle;

    /**
     * @inheritdoc
     */
    public function handleRules()
    {

        return [
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
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];

    }

    /**
     * @inheritdoc
     */
    public function handleAttributeLabel()
    {

        return [
            'handle' => Craft::t('app', 'Handle')
        ];

    }

}
