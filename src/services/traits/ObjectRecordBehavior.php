<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\behaviors\ObjectRecordAccessor as ObjectRecordAccessorBehavior;
use flipbox\spark\records\Record;
use yii\db\ActiveQuery;

/**
 * @method ActiveQuery getRecordQuery($config = [])
 * @method Record createRecord(array $attributes = [], string $toScenario = null)
 * @method Record findRecordByCondition($condition, string $toScenario = null)
 * @method Record findRecordByCriteria($criteria, string $toScenario = null)
 * @method Record getRecordByCondition($condition, string $toScenario = null)
 * @method Record getRecordByCriteria($criteria, string $toScenario = null)
 * @method Record[] findAllRecords(string $toScenario = null)
 * @method Record[] findAllRecordsByCondition($condition, string $toScenario = null)
 * @method Record[] findAllRecordsByCriteria($criteria, string $toScenario = null)
 * @method Record[] getAllRecords(string $toScenario = null)
 * @method Record[] getAllRecordsByCondition($condition, string $toScenario = null)
 * @method Record[] getAllRecordsByCriteria($criteria, string $toScenario = null)
 *
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ObjectRecordBehavior
{

    /**
     * @return string
     */
    public abstract static function recordClass(): string;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'record' => [
                'class' => ObjectRecordAccessorBehavior::class,
                'record' => static::recordClass()
            ]
        ];
    }
}