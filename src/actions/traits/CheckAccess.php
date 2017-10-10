<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\actions\traits;

use Craft;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CheckAccess
{
    /**
     * @var null|callable
     */
    public $checkAccess = null;

    /**
     * @param array ...$params
     * @return mixed
     */
    public function checkAccess(...$params)
    {
        if ($this->checkAccess) {
            if (call_user_func_array($this->checkAccess, $params) === false) {
                return $this->handleUnauthorizedResponse();
            };
        }

        return true;
    }

    /**
     * HTTP forbidden response code
     *
     * @return int
     */
    protected function statusCodeUnauthorized(): int
    {
        return 403;
    }

    /**
     * @return string
     */
    protected function messageUnauthorized(): string
    {
        return Craft::t('app', 'Unable to perform action.');
    }

    /**
     * @throws HttpException
     */
    protected function handleUnauthorizedResponse()
    {
        throw new HttpException(
            $this->statusCodeUnauthorized(),
            $this->messageUnauthorized()
        );
    }
}
