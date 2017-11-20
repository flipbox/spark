<?php

namespace flipbox\spark\actions\traits;

use Craft;
use yii\base\BaseObject;

trait Populate
{
    /**
     * @param BaseObject $object
     * @return BaseObject
     */
    protected function populate(BaseObject $object): BaseObject
    {
        // Valid attribute values
        $attributes = $this->attributeValuesFromBody();

        /** @var BaseObject $object */
        $object = Craft::configure(
            $object,
            $attributes
        );

        return $object;
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
