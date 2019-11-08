<?php
/**
 * User: Jefferson Simão Gonçalves
 * Email: gerson.simao.92@gmail.com
 * Date: 23/11/2018
 * Time: 13:39
 */

namespace JeffersonSimaoGoncalves\UserActivity\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Class UserActivityBehavior
 *
 * @author Jefferson Simão Gonçalves <gerson.simao.92@gmail.com>
 *
 * @package JeffersonSimaoGoncalves\UserActivity\Model\Behavior
 */
class UserActivityBehavior extends Behavior
{
    /**
     * @var array
     */
    private $ignoreFields = [
        'created',
        'modified',
        'created_by',
        'modified_by',
    ];

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * Do set created_by and modified_by
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
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
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     *
     * @throws \Cake\Database\Exception
     */
    public function afterSave(Event $event, Entity $entity, ArrayObject $options)
    {
        TableRegistry::getTableLocator()
            ->remove('JeffersonSimaoGoncalves/UserActivity.Logs');
        TableRegistry::getTableLocator()
            ->remove('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
        /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsTable $Logs */
        $Logs = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
        /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsDetailsTable $LogsDetails */
        $LogsDetails = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
        $listField = [];
        $tableEntity = TableRegistry::getTableLocator()->get($entity->getSource());
        $fields = $tableEntity->getSchema()->columns();
        $primaryKey = $tableEntity->getSchema()->primaryKey();
        $primary_key = [];
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
                    if ($tableEntity->getSchema()->getColumnType($property) === 'json') {
                        $field->new_value = json_encode($entity->get($property));
                    } else {
                        $field->new_value = $entity->get($property);
                    }
                    if ($tableEntity->getSchema()->getColumnType($property) === 'json') {
                        $field->old_value = $entity->isNew() ? null : json_encode($entity->get($property));
                    } else {
                        $field->old_value = $entity->isNew() ? null : $entity->get($property);
                    }
                    array_push($listField, $field);
                }
            }
        }

        foreach ($primaryKey as $pk) {
            $primary_key[$pk] = $entity->get($pk);
        }

        $configLog = $Logs->getConnection()->config();
        $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())->getConnection()->config();

        $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

        $log = $Logs->newEntity();
        $log->table_name = $entity->getSource();
        $log->database_name = $database;
        $log->primary_key = json_encode($primary_key);
        $log->action = $entity->isNew() ? 'C' : 'U';
        $log->created_by = $this->getId();
        $log->name = $this->getName();
        $log->recycle = false;
        $log->description = __('{0} um registro em {1} com sucesso', $entity->isNew() ? __('Criado') : __('Atualizado'), $entity->getSource());

        if ($Logs->save($log)) {
            foreach ($listField as $field) {
                /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail $field */
                $field->log_id = $log->id;
                $LogsDetails->save($field);
            }
        }
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
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        TableRegistry::getTableLocator()
            ->remove('JeffersonSimaoGoncalves/UserActivity.Logs');
        TableRegistry::getTableLocator()
            ->remove('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
        /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsTable $Logs */
        $Logs = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
        /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsDetailsTable $LogsDetails */
        $LogsDetails = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.LogsDetails');
        $listField = [];
        $tableEntity = TableRegistry::getTableLocator()->get($entity->getSource());
        $fields = $tableEntity->getSchema()->columns();
        $primaryKey = $tableEntity->getSchema()->primaryKey();
        $primary_key = [];
        /**
         * Log all visible properties
         */
        if (count($fields) > 0) {
            foreach ($fields as $key => $property) {
                if (!in_array($property, $this->ignoreFields)) {
                    $field = $LogsDetails->newEntity();
                    $field->field_name = $property;
                    $field->new_value = null;
                    if ($tableEntity->getSchema()->getColumnType($property) === 'json') {
                        $field->old_value = $entity->isNew() ? null : json_encode($entity->get($property));
                    } else {
                        $field->old_value = $entity->isNew() ? null : $entity->get($property);
                    }
                    array_push($listField, $field);
                }
            }
        }

        foreach ($primaryKey as $pk) {
            $primary_key[$pk] = $entity->get($pk);
        }

        $configLog = $Logs->getConnection()->config();
        $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())
            ->getConnection()
            ->config();

        $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

        $log = $Logs->newEntity();
        $log->table_name = $entity->getSource();
        $log->database_name = $database;
        $log->action = 'D';
        $log->created_by = $this->getId();
        $log->name = $this->getName();
        $log->recycle = true;
        $log->primary_key = json_encode($primary_key);
        $log->description = __('Registro excluído temporário {0} com êxito', $entity->getSource());
        if ($Logs->save($log)) {
            foreach ($listField as $field) {
                /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail $field */
                $field->log_id = $log->id;
                $LogsDetails->save($field);
            }
        }
    }
}
