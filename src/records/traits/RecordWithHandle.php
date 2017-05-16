<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records\traits;

use craft\validators\HandleValidator;
use flipbox\spark\helpers\RecordHelper;

/**
 * @property string $handle
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
trait RecordWithHandle
{

    /**
     * @var array
     */
    protected $reservedHandleWords = [
        'id',
        'uid',
    ];

    /**
     * @var int
     */
    protected $handleLength = 150;

    /**
     * @inheritdoc
     */
    protected function handleRules()
    {

        return [
            [
                [
                    'handle'
                ],
                HandleValidator::class,
                'reservedWords' => $this->reservedHandleWords
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
                'max' => $this->handleLength
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
        ];

    }

}
