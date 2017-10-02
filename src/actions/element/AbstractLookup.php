<?php

namespace flipbox\spark\actions\element;

use Craft;
use craft\base\ElementInterface;
use yii\web\HttpException;
use yii\web\Response;

abstract class AbstractLookup extends AbstractElement
{
    /**
     * The message returned when an element could not be found.
     */
    const NOT_FOUND_MESSAGE = 'Unable to find element.';

    /**
     * HTTP not found response code
     *
     * @var int
     */
    public $statusCodeNotFound = 404;

    /**
     * @param ElementInterface $element
     * @return ElementInterface|null
     */
    abstract protected function performAction(ElementInterface $element);

    /**
     * @param int $id
     * @return ElementInterface|Response
     */
    public function run(int $id)
    {
        if (!$element = $this->getElementById($id)) {
            return $this->handleNotFoundResponse();
        }

        // Check access
        if (($access = $this->checkAccess($element)) !== true) {
            return $access;
        }

        return $this->performAction($element);
    }

    /**
     * @param int $id
     * @return ElementInterface|null
     */
    public function getElementById(int $id)
    {
        return Craft::$app->getElements()->getElementById($id);
    }

    /**
     * @return null
     * @throws HttpException
     */
    protected function handleNotFoundResponse()
    {
        throw new HttpException(
            $this->statusCodeNotFound,
            Craft::t(
                'restful',
                static::NOT_FOUND_MESSAGE
            )
        );
    }
}
