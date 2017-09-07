<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models\traits;

use craft\helpers\DateTimeHelper;
use DateTime;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait DateUpdatedAttribute
{

    private $_dateUpdated;

    /**
     * @param $value
     * @return $this
     */
    public function setDateUpdated($value)
    {

        if ($value) {
            $value = DateTimeHelper::toDateTime($value);
        }

        $this->_dateUpdated = $value ?: null;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateUpdated()
    {

        if (empty($this->_dateUpdated)) {
            return DateTimeHelper::toDateTime(
                new DateTime('now')
            );
        }

        return $this->_dateUpdated;
    }

    /**
     * @return string|null
     */
    public function getDateUpdatedIso8601()
    {

        // Get the datetime
        if (!$dateCreated = $this->getDateUpdated()) {
            return null;
        }

        // Convert it to iso
        if (!$iso = DateTimeHelper::toIso8601($dateCreated)) {
            return null;
        }

        return $iso;
    }
}
