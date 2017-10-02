<?php

namespace flipbox\spark\actions\element\traits;

use Craft;
use craft\base\ElementInterface;
use flipbox\spark\actions\traits\Populate as AbstractPopulate;

trait Populate
{
    use AbstractPopulate;

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function populate(ElementInterface $element): ElementInterface
    {
        // Valid attribute values
        $attributes = $this->attributeValuesFromBody();

        /** @var ElementInterface $element */
        $element = Craft::configure(
            $element,
            $attributes
        );

        return $element;
    }
}
