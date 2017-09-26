<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\db;

use flipbox\spark\services\Object;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\base\Object as BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ActiveObjectQuery extends ActiveQuery
{
    /**
     * @var Object
     */
    public $serviceClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->serviceClass instanceof Object) {
            throw new InvalidConfigException("Invalid service class");
        }

        parent::init();
    }

    /**
     * @inheritdoc
     *
     * @return BaseObject[]
     */
    public function populate($rows)
    {
        return $this->serviceClass->findAllByRecords(
            parent::populate($rows)
        );
    }
}
