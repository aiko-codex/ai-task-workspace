<?php
/** @var array $event_summary */
/** @var bool $show_email */
/** @var bool $show_whatsapp */
?>
<section class="sr-card srp-section">
    <div class="srp-section-head">
        <h2 class="sr-card-title">By event</h2>
        <span class="sr-hint"><?php echo count($event_summary); ?> event<?php echo count($event_summary) == 1 ? '' : 's'; ?> with messages</span>
    </div>

    <?php if (empty($event_summary)) { ?>
    <div class="sr-empty">
        <div class="sr-empty-icon"><i class="sr-symbol sr-symbol-lg" aria-hidden="true">inbox</i></div>
        <p class="sr-empty-title">Nothing sent yet</p>
        <p class="sr-empty-text">No emails or WhatsApp messages match these filters.</p>
    </div>
    <?php } else { ?>
    <div class="srp-tablewrap">
        <table class="srp-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <?php if ($show_email) { ?>
                    <th class="num">Emails sent</th>
                    <th class="num">Emails failed</th>
                    <?php } ?>
                    <?php if ($show_whatsapp) { ?>
                    <th class="num">WhatsApp sent</th>
                    <th class="num">WhatsApp failed</th>
                    <?php } ?>
                    <th>Last sent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($event_summary as $event_row) { ?>
                <tr>
                    <td class="srp-strong"><?php echo htmlspecialchars($event_row['event_title']); ?></td>
                    <?php if ($show_email) { ?>
                    <td class="num"><?php echo srp_number($event_row['email_sent']); ?></td>
                    <td class="num <?php echo $event_row['email_failed'] ? 'srp-bad' : 'srp-muted'; ?>"><?php echo srp_number($event_row['email_failed']); ?></td>
                    <?php } ?>
                    <?php if ($show_whatsapp) { ?>
                    <td class="num"><?php echo srp_number($event_row['whatsapp_sent']); ?></td>
                    <td class="num <?php echo $event_row['whatsapp_failed'] ? 'srp-bad' : 'srp-muted'; ?>"><?php echo srp_number($event_row['whatsapp_failed']); ?></td>
                    <?php } ?>
                    <td class="srp-muted"><?php echo srp_format_time($event_row['last_at']); ?></td>
                    <td class="num">
                        <button type="button" class="sr-btn sr-btn-ghost srp-pick-event" data-event-id="<?php echo (int) $event_row['event_id']; ?>">View</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</section>
