<?php
/** @var array $totals */
/** @var bool $show_email */
/** @var bool $show_whatsapp */
?>
<div class="srp-cards">
    <div class="srp-card">
        <span class="srp-card-icon"><i class="sr-symbol" aria-hidden="true">forward_to_inbox</i></span>
        <span class="srp-card-label">Messages sent</span>
        <span class="srp-card-value"><?php echo srp_number($totals['email_sent'] + $totals['whatsapp_sent']); ?></span>
        <span class="srp-card-note">Success rate <?php echo srp_success_rate($totals); ?></span>
    </div>

    <?php if ($show_email) { ?>
    <div class="srp-card">
        <span class="srp-card-icon srp-card-icon-email"><i class="sr-symbol" aria-hidden="true">mail</i></span>
        <span class="srp-card-label">Emails sent</span>
        <span class="srp-card-value"><?php echo srp_number($totals['email_sent']); ?></span>
        <span class="srp-card-note <?php echo $totals['email_failed'] ? 'srp-card-note-bad' : ''; ?>"><?php echo srp_number($totals['email_failed']); ?> failed</span>
    </div>
    <?php } ?>

    <?php if ($show_whatsapp) { ?>
    <div class="srp-card">
        <span class="srp-card-icon srp-card-icon-whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
        <span class="srp-card-label">WhatsApp sent</span>
        <span class="srp-card-value"><?php echo srp_number($totals['whatsapp_sent']); ?></span>
        <span class="srp-card-note <?php echo $totals['whatsapp_failed'] ? 'srp-card-note-bad' : ''; ?>"><?php echo srp_number($totals['whatsapp_failed']); ?> failed</span>
    </div>
    <?php } ?>

    <div class="srp-card">
        <span class="srp-card-icon srp-card-icon-failed"><i class="sr-symbol" aria-hidden="true">error</i></span>
        <span class="srp-card-label">Failed</span>
        <span class="srp-card-value"><?php echo srp_number($totals['email_failed'] + $totals['whatsapp_failed']); ?></span>
        <span class="srp-card-note">Last sent <?php echo srp_format_time($totals['last_at']); ?></span>
    </div>
</div>
