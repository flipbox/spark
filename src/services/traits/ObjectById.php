<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use flipbox\spark\exceptions\ObjectNotFoundException;
use flipbox\spark\objects\ObjectWithId;
use flipbox\spark\records\RecordWithId;
use yii\base\Object as BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 *
 * @method RecordWithId|null findRecordByCondition($condition, string $toScenario = null)
 */
trait ObjectById
{

    use Object;

    /**
     * @var BaseObject[]
     */
    protected $_cacheById = [];

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

                $this->_cacheById[$id] = null;

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
        if ($record = $this->findRecordByCondition(
            ['id' => $id]
        )
        ) {

            // Create
            return $this->createFromRecord($record, $toScenario);

        }

        return null;

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

            return $this->_cacheById[$id];

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
        return array_key_exists($id, $this->_cacheById);
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
            $this->_cacheById[$id] = $object;

        }

        return $this;

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

}