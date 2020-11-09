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

namespace JeffersonSimaoGoncalves\UserActivity\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity.
 *
 * @property int $id
 * @property string $table_name
 * @property string $database_name
 * @property string $primary_key
 * @property string $action
 * @property \Cake\I18n\Time $created
 * @property string $operation_type
 * @property string $description
 * @property string $created_by
 * @property string $name
 * @property bool $recycle
 * @property \JeffersonSimaoGoncalves\UserActivity\Model\Entity\LogsDetail[] $logs_details
 */
class Log extends Entity
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
