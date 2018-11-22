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


namespace JeffersonSimaoGoncalves\UserActivity\Controller;

use JeffersonSimaoGoncalves\UserActivity\Event\UserActivityListener;

/**
 * Trait UserActivityTrait
 *
 * @author Jefferson Simão Gonçalves <gerson.simao.92@gmail.com>
 *
 * @mixin \Cake\Controller\Controller
 *
 * @package JeffersonSimaoGoncalves\UserActivity\Controller
 */
trait UserActivityTrait
{
    /**
     * @param string|null $modelClass
     * @param string|null $modelType
     *
     * @return \Cake\ORM\Table
     */
    public function loadModel($modelClass = null, $modelType = null)
    {
        /** @var \Cake\ORM\Table $model */
        $model = parent::loadModel($modelClass, $modelType);
        $model->getEventManager()->on(new UserActivityListener($this->Auth));

        return $model;
    }

}
