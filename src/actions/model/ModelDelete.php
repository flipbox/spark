<?php

namespace flipbox\spark\actions\model;

use flipbox\spark\actions\base\traits\DeleteAction;
use flipbox\spark\actions\traits\Lookup;
use yii\base\Action;

abstract class ModelDelete extends Action
{
    use DeleteAction, Lookup;
}
