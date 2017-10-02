<?php

namespace flipbox\spark\actions\base;

use Craft;
use craft\base\ElementInterface;

abstract class AbstractAction extends \flipbox\spark\actions\AbstractAction
{
    /**
     * @param $object
     * @return mixed
     */
    protected function handleSuccessResponse($object)
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess);
        return $object;
    }

    /**
     * @param $object
     * @return ElementInterface
     */
    protected function handleFailResponse($object): ElementInterface
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeFail);
        return $object;
    }
}
