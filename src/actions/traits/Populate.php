<?php

namespace flipbox\spark\actions\traits;

use Craft;
use yii\base\Model;

trait Populate
{
    /**
     * @param Model $model
     * @return Model
     */
    protected function populate(Model $model)
    {
        // Valid attribute values
        $attributes = $this->attributeValuesFromBody();

        /** @var Model $model */
        $model = Craft::configure(
            $model,
            $attributes
        );

        return $model;
    }

    /**
     * @return array
     */
    protected function attributeValuesFromBody(): array
    {
        $request = Craft::$app->getRequest();

        $attributes = [];
        foreach ($this->validBodyParams() as $bodyParam => $attribute) {
            if (is_numeric($bodyParam)) {
                $bodyParam = $attribute;
            }
            if (($value = $request->getBodyParam($bodyParam)) !== null) {
                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }

    /**
     * These are the default body params that we're accepting.  You can lock down specific fact attributes this way.
     *
     * @return array
     */
    abstract protected function validBodyParams(): array;
}
