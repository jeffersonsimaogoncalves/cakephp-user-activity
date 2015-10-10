<?php

if (!function_exists('log_activity')) {

    /**
     * log[table_name, action, created_by, primary_key, description]
     * @param array $log
     * @return void, if cannot write to logs table, log file will be used
     * @throws \Cake\Database\Exception
     */
    function log_activity(array $log) {
        if (empty($log)) {
            return;
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
            \Cake\Log\Log::alert(__('Cannot write user login activity to logs: {0}', implode(',', $log)))
        }
    }

}