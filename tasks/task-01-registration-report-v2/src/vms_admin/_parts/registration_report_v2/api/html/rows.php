<?php

/**
 * Endpoint to render table rows <tbody> HTML.
 * Invoked by JS on initial load, filter changes, and pagination.
 */

require_once __DIR__ . '/../../lib/api_boot.php';

$rows = rr_fetch_rows($connection, $filters);

if (empty($rows)) {
    ?>
    <tr class="rr-empty-row">
        <td colspan="9" class="rr-empty-cell">
            <div class="rr-empty-state">
                <i class="sr-symbol rr-empty-icon" aria-hidden="true">search_off</i>
                <h3 class="rr-empty-title">No matching registrations found</h3>
                <p class="rr-empty-desc">Try clearing your search or adjusting filters to see results.</p>
                <button type="button" class="sr-btn sr-btn-secondary rr-empty-reset" onclick="window.rrApp && window.rrApp.resetFilters()">
                    Reset filters
                </button>
            </div>
        </td>
    </tr>
    <?php
    exit;
}

$startIndex = ($filters['last_id'] > 0) ? 0 : 1;
$counter = $startIndex;

foreach ($rows as $index => $visitorItem) {
    $rowNumber = ($startIndex > 0) ? $counter++ : $visitorItem['auto_id'];
    include __DIR__ . '/../../components/table_row/table_row.php';
}
