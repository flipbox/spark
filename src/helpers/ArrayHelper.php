<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\helpers;

use craft\helpers\ArrayHelper as BaseArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ArrayHelper extends BaseArrayHelper
{

    /**
     * Returns the first value in a given array.
     *
     * @param array $arr
     *
     * @return mixed|null
     */
    public static function firstValue(array $arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $value) {
                return $value;
            }
        }

        return null;
    }
}
