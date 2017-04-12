<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use craft\helpers\Json as JsonHelper;
use flipbox\spark\exceptions\ModelNotFoundException;
use flipbox\spark\helpers\ArrayHelper;
use flipbox\spark\helpers\ModelHelper;
use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\records\Record;
use flipbox\spark\services\traits\ModelRecordBehavior;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\QueryInterface;

/**
 * @package flipbox\spark\services
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Model extends Component
{

    use ModelRecordBehavior;

    /**
     * @var BaseModel[]
     */
    protected $_cacheAll;

    /**
     * @var BaseModel[]
     */
    protected $_cacheById = [];


    /*******************************************
     * MODEL CLASSES
     *******************************************/

    /**
     * @return string
     */
    public abstract static function modelClass(): string;

    /**
     * @return string
     */
    public static function modelClassInstance(): string
    {
        return BaseModel::class;
    }

    /*******************************************
     * CREATE
     *******************************************/

    /**
     * @param array $config
     * @param string|null $toScenario
     * @throws InvalidConfigException
     * @return BaseModel
     */
    public function create($config = [], string $toScenario = null): BaseModel
    {

        // Treat records as known data and set via config
        if ($config instanceof Record) {
            return $this->createFromRecord($config, $toScenario);
        }

        // Force Array
        if (!is_array($config)) {
            $config = ArrayHelper::toArray($config, [], false);
        }

        // Set the model class
        $config['class'] = static::modelClass();

        return ModelHelper::create(
            $config,
            static::modelClassInstance(),
            $toScenario
        );

    }

    /**
     * @param Record $record
     * @param string|null $toScenario
     * @throws InvalidConfigException
     * @return BaseModel
     */
    protected function createFromRecord(Record $record, string $toScenario = null): BaseModel
    {

        if (null !== $toScenario) {
            $record->setScenario($toScenario);
        }

        $modelClass = static::modelClass();

        /** @var BaseModel $model */
        $model = new $modelClass($record);

        if (null !== $toScenario) {
            $model->setScenario($toScenario);
        }

        return $model;

    }

    /*******************************************
     * FIND/GET ALL
     *******************************************/

    /**
     * @param string $toScenario
     * @return BaseModel[]
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
     * @return BaseModel[]
     * @throws ModelNotFoundException
     */
    public function getAll(string $toScenario = null): array
    {

        if (!$models = $this->findAll($toScenario)) {

            $this->notFoundException();

        }

        return $models;

    }

    /*******************************************
     * FIND/GET
     *******************************************/

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseModel|null
     */
    public function find($identifier, string $toScenario = null)
    {

        if ($identifier instanceof BaseModel) {

            $this->addToCache($identifier);

            if (null !== $toScenario) {
                $identifier->setScenario($toScenario);
            }

            return $identifier;

        } elseif ($identifier instanceof Record) {

            return $this->findByRecord($identifier, $toScenario);

        } elseif (is_numeric($identifier)) {

            return $this->findById($identifier, $toScenario);

        }

        return null;

    }

    /**
     * @param $identifier
     * @param string $toScenario
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function get($identifier, string $toScenario = null): BaseModel
    {

        // Find model by ID
        if (!$model = $this->find($identifier, $toScenario)) {

            $this->notFoundException();

        }

        return $model;

    }

    /*******************************************
     * FIND/GET BY ID
     *******************************************/

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseModel|null
     */
    public function findById(int $id, string $toScenario = null)
    {

        // Check cache
        if (!$model = $this->findCacheById($id)) {

            // Find record in db
            if ($record = $this->findRecordById($id)) {

                // Perhaps in cache
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
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function getById(int $id, string $toScenario = null): BaseModel
    {

        // Find by ID
        if (!$model = $this->findById($id, $toScenario)) {

            $this->notFoundByIdException($id);

        }

        return $model;

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseModel|null
     */
    public function freshFindById(int $id, string $toScenario = null)
    {

        // Find record in db
        if ($record = $this->findRecordById($id)) {

            // Create
            return $this->createFromRecord($record, $toScenario);

        }

        return null;

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function freshGetById(int $id, string $toScenario = null): BaseModel
    {

        if (!$model = $this->freshFindById($id, $toScenario)) {

            $this->notFoundByIdException($id);

        }

        return $model;

    }

    /*******************************************
     * FIND/GET BY QUERY
     *******************************************/

    /**
     * @param QueryInterface $query
     * @param string $toScenario
     * @return BaseModel[]
     */
    public function findAllByQuery(QueryInterface $query, string $toScenario = null): array
    {

        $models = array();

        foreach ($query->all() as $record) {
            $models[] = $this->findByRecord($record, $toScenario);
        }

        return $models;

    }

    /**
     * @param QueryInterface $query
     * @param string $toScenario
     * @return BaseModel|null
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
     * @return BaseModel[]
     */
    public function findAllByCondition($condition, string $toScenario = null): array
    {

        $models = [];

        // Find record in db
        if ($records = $this->findAllRecordsByCondition($condition)) {

            foreach ($records as $record) {
                $models[] = $this->findByRecord($record, $toScenario);
            }

        }

        return $models;

    }

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseModel[]
     * @throws ModelNotFoundException
     */
    public function getAllByCondition($condition, string $toScenario = null): array
    {

        if (!$models = $this->findAllByCondition($condition, $toScenario)) {

            $this->notFoundByConditionException($condition);

        }

        return $models;

    }

    /**
     * @param $condition
     * @param string $toScenario
     * @return BaseModel|null
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
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function getByCondition($condition, string $toScenario = null): BaseModel
    {

        if (!$model = $this->findByCondition($condition, $toScenario)) {

            $this->notFoundByConditionException($condition);

        }

        return $model;

    }

    /*******************************************
     * FIND/GET BY CRITERIA
     *******************************************/

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseModel[]
     */
    public function findAllByCriteria($criteria, string $toScenario = null): array
    {

        $models = [];

        // Find record in db
        if ($records = $this->findAllRecordsByCriteria($criteria)
        ) {

            foreach ($records as $record) {
                $models[] = $this->findByRecord($record, $toScenario);
            }

        }

        return $models;

    }

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseModel[]
     * @throws ModelNotFoundException
     */
    public function getAllByCriteria($criteria, string $toScenario = null): array
    {

        if (!$models = $this->findAllByCriteria($criteria, $toScenario)) {

            $this->notFoundByCriteriaException($criteria);

        }

        return $models;

    }

    /**
     * @param $criteria
     * @param string $toScenario
     * @return BaseModel|null
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
     * @return BaseModel
     * @throws ModelNotFoundException
     */
    public function getByCriteria($criteria, string $toScenario = null): BaseModel
    {

        if (!$model = $this->findByCriteria($criteria, $toScenario)) {

            $this->notFoundByCriteriaException($criteria);

        }

        return $model;

    }


    /*******************************************
     * FIND/GET BY RECORD
     *******************************************/

    /**
     * @param Record $record
     * @param string $toScenario
     * @return BaseModel
     */
    public function findByRecord(Record $record, string $toScenario = null): BaseModel
    {

        // Check addToCache
        if (!$model = $this->findCacheByRecord($record)) {

            // New model
            $model = $this->createFromRecord($record, $toScenario);

            // Cache it
            $this->addToCache($model);

        }

        return $model;

    }

    /**
     * @param Record $record
     * @param string $toScenario
     * @return BaseModel
     */
    public function getByRecord(Record $record, string $toScenario = null): BaseModel
    {
        return $this->findByRecord($record, $toScenario);
    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @param $identifier
     * @return BaseModel|null
     */
    public function findCache($identifier)
    {

        if ($identifier instanceof Record) {

            return $this->findCacheByRecord($identifier);

        } elseif (is_numeric($identifier)) {

            return $this->findCacheById($identifier);

        }

        return null;

    }

    /**
     * Find an existing cache by ID
     *
     * @param $id
     * @return BaseModel|null
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
     * @param BaseModel $model
     * @return $this
     */
    protected function cacheById(BaseModel $model)
    {

        // Check if already in cache
        if (!$id = $this->isCachedById($model->id)) {

            // Cache it
            $this->_cacheById[$id] = $model;

        }

        return $this;

    }

    /**
     * @param Record $record
     * @return BaseModel|null
     */
    public function findCacheByRecord(Record $record)
    {

        // Check if already in addToCache by id
        if ($id = $this->isCachedById($record->id)) {

            return $this->findCacheById($id);

        }

        return null;
    }

    /**
     * @param BaseModel $model
     * @return static
     */
    public function addToCache(BaseModel $model)
    {

        $this->cacheById($model);

        return $this;
    }


    /*******************************************
     * FIND/GET RECORD BY ID
     *******************************************/

    /**
     * @param int $id
     * @param string $toScenario
     * @return Record|null
     */
    public function findRecordById(int $id, string $toScenario = null)
    {

        return $this->findRecordByCondition(
            ['id' => $id],
            $toScenario
        );

    }

    /**
     * @param int $id
     * @param string|null $toScenario
     * @return Record
     */
    public function getRecordById(int $id, string $toScenario = null): Record
    {

        return $this->getRecordByCondition(
            ['id' => $id],
            $toScenario
        );

    }


    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @throws ModelNotFoundException
     */
    protected function notFoundException()
    {

        throw new ModelNotFoundException(
            sprintf(
                "Model does not exist."
            )
        );

    }

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
     * @param null $criteria
     * @throws ModelNotFoundException
     */
    protected function notFoundByCriteriaException($criteria = null)
    {

        throw new ModelNotFoundException(
            sprintf(
                'Model does not exist with the criteria "%s".',
                (string)JsonHelper::encode($criteria)
            )
        );

    }

    /**
     * @param null $condition
     * @throws ModelNotFoundException
     */
    protected function notFoundByConditionException($condition = null)
    {

        throw new ModelNotFoundException(
            sprintf(
                'Model does not exist with the condition "%s".',
                (string)JsonHelper::encode($condition)
            )
        );

    }

}
