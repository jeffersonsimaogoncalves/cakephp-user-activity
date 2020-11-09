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

namespace JeffersonSimaoGoncalves\UserActivity\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use JeffersonSimaoGoncalves\UserActivity\Model\Entity\Log;

/**
 * Logs Model
 *
 * @property \JeffersonSimaoGoncalves\UserActivity\Model\Table\LogsDetailsTable|\Cake\ORM\Association\HasMany $LogsDetails
 *
 * @method Log get($primaryKey, $options = [])
 * @method Log newEntity($data = null, array $options = [])
 * @method Log[] newEntities(array $data, array $options = [])
 * @method Log|bool save(EntityInterface $entity, $options = [])
 * @method Log|bool saveOrFail(EntityInterface $entity, $options = [])
 * @method Log patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Log[] patchEntities($entities, array $data, array $options = [])
 * @method Log findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogsTable extends Table
{

    /**
     * set connection name
     *
     * @return string
     */
    public static function defaultConnectionName(): string
    {
        $connection = Configure::read('JeffersonSimaoGoncalves/UserActivity.connection');
        if (!empty($connection)) {
            return $connection;
        }

        return parent::defaultConnectionName();
    }

    /**
     * Initialize method
     *
     * @param  array  $config  The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('LogsDetails', [
            'foreignKey' => 'log_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
            'className' => 'JeffersonSimaoGoncalves/UserActivity.LogsDetails',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param  \Cake\Validation\Validator  $validator  Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('table_name', 'create')
            ->notEmptyString('table_name');

        $validator
            ->allowEmptyString('database_name');

        $validator
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->add('recycle', 'valid', ['rule' => 'boolean'])
            ->requirePresence('recycle', 'create')
            ->notEmptyString('recycle');

        $validator
            ->allowEmptyString('created_by');

        $validator
            ->allowEmptyString('name');

        return $validator;
    }

    /**
     * Find latest logs
     *
     * @param  Query  $query
     * @param  array  $options  limit default 100 record
     *
     * @return \Cake\ORM\Query
     */
    public function findLatest(Query $query, array $options)
    {
        return $query->order(['created' => 'DESC'])->limit(isset($options['limit']) ?: 100);
    }

}
