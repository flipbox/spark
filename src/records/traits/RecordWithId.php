<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records\traits;

use flipbox\spark\helpers\RecordHelper;

/**
 * @property int $id
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
trait RecordWithId
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @inheritdoc
     */
    protected function idRules()
    {

        return [
            [
                [
                    'id'
                ],
                'number',
                'integerOnly' => true
            ],
            [
                [
                    'id'
                ],
                'safe',
                'on' => [
                    RecordHelper::SCENARIO_DEFAULT
                ]
            ]
        ];

    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

}
