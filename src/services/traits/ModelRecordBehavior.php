<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\behaviors\ModelRecordAccessor as ModelRecordAccessorBehavior;
use flipbox\spark\models\Model;
use flipbox\spark\records\Record;

/**
 * @method void transferToRecord(Model $model, Record $record, bool $mirrorScenario = true)
 * @method Record toRecord(Model $model, bool $mirrorScenario = true)
 *
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ModelRecordBehavior
{

    use ObjectRecordBehavior;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'record' => [
                'class' => ModelRecordAccessorBehavior::class,
                'record' => static::recordClass()
            ]
        ];
    }
}