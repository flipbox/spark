<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models\traits;

use Craft;
use flipbox\spark\helpers\ModelHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
trait ModelWithId
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
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];

    }

    /**
     * @inheritdoc
     */
    protected function idAttributeLabel()
    {

        return [
            'id' => Craft::t('app', 'Id')
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
