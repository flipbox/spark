<?php

namespace flipbox\spark\actions\element;

use Craft;
use craft\base\ElementInterface;

abstract class ElementCreate extends AbstractElement
{
    use traits\Populate;

    /**
     * @inheritdoc
     */
    public $statusCodeSuccess = 201;

    /**
     * @param array $config
     * @return ElementInterface
     */
    abstract protected function newElement(array $config = []): ElementInterface;

    /**
     * @return ElementInterface
     */
    public function run(): ElementInterface
    {
        // Check access
        if (($access = $this->checkAccess()) !== true) {
            return $access;
        }

        return $this->performAction(
            $this->newElement()
        );
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function performAction(ElementInterface $element): ElementInterface
    {
        if (!Craft::$app->getElements()->saveElement(
            $this->populate($element)
        )) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }
}
