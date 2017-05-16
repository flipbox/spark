<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\Records\Record;
use yii\base\Object as BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.1.0
 */
abstract class ObjectByIdOrString extends ObjectById
{

    use traits\ObjectByString;

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
    public function addToCache(BaseObject $object)
    {

        parent::addToCache($object);

        $this->cacheByString($object);

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

}