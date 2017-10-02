<?php

namespace flipbox\spark\actions\element;

use Craft;
use flipbox\spark\actions\traits\PrepareData;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\QueryInterface;

abstract class ElementIndex extends AbstractElement
{
    use PrepareData;

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
        $dataProvider = $this->assembleDataProvider();

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

        Craft::$app->getResponse()->setStatusCode($this->statusCodeSuccess);

        return $dataProvider;
    }

    /**
     * @param array $config
     * @return DataProviderInterface
     */
    protected function assembleDataProvider(array $config = []): DataProviderInterface
    {
        return new ActiveDataProvider([
            'query' => $this->assembleQuery($config)
        ]);
    }
}
