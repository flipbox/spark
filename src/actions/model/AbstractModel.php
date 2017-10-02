<?php

namespace flipbox\spark\actions\model;

use Craft;
use flipbox\spark\actions\AbstractAction;
use yii\base\Action;
use yii\base\Model;

abstract class AbstractModel extends Action
{
    /**
     * @param Model $model
     * @return Model
     */
    protected function handleSuccessResponse(Model $model): Model
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess);
        return $model;
    }

    /**
     * @param Model $model
     * @return Model
     */
    protected function handleFailResponse(Model $model): Model
    {
        Craft::$app->getResponse()->setStatusCode($this->statusCodeFail);
        return $model;
    }
}
