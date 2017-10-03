<?php

namespace flipbox\spark\actions\base;

use craft\helpers\ArrayHelper;
use flipbox\spark\actions\base\traits\IndexAction;
use yii\base\Action;
use yii\data\DataProviderInterface;

abstract class ElementIndex extends Action
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

    /**
     * @param array $config
     * @return array
     */
    protected function normalizeQueryConfig(array $config = []): array
    {
        // OrderBy should be an array, not an empty string (which is set in the default element query)
        $config['orderBy'] = ArrayHelper::getValue($config, 'orderBy', []);
        return $config;
    }
}
