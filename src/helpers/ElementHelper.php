<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\helpers;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\ElementHelper as BaseElementHelper;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ElementHelper extends BaseElementHelper
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
     * The scenario used to populate an element
     */
    const SCENARIO_POPULATE = 'populate';

    /**
     * The scenario used to insert an element
     */
    const SCENARIO_INSERT = 'insert';

    /**
     * The scenario used to update an element
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * The scenario used to save an element
     */
    const SCENARIO_SAVE = 'save';

    /**
     * @param $config
     * @param null $instanceOf
     * @param string|null $toScenario
     * @throws InvalidConfigException
     * @return ElementInterface
     */
    public static function create(
        $config,
        $instanceOf = null,
        string $toScenario = null
    ): ElementInterface {

        // Get class from config
        $class = ObjectHelper::checkConfig($config, $instanceOf);

        // New model
        $model = new $class();

        return static::populate($model, $config, $toScenario);
    }

    /**
     * @param ElementInterface $element
     * @param array $attributes
     * @param string|null $toScenario
     * @return ElementInterface
     */
    public static function populate(
        ElementInterface $element,
        $attributes = [],
        string $toScenario = null
    ): ElementInterface {

        /** @var Element $element */

        // Set scenario
        if (null !== $toScenario) {
            $element->setScenario($toScenario);
        }

        // Populate model attributes
        $element->setAttributes($attributes);

        return $element;
    }
}
