<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.1.0
 */
abstract class ModelByIdOrHandle extends ModelByIdOrString
{

    /**
     * @inheritdoc
     */
    protected function stringProperty(): string
    {
        return 'handle';
    }

}
