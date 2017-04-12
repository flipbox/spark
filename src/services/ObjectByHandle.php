<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\exceptions\ObjectNotFoundException;
use flipbox\spark\objects\Object as BaseObject;
use flipbox\spark\objects\ObjectWithHandle;
use flipbox\spark\records\Record;
use flipbox\spark\records\RecordWithHandle;

/**
 * @package flipbox\spark\services
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ObjectByHandle extends Object
{

    /**
     * @var ObjectWithHandle[]
     */
    protected $_cacheByHandle = [];

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseObject|ObjectWithHandle|null
     */
    public function find($identifier, string $toScenario = null)
    {

        if (is_string($identifier)) {

            return $this->findByHandle($identifier, $toScenario);

        }

        return parent::find($identifier, $toScenario);

    }

    /*******************************************
     * FIND/GET BY HANDLE
     *******************************************/

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ObjectWithHandle|null
     */
    public function findByHandle(string $handle, string $toScenario = null)
    {

        // Check cache
        if (!$object = $this->findCacheByHandle($handle)) {

            // Find record in db
            if ($record = $this->findRecordByHandle($handle)) {

                /** @var ObjectWithHandle $object */
                $object = $this->findByRecord($record, $toScenario);

            } else {

                $this->_cacheByHandle[$handle] = null;

                return null;

            }

        }

        return $object;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ObjectWithHandle|null
     * @throws ObjectNotFoundException
     */
    public function getByHandle(string $handle, string $toScenario = null): ObjectWithHandle
    {

        if (!$object = $this->findByHandle($handle, $toScenario)) {

            $this->notFoundByHandleException($handle);

        }

        return $object;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ObjectWithHandle|null
     */
    public function freshFindByHandle(string $handle, string $toScenario = null)
    {

        // Find record in db
        if (!$record = $this->findRecordByHandle($handle)) {
            return null;
        }

        /** @var ObjectWithHandle $object */
        $object = $this->createFromRecord($record, $toScenario);

        return $object;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ObjectWithHandle
     * @throws ObjectNotFoundException
     */
    public function freshGetByHandle(string $handle, string $toScenario = null): ObjectWithHandle
    {

        if (!$object = $this->freshFindByHandle($handle, $toScenario)) {

            $this->notFoundByHandleException($handle);

        }

        return $object;

    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @inheritdoc
     * @return BaseObject|ObjectWithHandle|null
     */
    public function findCache($identifier)
    {

        if (is_string($identifier)) {

            return $this->findCacheByHandle($identifier);

        }

        return parent::findCache($identifier);

    }

    /**
     * @inheritdoc
     */
    public function addToCache(BaseObject $object)
    {

        if ($object instanceof ObjectWithHandle) {

            $this->cacheByHandle($object);

        }

        return parent::addToCache($object);

    }

    /**
     * Find an existing cache by handle
     *
     * @param string $handle
     * @return null
     */
    public function findCacheByHandle(string $handle)
    {

        // Check if already in addToCache
        if (!$this->isCachedByHandle($handle)) {
            return null;
        }

        return $this->_cacheByHandle[$handle];

    }

    /**
     * Identify whether in cache by handle
     *
     * @param string $handle
     * @return bool
     */
    private function isCachedByHandle(string $handle): bool
    {
        return array_key_exists($handle, $this->_cacheByHandle);
    }

    /**
     * @param ObjectWithHandle $object
     * @return static
     */
    protected function cacheByHandle(ObjectWithHandle $object)
    {

        // Check if already in cache
        if (!$this->isCachedByHandle($object->handle)) {

            // Cache it
            $this->_cacheByHandle[$object->handle] = $object;

        }

        return $this;

    }

    /**
     * @param Record $record
     * @return BaseObject|ObjectWithHandle|null
     */
    public function findCacheByRecord(Record $record)
    {

        if ($record instanceof RecordWithHandle) {

            // Check if already in cache by id
            if (!$this->isCachedByHandle($record->handle)) {

                return $this->findCacheByHandle($record->handle);

            }

        }

        return parent::findCacheByRecord($record);

    }

    /*******************************************
     * RECORD BY HANDLE
     *******************************************/

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return Record|RecordWithHandle|null
     */
    protected function findRecordByHandle(string $handle, string $toScenario = null)
    {

        return $this->findRecordByCondition(
            [
                'handle' => $handle
            ],
            $toScenario
        );

    }

    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @param string|null $handle
     * @throws ObjectNotFoundException
     */
    protected function notFoundByHandleException(string $handle = null)
    {

        throw new ObjectNotFoundException(
            sprintf(
                'Object does not exist with the handle "%s".',
                (string)$handle
            )
        );

    }

}
