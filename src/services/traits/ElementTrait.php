<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\spark\records\Record;

/**
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
trait ElementTrait
{

    use ObjectTrait;

    /*******************************************
     * ELEMENT -to- RECORD
     *******************************************/

    /**
     * @param ElementInterface $element
     * @param Record $record
     * @param bool $mirrorScenario
     */
    public function transferToRecord(ElementInterface $element, Record $record, $mirrorScenario = true)
    {

        /** @var $element Element */

        if ($mirrorScenario === true) {

            // Mirror scenarios
            $record->setScenario($element->getScenario());

        }

        // Transfer attributes
        $record->setAttributes($element->toArray());

    }

    /**
     * @param ElementInterface $element
     * @param bool $mirrorScenario
     * @return Record
     */
    public function toRecord(ElementInterface $element, $mirrorScenario = true)
    {

        if ($id = $element->getId()) {

            $record = $this->findRecordByCondition(
                ['id' => $id]
            );

        }

        if (empty($record)) {

            // Create new record
            $record = $this->createRecord();

        }

        // Populate the record attributes
        $this->transferToRecord($element, $record, $mirrorScenario);

        return $record;

    }

}
