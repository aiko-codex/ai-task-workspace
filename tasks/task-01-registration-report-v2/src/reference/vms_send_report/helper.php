<?php

/**
 * Parts helper for vms_send_report.
 * - plog()          logs into this folder's logs/<caller>.log
 * - error_log(), PHP warnings and fatals also land in this folder's logs/
 * - DOMAIN_BASE_URL, redirect_user(), asset_url() come from root helper.php
 */

require_once __DIR__ . '/../../../clog.php';

$_parts_log_dir = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
if (!is_dir($_parts_log_dir)) {
    @mkdir($_parts_log_dir, 0755, true);
}
$_parts_main_script = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__;
$GLOBALS['_clog_main_log_file'] = $_parts_log_dir . basename($_parts_main_script, '.php') . '.log';
ini_set('error_log', $GLOBALS['_clog_main_log_file']);

require_once __DIR__ . '/../../../helper.php';

if (!function_exists('plog')) {
    function plog($message, $level = 'INFO')
    {
        $backtrace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $callerFile = isset($backtrace[0]['file']) ? $backtrace[0]['file'] : 'unknown';
        $callerLine = isset($backtrace[0]['line']) ? $backtrace[0]['line'] : 'unknown';

        $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $date  = date('Y-m-d H:i:s');
        $ip    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'CLI';
        $level = strtoupper($level);

        $logEntry = '[' . $date . '] [' . $ip . '] [' . $level . '] [' . basename($callerFile) . ':' . $callerLine . '] ' . $message . PHP_EOL;

        @file_put_contents($logDir . basename($callerFile, '.php') . '.log', $logEntry, FILE_APPEND | LOCK_EX);
    }
}
