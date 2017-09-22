<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/guardian/license
 * @link       https://www.flipboxfactory.com/software/guardian/
 */

namespace flipbox\spark\filters;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\guardian\views\ViewInterface;
use yii\base\ActionFilter;
use yii\base\Exception;
use yii\base\Model;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Controller $sender
 */
class ModelFilter extends ActionFilter
{
    /**
     * The success handler key
     */
    const SUCCESS_KEY = 'success';

    /**
     * The fail handler key
     */
    const FAIL_KEY = 'fail';

    /**
     * @var array this property defines the transformers for each action.
     * Each action that should only support one transformer.
     *
     * You can use `'*'` to stand for all actions. When an action is explicitly
     * specified, it takes precedence over the specification given by `'*'`.
     *
     * For example,
     *
     * ```php
     * [
     *   'create' => $handler,
     *   'update' => $handler,
     *   'delete' => $handler,
     *   '*' => $handler
     * ]
     * ```
     */
    public $actions = [];

    /**
     * @var array
     */
    public $handler = [
        self::SUCCESS_KEY => 'Successfully performed action',
        self::FAIL_KEY => 'Unable to perform action'
    ];

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return null|Model|Response
     */
    public function afterAction($action, $result)
    {
        if (Craft::$app->getResponse()->format === Response::FORMAT_RAW) {
            return $result = $this->handleModel($result);
        }
        return $result;
    }

    /**
     * @param Model $model
     * @return Model|null|Response
     */
    protected function handleModel(Model $model)
    {
        if (!$handler = $this->findHandler()) {
            return $model;
        }

        if($model->hasErrors()) {
            Craft::$app->getSession()->setError(
                $this->resolveFailMessage($handler)
            );

            return null;
        }

        Craft::$app->getSession()->setNotice(
            $this->resolveSuccessMessage($handler)
        );

        $this->sender->redirectToPostedUrl($model);
    }

    /**
     * @param array $handler
     * @return mixed
     */
    private function resolveSuccessMessage(array $handler)
    {
        return ArrayHelper::getValue(
            $handler,
            self::SUCCESS_KEY
        );
    }

    /**
     * @param array $handler
     * @return mixed
     */
    private function resolveFailMessage(array $handler)
    {
        return ArrayHelper::getValue(
            $handler,
            self::FAIL_KEY
        );
    }

    /**
     * @return ViewInterface|array|null
     * @throws Exception
     */
    protected function findHandler()
    {
        // The requested action
        $action = Craft::$app->requestedAction->id;

        // Default
        $handler = $this->handler;

        // Look for definitions
        if (isset($this->actions[$action])) {
            $handler = array_merge(
                $handler,
                $this->actions[$action]
            );
        } elseif (isset($this->actions['*'])) {
            $handler = array_merge(
                $handler,
                $this->actions['*']
            );
        }

        return $handler;
    }
}
