<?php

namespace flipbox\spark\actions\element;

use Craft;
use craft\base\ElementInterface;

abstract class ElementDelete extends AbstractLookup
{
    /**
     * HTTP success response code
     *
     * @var int
     */
    public $statusCodeSuccess = 204;

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function performAction(ElementInterface $element): ElementInterface
    {
        if (!Craft::$app->getElements()->deleteElement(
            $element
        )) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }
}
