<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use JeffersonSimaoGoncalves\UserActivity\Event\UserActivityListener;

// Optionally load additional queue config defaults from local app config
if (file_exists(ROOT . DS . 'config' . DS . 'app_user_activity.php')) {
    Configure::load('app_user_activity');
}

EventManager::instance()->on(new UserActivityListener());

if (!function_exists('log_activity')) {

    /**
     * @param array $log
     */
    function log_activity(array $log)
    {
        if (empty($log)) {
            return;
        }
        $Logs = Cake\ORM\TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
        $logObj = $Logs->newEntity();
        $logObj->table_name = $log['table_name'];
        $logObj->action = $log['action'];
        $logObj->created_by = $log['created_by'];
        $logObj->recycle = false;
        $logObj->primary_key = $log['primary_key'];
        $logObj->description = $log['description'];
        if (!$Logs->save($logObj)) {
            \Cake\Log\Log::alert(__('Cannot write user login activity to logs: {0}', implode(',', $log)));
        }
    }

}