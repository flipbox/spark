<?php

namespace flipbox\spark\actions\model;

use yii\base\Action;

abstract class ModelDelete extends Action
{
    use traits\Delete, traits\Lookup;
}
