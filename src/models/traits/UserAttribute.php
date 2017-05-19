<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/spark/blob/master/LICENSE
 * @link       https://github.com/flipbox/spark
 */

namespace flipbox\spark\models\traits;

use Craft;
use craft\elements\User as UserElement;
use flipbox\spark\helpers\ModelHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UserTrait
{

    /**
     * @var int|null
     */
    private $_userId;

    /**
     * @var UserElement|null
     */
    private $_user;

    /**
     * Set associated userId
     *
     * @param $id
     * @return $this
     */
    public function setUserId(int $id)
    {

        // Has the id changed?
        if ($id !== $this->_userId) {

            // Invalidate existing user
            if ($this->_user !== null && $this->_user->getId() !== $id) {
                $this->_user = null;
            };

            $this->_userId = $id;

        }

        return $this;
    }

    /**
     * Get associated userId
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->_userId;
    }


    /**
     * Associate a user
     *
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {

        // Clear cache
        $this->_user = null;

        // Find element
        if (!$user = $this->findUserElement($user)) {

            // Clear property / cache
            $this->_userId = $this->_user = null;

        } else {

            // Set property
            $this->_userId = $user->getId();

            // Set cache
            $this->_user = $user;

        }

        return $this;

    }

    /**
     * @return UserElement|null
     */
    public function getUser()
    {

        // Check cache
        if (is_null($this->_user)) {

            // Check property
            if (!empty($this->_userId)) {

                // Find element
                if ($userElement = Craft::$app->getUsers()->getUserById($this->_userId)) {

                    // Set
                    $this->setUser($userElement);

                } else {

                    // Clear property (it's invalid)
                    $this->_userId = null;

                    // Prevent subsequent look-ups
                    $this->_user = false;

                }

            } else {

                // Prevent subsequent look-ups
                $this->_user = false;

            }

        }

        return !$this->_user ? null : $this->_user;

    }

    /**
     * @param $user
     * @return UserElement|null
     */
    private function findUserElement($user)
    {

        // Element
        if ($user instanceof UserElement) {

            return $user;

            // Id
        } elseif (is_numeric($user)) {

            return Craft::$app->getUsers()->getUserById($user);

            // Username / Email
        } elseif (!is_null($user)) {

            return Craft::$app->getUsers()->getUserByUsernameOrEmail($user);

        }

        return null;

    }

    /**
     * @return array
     */
    protected function userRules(): array
    {

        return [
            [
                [
                    'userId'
                ],
                'number',
                'integerOnly' => true
            ],
            [
                [
                    'userId',
                    'user'
                ],
                'safe',
                'on' => [
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];

    }

    /**
     * @return array
     */
    protected function userFields(): array
    {

        return [
            'userId'
        ];

    }

    /**
     * @return array
     */
    protected function userAttributes(): array
    {

        return [
            'userId'
        ];

    }

    /**
     * @return array
     */
    protected function userAttributeLabels(): array
    {

        return [
            'userId' => Craft::t('app', 'User Id')
        ];

    }

}
