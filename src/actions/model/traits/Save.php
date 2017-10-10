<?php

namespace flipbox\spark\actions\model\traits;

use flipbox\spark\actions\traits\Populate;
use yii\base\Model;

trait Save
{
    use Populate, Manage;

    /**
     * @param Model $model
     * @return mixed
     */
    public function runInternal(Model $model)
    {
        // Check access
        if (($access = $this->checkAccess($model)) !== true) {
            return $access;
        }

        if (!$this->performAction(
            $this->populate($model)
        )) {
            return $this->handleFailResponse($model);
        }

        return $this->handleSuccessResponse($model);
    }
}
