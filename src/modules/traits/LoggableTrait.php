<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\modules\traits;

use Craft;
use flipbox\spark\helpers\LoggingHelper;
use yii\log\Dispatcher;
use yii\log\Logger;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait LoggableTrait
{

    private static $_dispatcher;

    /**
     * @inheritdoc
     */
    public function isDebugModeEnabled()
    {
        return false;
    }

    /**
     * @return Logger
     */
    public static function getLogger()
    {
        return static::getDispatcher()->getLogger();
    }

    /**
     * Sets the logger object.
     * @param Logger $logger the logger object.
     */
    public static function setLogger($logger)
    {
        static::getDispatcher()->setLogger($logger);
    }

    /**
     * @return Dispatcher
     */
    public static function getDispatcher()
    {
        if (self::$_dispatcher !== null) {
            return self::$_dispatcher;
        } else {
            static::setDispatcher();
            return static::$_dispatcher;
        }
    }

    /**
     * @param array $dispatcher
     * @throws \yii\base\InvalidConfigException
     */
    public static function setDispatcher($dispatcher = [])
    {

        if (!$dispatcher instanceof Dispatcher) {
            $dispatcher = Craft::createObject(
                LoggingHelper::getDispatchDefinition(
                    static::getInstance(),
                    $dispatcher
                )
            );
        }

        self::$_dispatcher = $dispatcher;

    }

    /**
     * Logs a trace message.
     * Trace messages are logged mainly for development purpose to see
     * the execution work flow of some code.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function trace($message, $category = 'module')
    {
        static::getLogger()->log($message, Logger::LEVEL_TRACE, $category);
    }

    /**
     * Logs an error message.
     * An error message is typically logged when an unrecoverable error occurs
     * during the execution of an application.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function error($message, $category = 'module')
    {
        static::getLogger()->log($message, Logger::LEVEL_ERROR, $category);
    }

    /**
     * Logs a warning message.
     * A warning message is typically logged when an error occurs while the execution
     * can still continue.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function warning($message, $category = 'module')
    {
        static::getLogger()->log($message, Logger::LEVEL_WARNING, $category);
    }

    /**
     * Logs an informative message.
     * An informative message is typically logged by an application to keep record of
     * something important (e.g. an administrator logs in).
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function info($message, $category = 'module')
    {
        static::getLogger()->log($message, Logger::LEVEL_INFO, $category);
    }

}
