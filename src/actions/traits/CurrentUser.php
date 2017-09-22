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
     * @var int
     */
    public $accessCodeUserNotFound = 401;

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
     * @throws HttpException
     * @return mixed
     */
    protected function handleUserNotFoundResponse()
    {
        throw new HttpException(
            $this->accessCodeUserNotFound,
            'Unable to establish identity.'
        );
    }
}
