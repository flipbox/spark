<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\records\Record;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
trait Model
{

    use Object;

    /*******************************************
     * Model -to- Record
     *******************************************/

    /**
     * @param BaseModel $model
     * @return Record
     */
    abstract public function findRecordByModel(BaseModel $model);

    /**
     * @param BaseModel $model
     * @param Record $record
     * @param bool $mirrorScenario
     * @return void
     */
    public function transferToRecord(BaseModel $model, Record $record, bool $mirrorScenario = true)
    {

        if ($mirrorScenario === true) {

            // Mirror scenarios
            $record->setScenario($model->getScenario());

        }

        // Transfer attributes
        $record->setAttributes($model->toArray());

    }

    /**
     * @param BaseModel $model
     * @param bool $mirrorScenario
     * @return Record
     */
    public function toRecord(BaseModel $model, bool $mirrorScenario = true): Record
    {

        if (!$record = $this->findRecordByModel($model)) {

            // Create new record
            $record = $this->createRecord();

        }

        // Populate the record attributes
        $this->transferToRecord($model, $record, $mirrorScenario);

        return $record;

    }

}
