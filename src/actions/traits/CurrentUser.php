<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\actions\traits;

use Craft;
use craft\elements\User as UserElement;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CurrentUser
{
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
        if (($currentUser = $this->findUser()) === null) {
            return $this->handleUserNotFoundResponse();
        };

        return $currentUser;
    }

    /**
     * HTTP forbidden response code
     *
     * @return int
     */
    protected function statusCodeUserNotFound(): int
    {
        return 401;
    }

    /**
     * @return string
     */
    protected function messageUserNotFound(): string
    {
        return Craft::t('app', 'Unable to establish identity.');
    }

    /**
     * @throws HttpException
     * @return mixed
     */
    protected function handleUserNotFoundResponse()
    {
        throw new HttpException(
            $this->statusCodeUserNotFound(),
            $this->messageUserNotFound()
        );
    }
}
