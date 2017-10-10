<?php

namespace flipbox\spark\actions\model\traits;

use Craft;
use yii\base\Model;
use yii\web\HttpException;
use yii\web\Response;

trait Lookup
{
    /**
     * @param int $id
     * @return null|Model
     */
    abstract protected function findById(int $id);

    /**
     * @param Model $model
     * @return Model|Response
     */
    abstract public function runInternal(Model $model);

    /**
     * @param int $id
     * @return Model|Response
     */
    public function run(int $id)
    {
        if (!$object = $this->findById($id)) {
            return $this->handleNotFoundResponse();
        }

        return $this->runInternal($object);
    }

    /**
     * @return string
     */
    protected function messageNotFound(): string
    {
        return Craft::t('app', 'Unable to find object.');
    }

    /**
     * HTTP not found response code
     *
     * @return int
     */
    protected function statusCodeNotFound(): int
    {
        return 404;
    }

    /**
     * @return null
     * @throws HttpException
     */
    protected function handleNotFoundResponse()
    {
        throw new HttpException(
            $this->statusCodeNotFound(),
            $this->messageNotFound()
        );
    }
}
