<?php

namespace flipbox\spark\actions\base;

use yii\base\Action;

abstract class ModelDelete extends Action
{
    use traits\DeleteAction, traits\Lookup;
}
