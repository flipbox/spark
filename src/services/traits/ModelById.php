<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\exceptions\ModelNotFoundException;
use flipbox\spark\exceptions\RecordNotFoundException;
use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\models\ModelWithId;
use flipbox\spark\records\Record;
use flipbox\spark\records\RecordWithId;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 *
 * @method RecordWithId|null findRecordByCondition($condition, string $toScenario = null)
 */
trait ModelById
{

    use Model;

    /**
     * @var ModelWithId[]
     */
    protected $_cacheById = [];

    /**
     * @param Record $record
     * @param string|null $toScenario
     * @return BaseModel|ModelWithId
     */
    abstract protected function findByRecord(Record $record, string $toScenario = null): BaseModel;

    /**
     * @param array $config
     * @param string|null $toScenario
     * @return BaseModel|ModelWithId
     */
    abstract public function create($config = [], string $toScenario = null): BaseModel;

    /*******************************************
     * FIND/GET BY ID
     *******************************************/

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return ModelWithId|null
     */
    public function findById(int $id, string $toScenario = null)
    {

        // Check cache
        if (!$model = $this->findCacheById($id)) {

            // Find record in db
            if ($record = $this->findRecordById($id)) {

                $model = $this->findByRecord($record, $toScenario);

            } else {

                $this->_cacheById[$id] = null;

                return null;

            }

        }

        return $model;

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return ModelWithId|null
     * @throws ModelNotFoundException
     */
    public function getById(int $id, string $toScenario = null): ModelWithId
    {

        if (!$model = $this->findById($id, $toScenario)) {

            $this->notFoundByIdException($id);

        }

        return $model;

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return ModelWithId|null
     */
    public function freshFindById(int $id, string $toScenario = null)
    {

        // Find record in db
        if (!$record = $this->findRecordById($id)) {
            return null;
        }

        $model = $this->create($record, $toScenario);

        return $model;

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return ModelWithId
     * @throws ModelNotFoundException
     */
    public function freshGetById(int $id, string $toScenario = null): ModelWithId
    {

        if (!$model = $this->freshFindById($id, $toScenario)) {

            $this->notFoundByIdException($id);

        }

        return $model;

    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * Find an existing cache by id
     *
     * @param int $id
     * @return null
     */
    public function findCacheById(int $id)
    {

        // Check if already in cache
        if (!$this->isCachedById($id)) {
            return null;
        }

        return $this->_cacheById[$id];

    }

    /**
     * Identify whether in cache by id
     *
     * @param int $id
     * @return bool
     */
    private function isCachedById(int $id): bool
    {
        return array_key_exists($id, $this->_cacheById);
    }


    /**
     * @param ModelWithId $model
     * @return static
     */
    protected function cacheById(ModelWithId $model)
    {

        if (null === $model->getId()) {
            return $this;
        }

        // Check if already in cache
        if (!$this->isCachedById($model->getId())) {

            // Cache it
            $this->_cacheById[$model->getId()] = $model;

        }

        return $this;

    }

    /**
     * @param RecordWithId $record
     * @return ModelWithId|null
     */
    protected function findCacheByRecordById(RecordWithId $record)
    {

        $value = $record->id;

        if ($value === null) {
            return null;
        }

        return $this->findCacheById($value);

    }

    /*******************************************
     * RECORD BY ID
     *******************************************/

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return RecordWithId|null
     */
    public function findRecordById(int $id, string $toScenario = null)
    {

        return $this->findRecordByCondition(
            [
                'id' => $id
            ],
            $toScenario
        );

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return Record|RecordWithId|null
     */
    public function getRecordById(int $id, string $toScenario = null): Record
    {

        if (!$record = $this->findRecordById($id, $toScenario)) {

            $this->notFoundRecordByIdException($id);

        }

        return $record;

    }


    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @param int|null $id
     * @throws ModelNotFoundException
     */
    protected function notFoundByIdException(int $id = null)
    {

        throw new ModelNotFoundException(
            sprintf(
                'Model does not exist with the id "%s".',
                (string)$id
            )
        );

    }

    /**
     * @param int|null $id
     * @throws RecordNotFoundException
     */
    protected function notFoundRecordByIdException(int $id = null)
    {

        throw new RecordNotFoundException(
            sprintf(
                'Record does not exist with the id "%s".',
                (string)$id
            )
        );

    }

}
