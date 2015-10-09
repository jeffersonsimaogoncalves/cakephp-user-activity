<?php

if (!function_exists('log_activity')) {

    /**
     * log[table_name, action, created_by, primary_key, description]
     * @param array $log
     * @return boolean success
     * @throws \Cake\Database\Exception
     */
    function log_activity(array $log) {
        if (empty($log)) {
            return false;
        }
        $Logs = Cake\ORM\TableRegistry::get('Logs');
        $logObj = $Logs->newEntity();
        $logObj->table_name = $log['table_name'];
        $logObj->action = $log['action'];
        $logObj->created_by = $log['created_by'];
        $logObj->recycle = false;
        $logObj->primary_key = $log['primary_key'];
        $logObj->description = $log['description'];
        if (!$Logs->save($logObj)) {
            return false;
        }
        return true;
    }

}