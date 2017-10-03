<?php

namespace flipbox\spark\actions\model;

use yii\base\Action;
use yii\data\DataProviderInterface;

abstract class ModelIndex extends Action
{
    use traits\IndexAction;

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
