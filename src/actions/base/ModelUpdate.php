<?php

namespace flipbox\spark\actions\base;

use flipbox\spark\actions\model\traits\Lookup;
use yii\base\Action;

abstract class ModelUpdate extends Action
{
    use traits\SaveAction, Lookup;
}
