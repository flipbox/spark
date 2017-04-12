<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\behaviors;

use flipbox\spark\models\Model;
use flipbox\spark\records\Record;

/**
 * @package flipbox\spark\behaviors
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ModelRecordAccessor extends ObjectRecordAccessor
{

    /*******************************************
     * Model -to- Record
     *******************************************/

    /**
     * @param Model $model
     * @param Record $record
     * @param bool $mirrorScenario
     * @return void
     */
    public function transferToRecord(Model $model, Record $record, bool $mirrorScenario = true)
    {

        if ($mirrorScenario === true) {

            // Mirror scenarios
            $record->setScenario($model->getScenario());

        }

        // Transfer attributes
        $record->setAttributes($model->toArray());

    }

    /**
     * @param Model $model
     * @param bool $mirrorScenario
     * @return Record
     */
    public function toRecord(Model $model, bool $mirrorScenario = true): Record
    {

        if ($id = $model->id) {
            $record = $this->findRecordByCondition(
                ['id' => $id]
            );
        }

        if (empty($record)) {

            // Create new record
            $record = $this->createRecord();

        }

        // Populate the record attributes
        $this->transferToRecord($model, $record, $mirrorScenario);

        return $record;

    }

}
