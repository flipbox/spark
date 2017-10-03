<?php

namespace flipbox\spark\actions\model;

use yii\base\Action;

abstract class ModelUpdate extends Action
{
    use traits\SaveAction, traits\Lookup;
}
