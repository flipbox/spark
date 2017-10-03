<?php

namespace flipbox\spark\actions\element\traits;

use Craft;
use yii\base\Model;

trait Lookup
{
    /**
     * @param int $id
     * @return null|Model
     */
    protected function findById(int $id)
    {
        Craft::$app->getElements()->getElementById($id);
    }
}
