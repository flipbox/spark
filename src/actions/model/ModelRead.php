<?php

namespace flipbox\spark\actions\model;

use flipbox\spark\actions\base\traits\ViewAction;
use flipbox\spark\models\Model;
use yii\base\Action;

abstract class ModelRead extends Action
{
    use ViewAction;

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

        return $model;
    }
}
