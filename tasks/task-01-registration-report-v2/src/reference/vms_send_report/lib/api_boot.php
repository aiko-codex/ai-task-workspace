<?php

require_once __DIR__ . '/../../../../connection.php';
require_once __DIR__ . '/../helper.php';
require_once __DIR__ . '/report.php';

date_default_timezone_set('Asia/Kolkata');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
if ($user_id == '') {
    http_response_code(401);
    exit('Your session has ended. Please log in again.');
}

$user_events = srp_user_events($connection, $user_id);
$filters     = srp_filters_from_request($user_events);
