<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services;

use Craft;
use craft\base\Element as BaseElement;
use craft\base\ElementInterface;
use craft\elements\db\ElementQueryInterface;
use flipbox\spark\exceptions\ElementNotFoundException;
use flipbox\spark\helpers\ElementHelper;
use flipbox\spark\helpers\QueryHelper;
use flipbox\spark\helpers\SiteHelper;
use yii\base\Component as BaseComponent;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Element extends BaseComponent
{

    /**
     * @var [ElementInterface[]]
     */
    protected $cacheById = [];

    /*******************************************
     * ELEMENT CLASSES
     *******************************************/

    /**
     * @return string
     */
    abstract public static function elementClass(): string;

    /**
     * The element instance that this class interacts with
     *
     * @return string
     */
    public static function elementClassInstance(): string
    {
        return ElementInterface::class;
    }

    /*******************************************
     * CREATE
     *******************************************/

    /**
     * @param array $config
     * @return BaseElement|ElementInterface
     */
    public function create(array $config = [])
    {

        // Set the class the element should be
        $config['class'] = static::elementClass();

        // Create new model
        return ElementHelper::create(
            $config,
            static::elementClassInstance()
        );
    }


    /**
     * @param $identifier
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function find($identifier, int $siteId = null)
    {

        if ($identifier instanceof ElementInterface) {
            $this->addToCache($identifier);

            return $identifier;
        } elseif (is_numeric($identifier)) {
            return $this->findById($identifier, $siteId);
        } elseif (is_array($identifier)) {
            return $this->getQuery($identifier)
                ->siteId($siteId)
                ->one();
        }

        return null;
    }

    /**
     * @param int $id
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function findById(int $id, int $siteId = null)
    {

        // Check cache
        if (!$element = $this->findCacheById($id, $siteId)) {
            // Find new element
            if ($element = $this->freshFindById($id, $siteId)) {
                // Cache it
                $this->addToCache($element);
            } else {
                // Cache nothing
                $this->cacheById[$id] = $element;
            }
        }

        return $element;
    }

    /*******************************************
     * GET
     *******************************************/

    /**
     * @param $identifier
     * @param int|null $siteId
     * @return BaseElement|ElementInterface
     * @throws ElementNotFoundException
     */
    public function get($identifier, int $siteId = null)
    {

        // Find
        if (!$element = $this->find($identifier, $siteId)) {
            $this->notFoundException();
        }

        return $element;
    }

    /**
     * @param int $id
     * @param int|null $siteId
     * @return BaseElement|ElementInterface
     * @throws ElementNotFoundException
     */
    public function getById(int $id, int $siteId = null)
    {

        // Find by ID
        if (!$element = $this->findById($id, $siteId)) {
            $this->notFoundByIdException($id);
        }

        return $element;
    }

    /*******************************************
     * FRESH FIND
     *******************************************/

    /**
     * @param int $id
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function freshFindById(int $id, int $siteId = null)
    {
        return Craft::$app->getElements()->getElementById($id, static::elementClass(), $siteId);
    }

    /**
     * @param $id
     * @param int|null $siteId
     * @return BaseElement|ElementInterface
     * @throws ElementNotFoundException
     */
    public function freshGetById($id, int $siteId = null)
    {

        if (!$element = $this->freshFindById($id, $siteId)) {
            $this->notFoundByIdException($id);
        }

        return $element;
    }


    /*******************************************
     * QUERY
     *******************************************/

    /**
     * Get query
     *
     * @param $criteria
     * @return ElementQueryInterface
     */
    public function getQuery($criteria = [])
    {

        /** @var ElementInterface $elementClass */
        $elementClass = static::elementClass();

        /** @var ElementQueryInterface $query */
        $query = $elementClass::find();

        // Configure it
        QueryHelper::configure(
            $query,
            $criteria
        );

        return $query;
    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @param $identifier
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function findCache($identifier, int $siteId = null)
    {

        if (is_numeric($identifier)) {
            return $this->findCacheById($identifier, $siteId);
        }

        return null;
    }

    /**
     * @param ElementInterface $element
     * @return $this
     */
    public function addToCache(ElementInterface $element)
    {

        $this->cacheById($element);

        return $this;
    }

    /**
     * Find an existing cache by ID
     *
     * @param int $id
     * @param int|null $siteId
     * @return BaseElement|ElementInterface|null
     */
    public function findCacheById(int $id, int $siteId = null)
    {

        // Resolve siteId
        $siteId = SiteHelper::resolveSiteId($siteId);

        // Check if already in addToCache
        if ($this->isCachedById($id, $siteId)) {
            return $this->cacheById[$siteId][$id];
        }

        return null;
    }

    /**
     * Identify whether in cached by ID
     *
     * @param int $id
     * @param int|null $siteId
     * @return bool
     */
    protected function isCachedById(int $id, int $siteId = null)
    {
        // Resolve siteId
        $siteId = SiteHelper::resolveSiteId($siteId);

        if (!isset($this->cacheById[$siteId])) {
            $this->cacheById[$siteId] = [];
        }

        return array_key_exists($id, $this->cacheById[$siteId]);
    }

    /**
     * @param ElementInterface $element
     * @return $this
     */
    protected function cacheById(ElementInterface $element)
    {

        /** @var BaseElement $element */

        $id = $element->id;

        $siteId = $element->siteId;

        // Check if already in cache
        if (!$this->isCachedById($id, $siteId)) {
            // Cache it
            $this->cacheById[$siteId][$id] = $element;
        }

        return $this;
    }

    /*******************************************
     * EXCEPTIONS
     *******************************************/

    /**
     * @throws ElementNotFoundException
     */
    protected function notFoundException()
    {

        throw new ElementNotFoundException(
            sprintf(
                "Element does not exist."
            )
        );
    }

    /**
     * @param int|null $id
     * @throws ElementNotFoundException
     */
    protected function notFoundByIdException(int $id = null)
    {

        throw new ElementNotFoundException(
            sprintf(
                'Element does not exist with the id "%s".',
                (string)$id
            )
        );
    }
}
