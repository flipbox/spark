<?php

namespace flipbox\spark\actions\base\traits;

use Craft;
use yii\base\Model;
use yii\web\HttpException;
use yii\web\Response;

trait Lookup
{
    /**
     * @var string
     */
    public $messageNotFound = 'Unable to find object.';

    /**
     * HTTP not found response code
     *
     * @var int
     */
    public $statusCodeNotFound = 404;

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
     * @return null
     * @throws HttpException
     */
    protected function handleNotFoundResponse()
    {
        throw new HttpException(
            $this->statusCodeNotFound,
            Craft::t(
                'restful',
                $this->messageNotFound
            )
        );
    }
}
