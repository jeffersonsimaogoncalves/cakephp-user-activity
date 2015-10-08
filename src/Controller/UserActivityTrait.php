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

namespace UserActivity\Controller;

use Ace\Event\UserActivityListener;

/**
 * Register User Activity Listener
 */
trait UserActivityTrait {

    /**
     * 
     * @param type $modelClass
     * @param type $type
     * @return model
     */
    public function loadModel($modelClass = null, $type = 'Table') {
        $model = parent::loadModel($modelClass, $type);
        $model->eventManager()->on(new UserActivityListener($this->Auth));
        return $model;
    }

}
