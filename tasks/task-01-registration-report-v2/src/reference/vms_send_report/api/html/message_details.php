<?php

require_once __DIR__ . '/../../lib/api_boot.php';
require_once __DIR__ . '/../../lib/wa_preview.php';

$channel = srp_request('channel');
$message = srp_message_row($connection, $channel, (int) srp_request('id'), $user_events);

if (!$message) {
    http_response_code(404);
    exit('This message was not found or is not from one of your events.');
}

$sections   = srp_detail_sections($message);
$wa_preview = $channel == 'whatsapp' ? srp_wa_preview($message) : null;

include __DIR__ . '/../../components/message_details/message_details.php';
