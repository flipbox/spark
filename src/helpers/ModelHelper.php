<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\helpers;

use flipbox\spark\models\Model;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ModelHelper
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
     * The scenario used to insert a model
     */
    const SCENARIO_INSERT = 'insert';

    /**
     * The scenario used to update a model
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * The scenario used to save a model
     */
    const SCENARIO_SAVE = 'save';

    /**
     * @param $config
     * @param string|null $instanceOf
     * @param string|null $toScenario
     * @throws InvalidConfigException
     * @return Model
     */
    public static function create($config, string $instanceOf = null, string $toScenario = null): Model
    {

        // Get class from config
        $class = ObjectHelper::checkConfig($config, $instanceOf);

        // New model
        $model = new $class();

        return static::populate($model, $config, $toScenario);
    }

    /**
     * @param Model $model
     * @param array $attributes
     * @param string|null $toScenario
     * @return Model
     */
    public static function populate(Model $model, $attributes = [], string $toScenario = null): Model
    {

        // Set scenario
        if (null !== $toScenario) {
            $model->setScenario($toScenario);
        }

        // Populate model attributes
        $model->setAttributes($attributes);

        return $model;
    }
}
