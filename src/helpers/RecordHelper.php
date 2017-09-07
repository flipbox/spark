<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\helpers;

use Craft;
use flipbox\spark\records\Record;
use yii\base\InvalidConfigException;
use yii\db\QueryInterface;
use yii\db\Transaction;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RecordHelper
{

    /**
     * The default scenario
     */
    const DEFAULT_SCENARIO = self::SCENARIO_DEFAULT;

    /**
     * The scenario used by default
     */
    const SCENARIO_DEFAULT = 'default';

    /**
     * The scenario used to populate a model
     */
    const SCENARIO_POPULATE = 'populate';

    /**
     * The scenario used to insert a record
     */
    const SCENARIO_INSERT = 'insert';

    /**
     * The scenario used to update a record
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * The scenario used to save a record
     */
    const SCENARIO_SAVE = 'save';

    /**
     * @param $config
     * @param string|null $instanceOf
     * @param string|null $toScenario
     * @return Record
     * @throws InvalidConfigException
     */
    public static function create($config, string $instanceOf = null, string $toScenario = null)
    {

        // Get class from config
        $class = ObjectHelper::checkConfig($config, $instanceOf);

        // New model
        $model = new $class();

        return static::populate($model, $config, $toScenario);
    }

    /**
     * @param Record $record
     * @param array $properties
     * @param string $toScenario
     * @return Record
     */
    public static function populate(Record $record, $properties = [], string $toScenario = null)
    {

        // Set properties
        foreach ($properties as $name => $value) {
            if ($record->canSetProperty($name)) {
                $record->$name = $value;
            }
        }

        // Set scenario
        if (null !== $toScenario) {
            $record->setScenario($toScenario);
        }

        return $record;
    }

    /**
     * @param $condition
     * @return array
     */
    public static function conditionToCriteria($condition)
    {

        if (empty($condition)) {
            return $condition;
        }

        // Assume it's an id
        if (!is_array($condition)) {
            $condition = [
                'id' => $condition
            ];
        }

        return ['where' => ['and', $condition]];
    }

    /**
     * @param string|Record $record
     * @param $criteria
     * @return QueryInterface
     */
    public static function configure($record, $criteria)
    {

        $query = $record::find();

        QueryHelper::configure(
            $query,
            $criteria
        );

        return $query;
    }


    /**
     * @return Transaction
     */
    public static function beginTransaction()
    {
        return Craft::$app->getDb()->beginTransaction();
    }
}
