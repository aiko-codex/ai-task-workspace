<?php

/**
 * Summary cards component for Registration Report v2.
 * Matches Figma 58:4865 structure and typography.
 */

// Expects $summary = array('total_matching' => ..., 'paid_count' => ..., 'unpaid_count' => ..., 'total_types' => ...)
$totalMatching = isset($summary['total_matching']) ? number_format($summary['total_matching']) : '—';
$paidCount     = isset($summary['paid_count']) ? number_format($summary['paid_count']) : '—';
$unpaidCount   = isset($summary['unpaid_count']) ? number_format($summary['unpaid_count']) : '—';
$totalTypes    = isset($summary['total_types']) ? number_format($summary['total_types']) : '—';
?>
<section class="rr-summary-cards" id="rr-summary-cards" aria-label="Registration Statistics">
    <div class="sr-card rr-stat-card" id="rr-stat-total">
        <span class="rr-stat-label">Matching registrations</span>
        <span class="rr-stat-value" id="rr-val-total"><?php echo htmlspecialchars($totalMatching); ?></span>
        <span class="rr-stat-sub">Within applied filters</span>
    </div>

    <div class="sr-card rr-stat-card" id="rr-stat-paid">
        <span class="rr-stat-label">Paid</span>
        <span class="rr-stat-value" id="rr-val-paid"><?php echo htmlspecialchars($paidCount); ?></span>
        <span class="rr-stat-sub">Within applied filters</span>
    </div>

    <div class="sr-card rr-stat-card" id="rr-stat-unpaid">
        <span class="rr-stat-label">Unpaid</span>
        <span class="rr-stat-value" id="rr-val-unpaid"><?php echo htmlspecialchars($unpaidCount); ?></span>
        <span class="rr-stat-sub">Within applied filters</span>
    </div>

    <div class="sr-card rr-stat-card" id="rr-stat-types">
        <span class="rr-stat-label">Registration types</span>
        <span class="rr-stat-value" id="rr-val-types"><?php echo htmlspecialchars($totalTypes); ?></span>
        <span class="rr-stat-sub">Configured for this event</span>
    </div>
</section>
