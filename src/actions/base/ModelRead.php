<?php

namespace flipbox\spark\actions\base;

use yii\base\Action;

abstract class ModelRead extends Action
{
    use traits\ViewAction, traits\Lookup;
}
