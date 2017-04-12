<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records;

use craft\db\ActiveRecord as BaseRecord;
use craft\validators\DateTimeValidator;
use DateTime;
use flipbox\spark\helpers\RecordHelper;

/**
 * @property int $id
 * @property DateTime $dateCreated
 * @property DateTime $dateUpdated
 * @property string $uid
 *
 * @package flipbox\spark\records
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Record extends BaseRecord
{

    /**
     * The table alias
     */
    const TABLE_ALIAS = '';

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return static::TABLE_ALIAS;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%' . static::tableAlias() . '}}';
    }

    /*******************************************
     * RULES
     *******************************************/

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
                        'id'
                    ],
                    'number',
                    'integerOnly' => true
                ],
                [
                    [
                        'dateCreated',
                        'dateUpdated'
                    ],
                    DateTimeValidator::class
                ],
                [
                    [
                        'id',
                        'uid',
                        'dateCreated',
                        'dateUpdated'
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
