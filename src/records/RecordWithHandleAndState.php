<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records;

use flipbox\spark\helpers\RecordHelper;

/**
 * @property bool $enabled
 *
 * @package flipbox\spark\records
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class RecordWithHandleAndState extends RecordWithHandle
{

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            [
                [
                    'enabled',
                    'safe',
                    'on' => [
                        RecordHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );

    }

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

}
