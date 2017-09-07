<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use craft\base\ElementInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ElementByString extends Element
{

    use traits\ElementByString;

    /*******************************************
     * FIND
     *******************************************/

    /**
     * @inheritdoc
     */
    public function find($identifier, int $siteId = null)
    {

        if ($model = parent::find($identifier, $siteId)) {
            return $model;
        }

        if (!is_string($identifier)) {
            return null;
        }

        return $this->findByString($identifier, $siteId);
    }

    /**
     * @inheritdoc
     */
    public function findCache($identifier, int $siteId = null)
    {

        if ($model = parent::findCache($identifier)) {
            return $model;
        }

        if (!is_string($identifier)) {
            return null;
        }

        return $this->findCacheByString($identifier);
    }

    /**
     * @inheritdoc
     */
    public function addToCache(ElementInterface $element)
    {

        parent::addToCache($element);

        $this->cacheByString($element);

        return $this;
    }
}
