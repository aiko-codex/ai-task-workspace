<?php

/**
 * Endpoint to render summary cards HTML.
 * Invoked by JS on filter changes.
 */

require_once __DIR__ . '/../../lib/api_boot.php';

$summary = rr_fetch_summary($connection, $filters);

include __DIR__ . '/../../components/summary_cards/summary_cards.php';
