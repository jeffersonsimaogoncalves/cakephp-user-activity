<?php

use Cake\Core\Configure;
use Cake\Log\Log;

// Optionally load additional queue config defaults from local app config
if (file_exists(ROOT.DS.'config'.DS.'app_user_activity.php')) {
    Configure::load('app_user_activity');
}

if (!function_exists('log_activity')) {
    /**
     * @param  array  $log
     */
    function log_activity(array $log)
    {
        if (empty($log)) {
            return;
        }
        $Logs = Cake\ORM\TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/UserActivity.Logs');
        /** @var \JeffersonSimaoGoncalves\UserActivity\Model\Entity\Log $logObj */
        $logObj = $Logs->newEmptyEntity();
        $logObj->table_name = $log['table_name'];
        $logObj->action = $log['action'];
        $logObj->created_by = $log['created_by'];
        $logObj->recycle = false;
        $logObj->primary_key = $log['primary_key'];
        $logObj->description = $log['description'];
        if (!$Logs->save($logObj)) {
            Log::alert(__('Cannot write user login activity to logs: {0}', implode(',', $log)));
        }
    }
}
