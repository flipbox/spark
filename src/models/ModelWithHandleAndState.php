<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models;

use Craft;
use flipbox\spark\helpers\ModelHelper;

/**
 * @package flipbox\spark\models
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ModelWithHandleAndState extends ModelWithHandle
{

    /**
     * @var boolean Enabled
     */
    public $enabled;

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return (bool)$this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * @inheritdoc
     */
    public function toEnabled()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toDisabled()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'enabled'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_POPULATE
                    ]
                ]
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return array_merge(
            parent::attributeLabels(),
            [
                'state' => Craft::t('app', 'State')
            ]
        );

    }

}
