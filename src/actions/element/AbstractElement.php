<?php

namespace flipbox\spark\actions\element;

use Craft;
use craft\base\ElementInterface;
use flipbox\spark\actions\AbstractAction;

abstract class AbstractElement extends AbstractAction
{
    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleSuccessResponse(ElementInterface $element): ElementInterface
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess);
        return $element;
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleFailResponse(ElementInterface $element): ElementInterface
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeFail);
        return $element;
    }
}
