<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\objects\ObjectWithId;
use flipbox\spark\Records\Record;
use flipbox\spark\records\RecordWithId;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
abstract class ObjectById extends Object
{

    use traits\ObjectById;

    /*******************************************
     * FIND/GET
     *******************************************/

    /**
     * @inheritdoc
     */
    public function find($identifier, string $toScenario = null)
    {

        if ($object = parent::find($identifier, $toScenario)) {
            return $object;
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

        if ($object = parent::findCache($identifier)) {
            return $object;
        }

        if (!is_numeric($identifier)) {
            return null;
        }

        return $this->findCacheById($identifier);
    }

    /**
     * @inheritdoc
     */
    public function findCacheByRecord(Record $record)
    {

        if ($object = parent::findCacheByRecord($record)) {
            return $object;
        }

        if (!$record instanceof RecordWithId) {
            return null;
        }

        // Check if already in cache by id
        return $this->findCacheById($record->id);
    }

    /**
     * @inheritdoc
     */
    public function addToCache(BaseObject $object)
    {

        parent::addToCache($object);

        if ($object instanceof ObjectWithId) {
            $this->cacheById($object);
        }

        return $this;
    }
}
