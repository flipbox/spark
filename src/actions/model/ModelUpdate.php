<?php

namespace flipbox\spark\actions\model;

use flipbox\spark\actions\traits\Lookup;
use flipbox\spark\actions\base\traits\SaveAction;
use yii\base\Action;

abstract class ModelUpdate extends Action
{
    use SaveAction, Lookup;
}
