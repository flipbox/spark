<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\exceptions;

use yii\base\ErrorException as Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectNotFoundException extends Exception
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Object Not Found Exception';
    }

}