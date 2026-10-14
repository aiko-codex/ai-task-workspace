<?php

require_once __DIR__ . '/../../lib/api_boot.php';

$totals        = srp_totals($connection, $filters, $user_events);
$show_email    = $filters['channel'] != 'whatsapp';
$show_whatsapp = $filters['channel'] != 'email';

include __DIR__ . '/../../components/summary/cards.php';
