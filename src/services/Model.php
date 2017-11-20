<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use craft\helpers\Json as JsonHelper;
use flipbox\spark\db\ActiveModelQuery;
use flipbox\spark\exceptions\ModelNotFoundException;
use flipbox\spark\exceptions\RecordNotFoundException;
use flipbox\spark\helpers\ArrayHelper;
use flipbox\spark\helpers\ModelHelper;
use flipbox\spark\helpers\QueryHelper;
use flipbox\spark\models\Model as BaseModel;
use flipbox\spark\records\Record;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\QueryInterface;
use Yii;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Model extends Component
{

    use traits\Model;

    /**
     * @var BaseModel[]
     */
    protected $cacheAll;

    /*******************************************
     * MODEL CLASSES
     *******************************************/

    /**
     * @return string
     */
    abstract public static function modelClass(): string;

    /**
     * @return string
     */
    public static function modelClassInstance(): string
    {
        return BaseModel::class;
    }

    /*******************************************
     * QUERY
     *******************************************/

    /**
     * @param array $config
     * @return ActiveModelQuery
     */
    public function getQuery($config = [])
    {
        /** @var ActiveModelQuery $query */
        $query = Yii::createObject(
            ActiveModelQuery::class,
            [
                $this->recordClass(),
                [
                    'serviceClass' => $this
                ]
            ]
        );

        if ($config) {
            QueryHelper::configure(
                $query,
                $config
            );
        }

        return $query;
    }

    /*******************************************
     * CREATE
     *******************************************/

    /**
     * @param array|Record $config
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

        // Any eager loaded relations
        if ($relations = $record->getRelatedRecords()) {
            $model->setAttributes($relations);
        }

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
        if (is_null($this->cacheAll)) {
            $this->cacheAll = [];

            // Find record in db
            if ($records = $this->findAllRecords()) {
                foreach ($records as $record) {
                    $this->cacheAll[] = $this->findByRecord($record, $toScenario);
                }
            }
        }

        return $this->cacheAll;
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
     * @param array $records
     * @param string|null $toScenario
     * @return BaseModel[]
     */
    public function findAllByRecords(array $records, string $toScenario = null): array
    {
        $models = [];

        foreach ($records as $index => $record) {
            $models[$index] = $this->findByRecord($record, $toScenario);
        }

        return $models;
    }

    /**
     * @param array $records
     * @param string|null $toScenario
     * @return BaseModel[]
     * @throws ModelNotFoundException
     */
    public function getAllByRecords(array $records, string $toScenario = null): array
    {
        $models = $this->findAllByRecords($records, $toScenario);

        if (empty($models)) {
            throw new ModelNotFoundException("Unable to get from records.");
        }

        return $models;
    }

    /**
     * @param Record $record
     * @param string $toScenario
     * @return BaseModel
     */
    public function findByRecord(Record $record, string $toScenario = null): BaseModel
    {
        if (!$model = $this->findCacheByRecord($record)) {
            $model = $this->createFromRecord($record, $toScenario);
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

    /**
     * @param BaseModel $model
     * @return Record|null
     */
    public function findRecordByModel(BaseModel $model)
    {
        return null;
    }

    /**
     * @param BaseModel $model
     * @return Record
     * @throws RecordNotFoundException
     */
    public function getRecordByModel(BaseModel $model): Record
    {

        if (!$record = $this->findRecordByModel($model)) {
            throw new RecordNotFoundException("Record does not exist found.");
        }

        return $record;
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
        }

        return null;
    }

    /**
     * @param Record $record
     * @return BaseModel|null
     */
    public function findCacheByRecord(Record $record)
    {
        return null;
    }

    /**
     * @param BaseModel $model
     * @return static
     */
    public function addToCache(BaseModel $model)
    {
        return $this;
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
