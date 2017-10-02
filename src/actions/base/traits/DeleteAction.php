<?php

namespace flipbox\spark\actions\base\traits;

use Craft;
use flipbox\spark\actions\traits\CheckAccess;
use yii\base\Model;

trait DeleteAction
{
    use CheckAccess;

    /**
     * HTTP success response code
     *
     * @var int
     */
    public $statusCodeSuccess = 204;

    /**
     * HTTP fail response code
     *
     * @var int
     */
    public $statusCodeFail = 401;

    /**
     * @param Model $model
     * @return Model
     */
    protected function runInternal(Model $model)
    {
        // Check access
        if (($access = $this->checkAccess($model)) !== true) {
            return $access;
        }

        if (!$this->performAction($model)) {
            return $this->handleFailResponse($model);
        }

        return $this->handleSuccessResponse($model);
    }

    /**
     * @param Model $model
     * @return bool
     */
    abstract protected function performAction(Model $model): bool;

    /**
     * @param Model $model
     * @return Model
     */
    protected function handleSuccessResponse(Model $model)
    {
        // Success status code
        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess);
        return $model;
    }

    /**
     * @param Model $model
     * @return Model
     */
    protected function handleFailResponse(Model $model)
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeFail);
        return $model;
    }
}
