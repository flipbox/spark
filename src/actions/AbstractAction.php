<?php

namespace flipbox\spark\actions;

use Craft;
use craft\elements\User as UserElement;
use flipbox\spark\actions\traits\CheckAccess;
use yii\base\Action;
use yii\web\HttpException;

abstract class AbstractAction extends Action
{
    use CheckAccess;

    /**
     * HTTP success response code
     *
     * @var int
     */
    public $statusCodeSuccess = 200;

    /**
     * HTTP fail response code
     *
     * @var int
     */
    public $statusCodeFail = 400;

    /**
     * @return UserElement
     */
    protected function findUser()
    {
        return Craft::$app->getUser()->getIdentity();
    }

    /**
     * @return UserElement
     */
    protected function getUser()
    {
        $currentUser = $this->findUser();

        if (null === $currentUser) {
            return $this->handleUserNotFoundResponse();
        }

        return $currentUser;
    }

    /**
     * @return null
     */
    protected function handleUserNotFoundResponse()
    {
        return $this->handleUnauthorizedResponse();
    }

    /**
     * @throws HttpException
     */
    protected function handleUnauthorizedResponse()
    {
        throw new HttpException(
            $this->statusCodeUnauthorized,
            'Unable to perform action.'
        );
    }
}
