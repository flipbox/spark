<?php

namespace flipbox\spark\actions\model;

use flipbox\spark\actions\base\traits\IndexAction;
use yii\data\DataProviderInterface;

abstract class ModelIndex extends AbstractModel
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
