<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use craft\base\Element as BaseElement;
use craft\base\ElementInterface;
use flipbox\spark\exceptions\ElementNotFoundException;
use flipbox\spark\helpers\SiteHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
trait ElementByString
{

    /**
     * @var [ElementInterface[]]
     */
    protected $_cacheByString = [];

    /*******************************************
     * ABSTRACTS
     *******************************************/

    /**
     * @return string
     */
    abstract protected function stringProperty(): string;

    /**
     * @param $string
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    abstract protected function freshFindByString(string $string, int $siteId = null);

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function stringValue(ElementInterface $element)
    {

        $property = $this->stringProperty();

        return $element->{$property};

    }

    /**
     * @param string $string
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function findByString(string $string, int $siteId = null)
    {

        // Check cache
        if (!$element = $this->findCacheByString($string)) {

            // Find new element
            if ($element = $this->freshFindByString($string, $siteId)) {

                // Cache it
                $this->cacheByString($element);

            } else {

                // Cache nothing
                $this->_cacheByString[$string] = $element;

            }

        }

        return $element;

    }

    /**
     * @param string $string
     * @param int|null $siteId
     * @return BaseElement|ElementInterface
     * @throws ElementNotFoundException
     */
    public function getByString(string $string, int $siteId = null)
    {

        // Find by Handle
        if (!$element = $this->findByString($string, $siteId)) {

            $this->notFoundByStringException($string);

        }

        return $element;

    }


    /**
     * @param string $string
     * @param int|null $siteId
     * @return BaseElement|ElementInterface
     * @throws ElementNotFoundException
     */
    public function freshGetByString(string $string, int $siteId = null)
    {

        if (!$element = $this->freshFindByString($string, $siteId)) {

            $this->notFoundByStringException($string);

        }

        return $element;

    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * Find an existing cache by Handle
     *
     * @param string $string
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function findCacheByString(string $string, int $siteId = null)
    {

        // Check if already in cache
        if ($this->isCachedByString($string, $siteId)) {

            return $this->_cacheByString[$siteId][$string];

        }

        return null;

    }

    /**
     * Identify whether in cached by string
     *
     * @param $string
     * @param int|null $siteId
     * @return bool
     */
    protected function isCachedByString($string, int $siteId = null)
    {

        // Resolve siteId
        $siteId = SiteHelper::resolveSiteId($siteId);

        if (!isset($this->_cacheByString[$siteId])) {
            $this->_cacheByString[$siteId] = [];
        }

        return array_key_exists($string, $this->_cacheByString[$siteId]);

    }

    /**
     * @param ElementInterface $element
     * @return $this
     */
    protected function cacheByString(ElementInterface $element)
    {

        /** @var BaseElement $element */

        $stringValue = $this->stringValue($element);

        if (null === $stringValue) {
            return $this;
        }

        // Resolve siteId
        $siteId = SiteHelper::resolveSiteId($element->siteId);

        // Check if already in cache
        if ($stringValue && !$this->isCachedByString($stringValue, $siteId)) {

            // Cache it
            $this->_cacheByString[$siteId][$stringValue] = $element;

        }

        return $this;

    }




    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @param null $string
     * @throws ElementNotFoundException
     */
    protected function notFoundByStringException($string = null)
    {

        throw new ElementNotFoundException(
            sprintf(
                'Element does not exist with the string "%s".',
                (string)$string
            )
        );

    }

}
