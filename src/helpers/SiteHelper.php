<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\helpers;

use Craft;

/**
 * @package flipbox\spark\helpers
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SiteHelper
{

    /**
     * @param int|null $siteId
     * @return int
     */
    public static function resolveSiteId(int $siteId = null): int
    {

        if (is_null($siteId)) {

            $siteId = Craft::$app->getSites()->currentSite->id;

        }

        return $siteId;

    }

}


