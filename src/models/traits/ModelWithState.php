<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models\traits;

use Craft;
use flipbox\spark\helpers\ModelHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
trait ModelWithState
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
    public function stateRules()
    {

        return [
            [
                [
                    'enabled'
                ],
                'safe',
                'on' => [
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];

    }

    /**
     * @inheritdoc
     */
    public function stateAttributeLabel()
    {

        return [
            'state' => Craft::t('app', 'State')
        ];

    }

}
