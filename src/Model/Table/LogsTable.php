<?php
/**
 * Crabstudio(tm): Cake bake ACE template (http://github.com/crabstudio/backend)
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

namespace UserActivity\Model\Table;

use UserActivity\Model\Entity\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Logs Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Transactions
 * @property \Cake\ORM\Association\BelongsTo $Products
 */
class LogsTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('logs');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('LogsDetails', [
            'foreignKey' => 'log_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsTo('Transactions', [
            'foreignKey' => 'transaction_id'
        ]);
        $this->belongsTo('Products', [
            'foreignKey' => 'product_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create');

        $validator
                ->requirePresence('table_name', 'create')
                ->notEmpty('table_name');

        $validator
                ->requirePresence('action', 'create')
                ->notEmpty('action');

        $validator
                ->add('created_by', 'valid', ['rule' => 'numeric'])
                ->requirePresence('created_by', 'create')
                ->notEmpty('created_by');

        $validator
                ->allowEmpty('memberid');

        $validator
                ->allowEmpty('operation_type');

        $validator
                ->requirePresence('description', 'create')
                ->notEmpty('description');

        $validator
                ->add('recycle', 'valid', ['rule' => 'boolean'])
                ->requirePresence('recycle', 'create')
                ->notEmpty('recycle');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['transaction_id'], 'Transactions'));
        $rules->add($rules->existsIn(['product_id'], 'Products'));
        return $rules;
    }

    /**
     * Find latest logs
     * @param Query $query
     * @param array $options limit default 100 record
     */
    public function findLatest(Query $query, array $options) {
        return $query->order(['created' => 'DESC'])->limit(isset($options['limit'])? : 100);
    }

}
