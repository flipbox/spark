<?php

namespace flipbox\spark\actions\element;

use Craft;
use craft\base\ElementInterface;

class ElementUpdate extends AbstractLookup
{
    use traits\Populate;

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function performAction(ElementInterface $element): ElementInterface
    {
        if (!Craft::$app->getElements()->saveElement(
            $this->populate($element)
        )
        ) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }
}
