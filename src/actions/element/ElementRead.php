<?php

namespace flipbox\spark\actions\element;

use craft\base\ElementInterface;

abstract class ElementRead extends AbstractLookup
{
    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function performAction(ElementInterface $element): ElementInterface
    {
        return $this->handleSuccessResponse($element);
    }
}
