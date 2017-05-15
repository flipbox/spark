<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use Craft;
use craft\events\ModelEvent;
use flipbox\spark\helpers\RecordHelper;
use flipbox\spark\models\Model;
use flipbox\spark\records\Record;

/**
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ModelSave
{

    /*******************************************
     * ABSTRACTS
     *******************************************/

    /**
     * @param Model $model
     * @param bool $mirrorScenario
     * @return Record
     */
    abstract protected function toRecord(Model $model, bool $mirrorScenario = true): Record;


    /*******************************************
     * SAVE
     *******************************************/

    /**
     * @param Model $model
     * @param bool $runValidation
     * @param null $attributes
     * @param bool $mirrorScenario
     * @return bool
     * @throws \Exception
     */
    public function save(Model $model, bool $runValidation = true, $attributes = null, bool $mirrorScenario = true)
    {

        // Validate
        if ($runValidation && !$model->validate($attributes)) {
            Craft::info('Model not saved due to validation error.', __METHOD__);
            return false;
        }

        $isNew = (null === $model->id);

        // a 'beforeSave' event
        if(!$this->beforeSave($model, $isNew)) {
            return false;
        }

        // Create event
        $event = new ModelEvent([
            'isNew' => $isNew
        ]);

        // Db transaction
        $transaction = RecordHelper::beginTransaction();

        try {

            // The 'before' event
            if (!$model->beforeSave($event)) {

                $transaction->rollBack();

                return false;
            }

            $record = $this->toRecord($model, $mirrorScenario);

            // Validate
            if (!$record->validate($attributes)) {

                $model->addErrors($record->getErrors());

                $transaction->rollBack();

                return false;

            }

            // Insert record
            if (!$record->save($attributes)) {

                // Transfer errors to model
                $model->addErrors($record->getErrors());

                $transaction->rollBack();

                return false;

            }

            // Transfer record to model
            if ($event->isNew) {
                $model->id = $record->id;
                $model->dateCreated = $record->dateCreated;
                $model->uid = $record->uid;
            }
            $model->dateUpdated = $record->dateUpdated;


            // The 'after' event
            if (!$model->afterSave($event)) {

                $transaction->rollBack();

                return false;

            }

        } catch (\Exception $e) {

            $transaction->rollBack();

            throw $e;

        }

        $transaction->commit();

        // an 'afterSave' event
        $this->afterSave($model, $isNew);

        return true;

    }

    /**
     * @param Model $model
     * @param bool $isNew
     * @return bool
     */
    protected function beforeSave(Model $model, bool $isNew): bool
    {
        return true;
    }

    /**
     * @param Model $model
     * @param bool $isNew
     */
    protected function afterSave(Model $model, bool $isNew)
    {

        Craft::info(sprintf(
            "Model '%s' with ID '%s' was saved successfully.",
            (string) get_class($model),
            (string) $model->id
        ), __METHOD__);

    }

}
