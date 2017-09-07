<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class ModelWithHandle extends Model
{

    use traits\ModelWithHandle;

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return null === $this->handle;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            $this->handleRules()
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return array_merge(
            parent::attributeLabels(),
            $this->handleAttributeLabel()
        );
    }
}
