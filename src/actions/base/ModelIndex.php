<?php

namespace flipbox\spark\actions\base;

use flipbox\spark\actions\base\traits\IndexAction;
use yii\base\Action;
use yii\data\DataProviderInterface;

abstract class ModelIndex extends Action
{
    use IndexAction;

    /**
     * @return DataProviderInterface
     */
    public function run(): DataProviderInterface
    {
        return $this->runInternal(
            $this->assembleDataProvider()
        );
    }
}
