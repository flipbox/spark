<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\records;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
abstract class RecordWithState extends Record
{

    use traits\RecordWithState;

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            $this->stateRules()
        );
    }
}
