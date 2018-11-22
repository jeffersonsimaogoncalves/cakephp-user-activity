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
use Cake\Database\Schema\TableSchema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogsDetails Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Logs
 *
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail get($primaryKey, $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail newEntity($data = null, array $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail[] newEntities(array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail[] patchEntities($entities, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogsDetailsTable extends Table
{

    /**
     * set connection name
     *
     * @return string
     */
    public static function defaultConnectionName()
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
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('logs_details');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Logs', [
            'foreignKey' => 'log_id',
            'joinType'   => 'INNER',
            'className'  => 'JeffersonSimaoGoncalves/UserActivity.Logs',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('object_file');

        $validator
            ->allowEmpty('field_name');

        $validator
            ->allowEmpty('new_value');

        $validator
            ->allowEmpty('old_value');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['log_id'], 'Logs'));

        return $rules;
    }

    /**
     * @param \Cake\Database\Schema\TableSchema $schema
     *
     * @return \Cake\Database\Schema\TableSchema
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('new_value', 'json');
        $schema->setColumnType('old_value', 'json');

        return parent::_initializeSchema($schema);
    }
}
