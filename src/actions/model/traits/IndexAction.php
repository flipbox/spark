<?php

namespace flipbox\spark\actions\model\traits;

use flipbox\spark\actions\traits\CheckAccess;
use flipbox\spark\actions\traits\PrepareData;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\QueryInterface;

trait IndexAction
{
    use PrepareData, CheckAccess;

    /**
     * @param array $config
     * @return QueryInterface
     */
    abstract protected function assembleQuery(array $config = []): QueryInterface;

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
     * @param DataProviderInterface $dataProvider
     * @return DataProviderInterface
     */
    protected function runInternal(DataProviderInterface $dataProvider): DataProviderInterface
    {
        // Check access
        if (($access = $this->checkAccess($dataProvider)) !== true) {
            return $access;
        }

        return $this->performAction($dataProvider);
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return DataProviderInterface
     */
    protected function performAction(DataProviderInterface $dataProvider): DataProviderInterface
    {
        // Allow alterations to the data
        $this->prepareData($dataProvider);

        return $dataProvider;
    }

    /**
     * @param array $config
     * @return DataProviderInterface
     */
    protected function assembleDataProvider(array $config = []): DataProviderInterface
    {
        return new ActiveDataProvider([
            'query' => $this->assembleQuery(
                $this->normalizeQueryConfig($config)
            )
        ]);
    }

    /**
     * @param array $config
     * @return array
     */
    protected function normalizeQueryConfig(array $config = []): array
    {
        return $config;
    }
}
