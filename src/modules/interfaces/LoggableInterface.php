<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\modules\interfaces;

/**
 * @package flipbox\spark\modules\interfaces
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface LoggableInterface
{

    /**
     * Identify whether the module is in debug mode
     *
     * @return bool
     */
    public function isDebugModeEnabled();

}
