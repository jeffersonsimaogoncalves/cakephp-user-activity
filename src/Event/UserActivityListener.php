<?php
/**
 * Crabstudio(tm): Cake UserActivity logging plugin (http://github.com/crabstudio/UserActivity)
 * Copyright (c) Crabstudio. (http://crabstudio.info)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crabstudio. (http://crabstudio.info)
 * @link          http://github.com/crabstudio/backend Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace UserActivity\Event;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Event\EventListenerInterface;
use Cake\Controller\Component\AuthComponent;
use Cake\ORM\TableRegistry;

/**
 * User Activity Listener set created_by, modified_by when beforeSave event was fired
 * Write to logs when afterSaveCommit, afterDeleteCommit event was fired
 */
class UserActivityListener implements EventListenerInterface {

    protected $auth;

    public function __construct(AuthComponent $auth) {
        $this->auth = $auth;
    }

    public function implementedEvents() {
        return [
            'Model.beforeSave' => [
                'callable' => 'beforeSave',
                'priority' => -100
            ],
            'Model.afterSaveCommit' => [
                'callable' => 'afterSaveCommit',
                'priority' => -100
            ],
            'Model.afterDeleteCommit' => [
                'callable' => 'afterDeleteCommit',
                'priority' => -100
            ]
        ];
    }

    /**
     * Do set created_by and modified_by
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     */
    public function beforeSave(Event $event, Entity $entity, ArrayObject $options) {
        if($entity->isNew()) {
            $entity->set('created_by', $this->auth->user('id'));
        } else {
            $entity->set('modified_by', $this->auth->user('id'));
        }
    }

    /**
     * 
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     * @throws \Cake\Database\Exception
     */
    public function afterSaveCommit(Event $event, Entity $entity, ArrayObject $options) {
        $Logs = TableRegistry::get('Logs');
        $listField = [];
        /**
         * Log all visible properties
         */
        if (!empty($entity->visibleProperties())) {
            foreach ($entity->visibleProperties() as $key => $property) {
                if ($entity->getOriginal($property) === $entity->get($property) && !$entity->isNew()) {
                    continue;
                }
                $field = $Logs->LogsDetails->newEntity();
                $field->field_name = $property;
                $field->new_value = $entity->get($property);
                $field->old_value = $entity->isNew() ? null : $entity->getOriginal($property);
                array_push($listField, $field);
            }
        }
        /**
         * Log all invisible properties
         */
        if (!empty($entity->hiddenProperties())) {
            foreach ($entity->hiddenProperties() as $key => $property) {
                if ($entity->getOriginal($property) === $entity->get($property) && !$entity->isNew()) {
                    continue;
                }
                $entity->get($property);
                $field = $Logs->LogsDetails->newEntity();
                $field->field_name = $property;
                $field->new_value = $entity->get($property);
                $field->old_value = $entity->isNew() ? null : $entity->getOriginal($property);
                array_push($listField, $field);
            }
        }
        $log = $Logs->newEntity();
        $log->table_name = $entity->source();
        $log->action = $entity->isNew() ? 'C' : 'U';
        $log->created_by = $this->auth->user('id');
        $log->logs_details = $listField;
        $log->deleted = false;
        $log->description = __('User {0} was {1} successfully', $this->auth->user('email'), $entity->isNew() ? __('created') : __('updated') );
        if (!$Logs->save($log)) {
            throw new \Cake\Database\Exception('Cannot log user activity');
        }
    }

    /**
     * 
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     */
    public function afterDeleteCommit(Event $event, Entity $entity, ArrayObject $options) {
        $Logs = TableRegistry::get('Logs');
        $listField = [];
        /**
         * Log all visible properties
         */
        if (!empty($entity->visibleProperties())) {
            foreach ($entity->visibleProperties() as $key => $property) {
                $field = $Logs->LogsDetails->newEntity();
                $field->field_name = $property;
                $field->new_value = null;
                $field->old_value = $entity->isNew() ? null : $entity->get($property);
                array_push($listField, $field);
            }
        }
        /**
         * Log all invisible properties
         */
        if (!empty($entity->hiddenProperties())) {
            foreach ($entity->hiddenProperties() as $key => $property) {
                $field = $Logs->LogsDetails->newEntity();
                $field->field_name = $property;
                $field->new_value = null;
                $field->old_value = $entity->isNew() ? null : $entity->get($property);
                array_push($listField, $field);
            }
        }
        $log = $Logs->newEntity();
        $log->table_name = $entity->source();
        $log->action = 'D';
        $log->created_by = $this->auth->user('id');
        $log->logs_details = $listField;
        $log->description = __('User {0} was soft deleted successfully', $this->auth->user('email'));
        if (!$Logs->save($log)) {
            throw new \Cake\Database\Exception('Cannot log activity');
        }
    }

}
