<?php

namespace flipbox\spark\actions\traits;

use Craft;

trait Populate
{
    /**
     * These are the default body params that we're accepting.  You can lock down specific fact attributes this way.
     *
     * @return array
     */
    abstract protected function validBodyParams(): array;

    /**
     * @param $object
     * @return $object
     */
    protected function populate($object)
    {
        // Valid attribute values
        $attributes = $this->attributeValuesFromBody();

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
}
