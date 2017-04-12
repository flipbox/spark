<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\services\traits;

use craft\base\ElementInterface;
use flipbox\spark\behaviors\ElementRecordAccessor as ElementRecordAccessorBehavior;
use flipbox\spark\records\Record;

/**
 * @method void transferToRecord(ElementInterface $element, Record $record, bool $mirrorScenario = true)
 * @method Record toRecord(ElementInterface $element, bool $mirrorScenario = true)
 *
 * @package flipbox\spark\services\traits
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ElementRecordBehavior
{

    use ObjectRecordBehavior;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'record' => [
                'class' => ElementRecordAccessorBehavior::class,
                'record' => static::recordClass()
            ]
        ];
    }
}