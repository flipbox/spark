<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models;

use Craft;
use craft\events\ModelEvent as ModelSaveEvent;
use craft\validators\DateTimeValidator;
use DateTime;
use flipbox\spark\helpers\ModelHelper;
use flipbox\spark\models\traits\DateCreatedAttribute;
use flipbox\spark\models\traits\DateUpdatedAttribute;
use yii\base\Model as BaseModel;
use yii\base\ModelEvent as ModelDeleteEvent;

/**
 * @property DateTime $dateCreated
 * @property DateTime $dateUpdated
 *
 * @package flipbox\spark\models
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Model extends BaseModel
{

    use DateCreatedAttribute, DateUpdatedAttribute;

    /**
     * @event ModelEvent an event. You may set
     * [[ModelEvent::isValid]] to be false to stop the save.
     */
    const EVENT_BEFORE_SAVE = 'beforeSave';

    /**
     * @event ModelEvent an event. You may set
     * [[ModelEvent::isValid]] to be false to stop the save.
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    /**
     * @event ModelEvent an event. You may set
     * [[ModelEvent::isValid]] to be false to stop the deletion.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * @event ModelEvent an event. You may set
     * [[ModelEvent::isValid]] to be false to stop the deletion.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';


    /**
     * @var string
     */
    public $uid;

    /**
     * @var integer
     */
    public $id;

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'id'
                    ],
                    'number',
                    'integerOnly' => true
                ],
                [
                    [
                        'dateCreated',
                        'dateUpdated'
                    ],
                    DateTimeValidator::class
                ],
                [
                    [

                        'id',
                        'uid',
                        'dateCreated',
                        'dateUpdated'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {

        return array_merge(
            parent::attributes(),
            [
                'dateCreated',
                'dateUpdated'
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return array_merge(
            parent::attributeLabels(),
            [
                'id' => Craft::t('app', 'ID'),
                'uid' => Craft::t('app', 'UID'),
                'dateCreated' => Craft::t('app', 'Date Created'),
                'dateUpdated' => Craft::t('app', 'Date Updated')
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function fields()
    {

        return array_merge(
            parent::fields(),
            [
                'dateCreated' => 'dateCreatedIso8601',
                'dateUpdated' => 'dateUpdatedIso8601'
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete(ModelDeleteEvent $event): bool
    {
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete(ModelDeleteEvent $event): bool
    {
        $this->trigger(self::EVENT_AFTER_DELETE, $event);
        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave(ModelSaveEvent $event): bool
    {
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);
        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function afterSave(ModelSaveEvent $event): bool
    {
        $this->trigger(self::EVENT_AFTER_SAVE, $event);
        return $event->isValid;
    }

}
