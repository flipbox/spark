<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use flipbox\spark\exceptions\ModelNotFoundException;
use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\models\ModelWithHandle;
use flipbox\spark\records\Record;
use flipbox\spark\records\RecordWithHandle;

/**
 * @package flipbox\spark\services
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ModelByHandle extends Model
{

    /**
     * @var ModelWithHandle[]
     */
    protected $_cacheByHandle = [];

    /**
     * @return string
     */
    public static function modelClassInstance(): string
    {
        return ModelWithHandle::class;
    }

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseModel|ModelWithHandle|null
     */
    public function find($identifier, string $toScenario = null)
    {

        if($model = parent::find($identifier, $toScenario)) {
            return $model;
        }

        if(!is_string($identifier)) {
            return null;
        }

        return $this->findByHandle($identifier, $toScenario);

    }


    /*******************************************
     * FIND/GET BY HANDLE
     *******************************************/

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ModelWithHandle|null
     */
    public function findByHandle(string $handle, string $toScenario = null)
    {

        // Check cache
        if (!$model = $this->findCacheByHandle($handle)) {

            // Find record in db
            if ($record = $this->findRecordByHandle($handle)) {

                /** @var ModelWithHandle $model */
                $model = $this->findByRecord($record, $toScenario);

            } else {

                $this->_cacheByHandle[$handle] = null;

                return null;

            }

        }

        return $model;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ModelWithHandle|null
     * @throws ModelNotFoundException
     */
    public function getByHandle(string $handle, string $toScenario = null): ModelWithHandle
    {

        if (!$model = $this->findByHandle($handle, $toScenario)) {

            $this->notFoundByHandleException($handle);

        }

        return $model;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ModelWithHandle|null
     */
    public function freshFindByHandle(string $handle, string $toScenario = null)
    {

        // Find record in db
        if (!$record = $this->findRecordByHandle($handle)) {
            return null;
        }

        /** @var ModelWithHandle $model */
        $model = $this->create($record, $toScenario);

        return $model;

    }

    /**
     * @param string $handle
     * @param string|null $toScenario
     * @return ModelWithHandle
     * @throws ModelNotFoundException
     */
    public function freshGetByHandle(string $handle, string $toScenario = null): ModelWithHandle
    {

        if (!$model = $this->freshFindByHandle($handle, $toScenario)) {

            $this->notFoundByHandleException($handle);

        }

        return $model;

    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @inheritdoc
     * @return BaseModel|ModelWithHandle|null
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
    public function addToCache(BaseModel $model)
    {

        if ($model instanceof ModelWithHandle) {

            $this->cacheByHandle($model);

        }

        return parent::addToCache($model);

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
     * @param ModelWithHandle $model
     * @return static
     */
    protected function cacheByHandle(ModelWithHandle $model)
    {

        // Check if already in cache
        if (!$this->isCachedByHandle($model->handle)) {

            // Cache it
            $this->_cacheByHandle[$model->handle] = $model;

        }

        return $this;

    }

    /**
     * @param Record $record
     * @return BaseModel|ModelWithHandle|null
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


    /**
     * @param BaseModel $model
     * @param bool $mirrorScenario
     * @return RecordWithHandle|Record
     */
    public function toRecord(BaseModel $model, bool $mirrorScenario = true): Record
    {

        // Get existing record
        if ($model instanceof ModelWithHandle) {

            if ($record = $this->getRecordByCondition([
                'handle' => $model->handle
            ])
            ) {

                // Populate the record attributes
                $this->transferToRecord($model, $record, $mirrorScenario);

                return $record;

            }

        }

        return parent::toRecord($model, $mirrorScenario);

    }

    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @param string|null $handle
     * @throws ModelNotFoundException
     */
    protected function notFoundByHandleException(string $handle = null)
    {

        throw new ModelNotFoundException(
            sprintf(
                'Model does not exist with the handle "%s".',
                (string)$handle
            )
        );

    }

}
