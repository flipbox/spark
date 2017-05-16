<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\records\Record;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
abstract class ModelByString extends Model
{

    use traits\ModelByString;

    /**
     * @inheritdoc
     */
    public function find($identifier, string $toScenario = null)
    {

        if ($model = parent::find($identifier, $toScenario)) {
            return $model;
        }

        if (!is_string($identifier)) {
            return null;
        }

        return $this->findByString($identifier, $toScenario);

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

        if (!is_string($identifier)) {
            return null;
        }

        return $this->findCacheByString($identifier);

    }

    /**
     * @inheritdoc
     */
    public function addToCache(BaseModel $model)
    {

        parent::addToCache($model);

        $this->cacheByString($model);

        return $this;

    }


    /**
     * @inheritdoc
     */
    public function findCacheByRecord(Record $record)
    {

        if ($model = parent::findCacheByRecord($record)) {
            return $model;
        }

        return $this->findCacheByRecordByString($record);

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

        $stringValue = $this->stringValue($model);

        if ($stringValue === null) {
            return null;
        }

        return $this->findRecordByString($stringValue);

    }

    /**
     * @inheritdoc
     */
    public function toRecord(BaseModel $model, bool $mirrorScenario = true): Record
    {

        if ($record = $this->toRecordByString($model, $mirrorScenario)) {
            return $record;
        }

        return parent::toRecord($model, $mirrorScenario);

    }

}
