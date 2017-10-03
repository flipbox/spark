<?php

namespace flipbox\spark\actions\base;

use yii\base\Action;

abstract class ModelUpdate extends Action
{
    use traits\SaveAction, traits\Lookup;
}
