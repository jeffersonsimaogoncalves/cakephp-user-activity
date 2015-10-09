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
        $log = $Logs->newEntity();
        $log->table_name = $log['table_name'];
        $log->action = $log['action'];
        $log->created_by = $log['created_by'];
        $log->recycle = false;
        $log->primary_key = $log['primary_key'];
        $log->description = $log['description'];
        if (!$Logs->save($log)) {
            return false;
        }
        return true;
    }

}