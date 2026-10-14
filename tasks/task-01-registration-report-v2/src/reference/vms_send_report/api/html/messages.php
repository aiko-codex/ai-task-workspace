<?php

require_once __DIR__ . '/../../lib/api_boot.php';

$message_page = srp_message_page($connection, $filters, $user_events);
$show_event   = $filters['event_id'] == 0;

include __DIR__ . '/../../components/message_table/message_table.php';
