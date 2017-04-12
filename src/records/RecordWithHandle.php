<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records;

use craft\validators\HandleValidator;
use flipbox\spark\helpers\RecordHelper;

/**
 * @property string $handle
 *
 * @package flipbox\spark\records
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class RecordWithHandle extends Record
{

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
                    HandleValidator::class,
                    'reservedWords' => [
                        'id',
                        'uid',
                    ]
                ],
                [
                    [
                        'handle'
                    ],
                    'unique'
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
                    'max' => 150
                ],
                [
                    [
                        'handle'
                    ],
                    'safe',
                    'on' => [
                        RecordHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );

    }

}

