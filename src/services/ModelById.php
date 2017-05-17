<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\models\ModelWithId;
use flipbox\spark\records\Record;
use flipbox\spark\records\RecordWithId;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ModelById extends Model
{

    use traits\ModelById;

    /*******************************************
     * FIND/GET
     *******************************************/

    /**
     * @inheritdoc
     */
    public function find($identifier, string $toScenario = null)
    {

        if ($model = parent::find($identifier, $toScenario)) {
            return $model;
        }

        if (!is_numeric($identifier)) {
            return null;
        }

        return $this->findById($identifier, $toScenario);

    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function findCache($identifier)
    {

        if ($model = parent::findCache($identifier)) {
            return $model;
        }

        if (empty($identifier) || !is_numeric($identifier)) {
            return null;
        }

        return $this->findCacheById($identifier);

    }

    /**
     * @inheritdoc
     */
    public function findCacheByRecord(Record $record)
    {

        if ($model = parent::findCacheByRecord($record)) {
            return $model;
        }

        if (!$record instanceof RecordWithId || null === $record->id) {
            return null;
        }

        // Check if already in cache by id
        return $this->findCacheById($record->id);

    }

    /**
     * @inheritdoc
     */
    public function addToCache(BaseModel $model)
    {

        parent::addToCache($model);

        if ($model instanceof ModelWithId) {
            $this->cacheById($model);
        }

        return $this;

    }

    /*******************************************
     * RECORD
     *******************************************/

    /**
     * @param BaseModel $model
     * @return Record|null
     */
    public function findRecordByModel(BaseModel $model)
    {

        if ($model instanceof ModelWithId && null !== $model->id) {
            return $this->findRecordById($model->id);
        }

        return parent::findRecordByModel($model);

    }

}
