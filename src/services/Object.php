<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use craft\helpers\Json as JsonHelper;
use flipbox\spark\exceptions\ObjectNotFoundException;
use flipbox\spark\helpers\ArrayHelper;
use flipbox\spark\helpers\ObjectHelper;
use flipbox\spark\Records\Record;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Object as BaseObject;
use yii\db\QueryInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Object extends Component
{

    use traits\Object;

    /**
     * @var BaseObject[]
     */
    protected $_cacheAll;

    /*******************************************
     * OBJECT CLASSES
     *******************************************/

    /**
     * @return string
     */
    public abstract static function objectClass(): string;

    /**
     * @return string
     */
    public static function objectClassInstance(): string
    {
        return BaseObject::class;
    }

    /*******************************************
     * CREATE
     *******************************************/

    /**
     * @param array|Record $config
     * @throws InvalidConfigException
     * @return BaseObject
     */
    public function create($config = []): BaseObject
    {

        // Treat records as known data and set via config
        if ($config instanceof Record) {
            return $this->createFromRecord($config);
        }

        // Force Array
        if (!is_array($config)) {
            $config = ArrayHelper::toArray($config, [], false);
        }

        // Auto-set the class
        if ($class = static::objectClass()) {
            $config['class'] = $class;
        }

        return ObjectHelper::create(
            $config,
            static::objectClassInstance()
        );

    }

    /**
     * @param Record $record
     * @param string|null $toScenario
     * @throws InvalidConfigException
     * @return BaseObject
     */
    protected function createFromRecord(Record $record, string $toScenario = null): BaseObject
    {

        if (null !== $toScenario) {
            $record->setScenario($toScenario);
        }

        $config = $record->toArray();

        // Auto-set the class
        if ($class = static::objectClass()) {
            $config['class'] = $class;
        }

        return ObjectHelper::create(
            $config,
            static::objectClassInstance()
        );

    }


    /*******************************************
     * FIND/GET ALL
     *******************************************/

    /**
     * @param string $toScenario
     * @return BaseObject[]
     */
    public function findAll(string $toScenario = null)
    {

        // Check addToCache
        if (is_null($this->_cacheAll)) {

            $this->_cacheAll = [];

            // Find record in db
            if ($records = $this->findAllRecords()) {

                foreach ($records as $record) {

                    $this->_cacheAll[] = $this->findByRecord($record, $toScenario);

                }

            }

        }

        return $this->_cacheAll;

    }

    /**
     * @param string $toScenario
     * @return BaseObject[]
     * @throws ObjectNotFoundException
     */
    public function getAll(string $toScenario = null): array
    {

        if (!$objects = $this->findAll($toScenario)) {

            $this->notFoundException();

        }

        return $objects;

    }

    /*******************************************
     * FIND/GET
     *******************************************/

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseObject|null
     */
    public function find($identifier, string $toScenario = null)
    {

        if ($identifier instanceof BaseObject) {

            $this->addToCache($identifier);

            return $identifier;

        } elseif ($identifier instanceof Record) {

            return $this->findByRecord($identifier, $toScenario);

        }

        return null;

    }

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseObject
     * @throws ObjectNotFoundException
     */
    public function get($identifier, string $toScenario = null): BaseObject
    {

        // Find model by ID
        if (!$object = $this->find($identifier, $toScenario)) {

            $this->notFoundException();

        }

        return $object;

    }

    /*******************************************
     * FIND/GET BY QUERY
     *******************************************/

    /**
     * @param QueryInterface $query
     * @param string $toScenario
     * @return BaseObject[]
     */
    public function findAllByQuery(QueryInterface $query, string $toScenario = null): array
    {

        $objects = array();

        foreach ($query->all() as $record) {
            $objects[] = $this->findByRecord($record, $toScenario);
        }

        return $objects;

    }

    /**
     * @param QueryInterface $query
     * @param string $toScenario
     * @return BaseObject|null
     */
    public function findByQuery(QueryInterface $query, string $toScenario = null)
    {

        /** @var Record $record */
        if (!$record = $query->one()) {
            return null;
        }

        return $this->findByRecord($record, $toScenario);

    }

    /*******************************************
     * FIND/GET BY CONDITION
     *******************************************/

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseObject[]
     */
    public function findAllByCondition($condition, string $toScenario = null): array
    {

        $objects = [];

        // Find record in db
        if ($records = $this->findAllRecordsByCondition($condition)) {

            foreach ($records as $record) {
                $objects[] = $this->findByRecord($record, $toScenario);
            }

        }

        return $objects;

    }

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseObject[]
     * @throws ObjectNotFoundException
     */
    public function getAllByCondition($condition, string $toScenario = null): array
    {

        if (!$objects = $this->findAllByCondition($condition, $toScenario)) {

            $this->notFoundByConditionException($condition);

        }

        return $objects;

    }

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseObject|null
     */
    public function findByCondition($condition, string $toScenario = null)
    {

        // Find record in db
        if ($record = $this->findRecordByCondition($condition)) {
            return $this->findByRecord($record, $toScenario);
        }

        return null;

    }

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseObject
     * @throws ObjectNotFoundException
     */
    public function getByCondition($condition, string $toScenario = null): BaseObject
    {

        if (!$object = $this->findByCondition($condition, $toScenario)) {

            $this->notFoundByConditionException($condition);

        }

        return $object;

    }

    /*******************************************
     * FIND/GET BY CRITERIA
     *******************************************/

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseObject[]
     */
    public function findAllByCriteria($criteria, string $toScenario = null): array
    {

        $objects = [];

        // Find record in db
        if ($records = $this->findAllRecordsByCriteria($criteria)
        ) {

            foreach ($records as $record) {
                $objects[] = $this->findByRecord($record, $toScenario);
            }

        }

        return $objects;

    }

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseObject[]
     * @throws ObjectNotFoundException
     */
    public function getAllByCriteria($criteria, string $toScenario = null): array
    {

        if (!$objects = $this->findAllByCriteria($criteria, $toScenario)) {

            $this->notFoundByCriteriaException($criteria);

        }

        return $objects;

    }

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseObject|null
     */
    public function findByCriteria($criteria, string $toScenario = null)
    {

        // Find record in db
        if ($record = $this->findRecordByCriteria($criteria)) {
            return $this->findByRecord($record, $toScenario);
        }

        return null;

    }

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseObject
     * @throws ObjectNotFoundException
     */
    public function getByCriteria($criteria, string $toScenario = null): BaseObject
    {

        if (!$object = $this->findByCriteria($criteria, $toScenario)) {

            $this->notFoundByCriteriaException($criteria);

        }

        return $object;

    }


    /*******************************************
     * FIND/GET BY RECORD
     *******************************************/

    /**
     * @param Record $record
     * @param string $toScenario
     * @return BaseObject
     */
    public function findByRecord(Record $record, string $toScenario = null): BaseObject
    {

        // Check addToCache
        if (!$object = $this->findCacheByRecord($record)) {

            // New model
            $object = $this->createFromRecord($record, $toScenario);

            // Cache it
            $this->addToCache($object);

        }

        return $object;

    }

    /**
     * @param Record $record
     * @param string $toScenario
     * @return BaseObject
     */
    public function getByRecord(Record $record, string $toScenario = null): BaseObject
    {
        return $this->findByRecord($record, $toScenario);
    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @param $identifier
     * @return BaseObject|null
     */
    public function findCache($identifier)
    {

        if ($identifier instanceof Record) {

            return $this->findCacheByRecord($identifier);

        }

        return null;

    }

    /**
     * @param Record $record
     * @return BaseObject|null
     */
    public function findCacheByRecord(Record $record)
    {
        return null;
    }

    /**
     * @param BaseObject $object
     * @return static
     */
    public function addToCache(BaseObject $object)
    {
        return $this;
    }


    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @throws ObjectNotFoundException
     */
    protected function notFoundException()
    {

        throw new ObjectNotFoundException(
            sprintf(
                "Object does not exist."
            )
        );

    }

    /**
     * @param null $criteria
     * @throws ObjectNotFoundException
     */
    protected function notFoundByCriteriaException($criteria = null)
    {

        throw new ObjectNotFoundException(
            sprintf(
                'Object does not exist with the criteria "%s".',
                (string)JsonHelper::encode($criteria)
            )
        );

    }

    /**
     * @param null $condition
     * @throws ObjectNotFoundException
     */
    protected function notFoundByConditionException($condition = null)
    {

        throw new ObjectNotFoundException(
            sprintf(
                'Object does not exist with the condition "%s".',
                (string)JsonHelper::encode($condition)
            )
        );

    }

}
