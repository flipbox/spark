<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\exceptions\ObjectNotFoundException;
use flipbox\spark\exceptions\RecordNotFoundException;
use flipbox\spark\objects\ObjectWithId;
use flipbox\spark\records\Record;
use flipbox\spark\records\RecordWithId;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 *
 * @method RecordWithId|null findRecordByCondition($condition, string $toScenario = null)
 */
trait ObjectById
{

    use SparkObject;

    /**
     * @var BaseObject[]
     */
    protected $cacheById = [];

    /**
     * @param Record $record
     * @param string|null $toScenario
     * @return BaseObject
     */
    abstract protected function findByRecord(Record $record, string $toScenario = null): BaseObject;


    /*******************************************
     * FIND/GET BY ID
     *******************************************/

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseObject|null
     */
    public function findById(int $id, string $toScenario = null)
    {

        // Check cache
        if (!$object = $this->findCacheById($id)) {
            // Find record in db
            if ($record = $this->findRecordByCondition(
                ['id' => $id]
            )
            ) {
                // Perhaps in cache
                $object = $this->findByRecord($record, $toScenario);
            } else {
                $this->cacheById[$id] = null;

                return null;
            }
        }

        return $object;
    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseObject
     * @throws ObjectNotFoundException
     */
    public function getById(int $id, string $toScenario = null): BaseObject
    {

        // Find by ID
        if (!$object = $this->findById($id, $toScenario)) {
            $this->notFoundByIdException($id);
        }

        return $object;
    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseObject|null
     */
    public function freshFindById(int $id, string $toScenario = null)
    {

        // Find record in db
        if (!$record = $this->findRecordById($id)) {
            return null;
        }

        return $this->createFromRecord($record, $toScenario);
    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseObject
     * @throws ObjectNotFoundException
     */
    public function freshGetById(int $id, string $toScenario = null): BaseObject
    {

        if (!$object = $this->freshFindById($id, $toScenario)) {
            $this->notFoundByIdException($id);
        }

        return $object;
    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * Find an existing cache by ID
     *
     * @param $id
     * @return BaseObject|null
     */
    public function findCacheById(int $id)
    {

        // Check if already in addToCache
        if ($this->isCachedById($id)) {
            return $this->cacheById[$id];
        }

        return null;
    }

    /**
     * Identify whether in cache by ID
     *
     * @param $id
     * @return bool
     */
    protected function isCachedById(int $id)
    {
        return array_key_exists($id, $this->cacheById);
    }

    /**
     * @param ObjectWithId $object
     * @return $this
     */
    protected function cacheById(ObjectWithId $object)
    {

        // Check if already in cache
        if (!$id = $this->isCachedById($object->id)) {
            // Cache it
            $this->cacheById[$id] = $object;
        }

        return $this;
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
     * @throws RecordNotFoundException
     * @return RecordWithId|null
     */
    public function getRecordById(int $id, string $toScenario = null)
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
     * @throws ObjectNotFoundException
     */
    protected function notFoundByIdException(int $id = null)
    {

        throw new ObjectNotFoundException(
            sprintf(
                'Object does not exist with the id "%s".',
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
