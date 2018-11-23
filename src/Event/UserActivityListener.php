<?php

namespace JeffersonSimaoGoncalves\UserActivity\Event;

use ArrayObject;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Class UserActivityListener
 *
 * User Activity Listener set created_by, modified_by when beforeSave event was fired
 * Write to logs when afterSaveCommit, afterDeleteCommit event was fired
 *
 * @author Jefferson Simão Gonçalves <gerson.simao.92@gmail.com>
 *
 * @package JeffersonSimaoGoncalves\UserActivity\Event
 */
class UserActivityListener implements EventListenerInterface
{
    private $ignoreClass = [
        'DatabaseLog\\Model\\Entity\\DatabaseLog',
        'JeffersonSimaoGoncalves\\UserActivity\\Model\\Entity\\Log',
        'JeffersonSimaoGoncalves\\UserActivity\\Model\\Entity\\LogsDetail',
        'Settings\\Model\\Entity\\Configuration',
    ];
    private $ignoreFields = [
        'created',
        'modified',
        'created_by',
        'modified_by',
    ];

    public function implementedEvents()
    {
        return [
            'Model.beforeSave'        => [
                'callable' => 'beforeSave',
                'priority' => -100,
            ],
            'Model.afterSaveCommit'   => [
                'callable' => 'afterSaveCommit',
                'priority' => -100,
            ],
            'Model.afterDeleteCommit' => [
                'callable' => 'afterDeleteCommit',
                'priority' => -100,
            ],
        ];
    }

    /**
     * Do set created_by and modified_by
     *
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     */
    public function beforeSave(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $entity->set('created_by', $this->getId());
        } else {
            $entity->set('modified_by', $this->getId());
        }
    }

    /**
     * @return string|null
     */
    private function getId()
    {
        if (function_exists('getUserAuth')) {
            return getUserAuth('id');
        }

        return null;
    }

    /**
     *
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     *
     * @throws \Cake\Database\Exception
     */
    public function afterSaveCommit(Event $event, Entity $entity, ArrayObject $options)
    {
        $class = get_class($entity);

        if (!in_array($class, $this->ignoreClass)) {
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/UserActivity.Logs');
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
            /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsTable $Logs */
            $Logs = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
            /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsDetailsTable $LogsDetails */
            $LogsDetails = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
            $listField = [];
            $fields = TableRegistry::getTableLocator()->get($entity->getSource())->getSchema()->columns();
            /**
             * Log all visible properties
             */
            if (count($fields) > 0) {
                foreach ($fields as $key => $property) {
                    if (!in_array($property, $this->ignoreFields)) {
                        if ($entity->getOriginal($property) === $entity->get($property) && !$entity->isNew()) {
                            continue;
                        }
                        $field = $LogsDetails->newEntity();
                        $field->field_name = $property;
                        $field->new_value = $entity->get($property);
                        $field->old_value = $entity->isNew() ? null : $entity->getOriginal($property);
                        array_push($listField, $field);
                    }
                }
            }

            $configLog = $Logs->getConnection()->config();
            $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())->getConnection()->config();

            $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

            $log = $Logs->newEntity();
            $log->table_name = $entity->getSource();
            $log->database_name = $database;
            $log->primary_key = $entity->id;
            $log->action = $entity->isNew() ? 'C' : 'U';
            $log->created_by = $this->getId();
            $log->name = $this->getName();
            $log->recycle = false;
            $log->description = __('{0} a record in {1} successfully', $entity->isNew() ? __('Create') : __('Update'), $entity->getSource());

            if ($Logs->save($log)) {
                foreach ($listField as $field) {
                    /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail $field */
                    $field->log_id = $log->id;
                    $LogsDetails->save($field);
                }
            }
        }
        $event->stopPropagation();
    }

    /**
     * @return string|null
     */
    private function getName()
    {
        if (function_exists('getUserAuth')) {
            return getUserAuth('name');
        }

        return null;
    }

    /**
     *
     * @param Event $event
     * @param Entity $entity
     * @param ArrayObject $options
     */
    public function afterDeleteCommit(Event $event, Entity $entity, ArrayObject $options)
    {
        $class = get_class($entity);

        if (!in_array($class, $this->ignoreClass)) {
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/UserActivity.Logs');
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
            /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsTable $Logs */
            $Logs = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
            /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsDetailsTable $LogsDetails */
            $LogsDetails = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
            $listField = [];
            $fields = TableRegistry::getTableLocator()->get($entity->getSource())->getSchema()->columns();
            /**
             * Log all visible properties
             */
            if (count($fields) > 0) {
                foreach ($fields as $key => $property) {
                    if (!in_array($property, $this->ignoreFields)) {
                        $field = $LogsDetails->newEntity();
                        $field->field_name = $property;
                        $field->new_value = null;
                        $field->old_value = $entity->isNew() ? null : $entity->get($property);
                        array_push($listField, $field);
                    }
                }
            }

            $configLog = $Logs->getConnection()->config();
            $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())->getConnection()->config();

            $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

            $log = $Logs->newEntity();
            $log->table_name = $entity->getSource();
            $log->database_name = $database;
            $log->action = 'D';
            $log->created_by = $this->getId();
            $log->name = $this->getName();
            $log->recycle = true;
            $log->primary_key = $entity->id;
            $log->description = __('Temporary deleted record {0} successfully', $entity->getSource());
            if ($Logs->save($log)) {
                foreach ($listField as $field) {
                    /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail $field */
                    $field->log_id = $log->id;
                    $LogsDetails->save($field);
                }
            }
        }
        $event->stopPropagation();
    }

}
