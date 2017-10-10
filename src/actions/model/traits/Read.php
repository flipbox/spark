<?php

namespace flipbox\spark\actions\model\traits;

use flipbox\spark\actions\traits\CheckAccess;
use yii\base\Model;

trait Read
{
    use CheckAccess;

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
