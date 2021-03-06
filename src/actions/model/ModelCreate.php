<?php

namespace flipbox\spark\actions\model;

use yii\base\Action;
use yii\base\Model;
use yii\web\Response;

abstract class ModelCreate extends Action
{
    use traits\Save;

    /**
     * @param array $config
     * @return Model
     */
    abstract protected function newModel(array $config = []): Model;

    /**
     * @return Model|null|Response
     */
    public function run()
    {
        return $this->runInternal($this->newModel());
    }

    /**
     * @inheritdoc
     */
    public function statusCodeSuccess(): int
    {
        return 201;
    }
}
