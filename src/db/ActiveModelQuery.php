<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\db;

use flipbox\spark\services\Model;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use flipbox\spark\models\Model as BaseModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ActiveModelQuery extends ActiveQuery
{
    /**
     * @var Model
     */
    public $serviceClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->serviceClass instanceof Model) {
            throw new InvalidConfigException("Invalid service class");
        }

        parent::init();
    }

    /**
     * @inheritdoc
     *
     * @return BaseModel[]
     */
    public function populate($rows)
    {
        return $this->serviceClass->findAllByRecords(
            parent::populate($rows)
        );
    }
}
