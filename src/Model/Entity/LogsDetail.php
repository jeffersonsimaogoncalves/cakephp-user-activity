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
 
namespace UserActivity\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogsDetail Entity.
 *
 * @property int $id
 * @property int $log_id
 * @property \App\Model\Entity\Log $log
 * @property string $object_file
 * @property string $field_name
 * @property string $new_value
 * @property string $old_value
 * @property \Cake\I18n\Time $created
 */
class LogsDetail extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
