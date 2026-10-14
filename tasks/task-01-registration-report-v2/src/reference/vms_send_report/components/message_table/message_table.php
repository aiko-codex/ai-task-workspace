<?php
/** @var array $message_page */
/** @var array $user_events */
/** @var bool $show_event */
?>
<div class="srp-section-head">
    <h2 class="sr-card-title">Messages</h2>
    <span class="sr-hint">
        <?php if ($message_page['total']) { ?>
        Showing <?php echo srp_number($message_page['first_row']); ?>–<?php echo srp_number($message_page['last_row']); ?> of <?php echo srp_number($message_page['total']); ?>
        <?php } ?>
    </span>
</div>

<?php if (empty($message_page['rows'])) { ?>
<div class="sr-empty">
    <div class="sr-empty-icon"><i class="sr-symbol sr-symbol-lg" aria-hidden="true">search_off</i></div>
    <p class="sr-empty-title">No messages</p>
    <p class="sr-empty-text">Try a different event, date range or search.</p>
</div>
<?php } else { ?>
<div class="srp-tablewrap">
    <table class="srp-table">
        <thead>
            <tr>
                <th>Sent at</th>
                <th>Channel</th>
                <?php if ($show_event) { ?>
                <th>Event</th>
                <?php } ?>
                <th>To</th>
                <th>Subject / template</th>
                <th>Source</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($message_page['rows'] as $message) { ?>
            <?php $event_id = (int) $message['event_id']; ?>
            <tr>
                <td class="srp-muted srp-nowrap"><?php echo srp_format_time($message['created_at']); ?></td>
                <td>
                    <?php if ($message['channel'] == 'email') { ?>
                    <span class="srp-channel srp-channel-email"><i class="sr-symbol" aria-hidden="true">mail</i> Email</span>
                    <?php } else { ?>
                    <span class="srp-channel srp-channel-whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</span>
                    <?php } ?>
                </td>
                <?php if ($show_event) { ?>
                <td class="srp-clip"><?php echo htmlspecialchars(isset($user_events[$event_id]) ? $user_events[$event_id] : ''); ?></td>
                <?php } ?>
                <td class="srp-strong srp-clip"><?php echo htmlspecialchars($message['recipient']); ?></td>
                <td class="srp-clip" title="<?php echo htmlspecialchars($message['title']); ?>"><?php echo htmlspecialchars($message['title']); ?></td>
                <td class="srp-muted srp-clip"><?php echo htmlspecialchars(srp_source_label($message['source'])); ?></td>
                <td>
                    <?php if ($message['status'] == 'sent') { ?>
                    <span class="sr-chip sr-chip-success">Sent</span>
                    <?php } else { ?>
                    <span class="sr-chip sr-chip-danger" title="<?php echo htmlspecialchars($message['error_message']); ?>">Failed</span>
                    <?php } ?>
                </td>
                <td class="num">
                    <button type="button" class="sr-btn sr-btn-secondary srp-details-btn"
                        data-channel="<?php echo $message['channel']; ?>" data-id="<?php echo (int) $message['id']; ?>">
                        <i class="sr-symbol" aria-hidden="true">visibility</i> Details
                    </button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($message_page['page_count'] > 1) { ?>
<div class="srp-pager">
    <button type="button" class="sr-btn sr-btn-secondary srp-page" data-page="<?php echo $message_page['page'] - 1; ?>" <?php echo $message_page['page'] <= 1 ? 'disabled' : ''; ?>>
        <i class="sr-symbol" aria-hidden="true">chevron_left</i> Previous
    </button>
    <span class="sr-hint">Page <?php echo $message_page['page']; ?> of <?php echo $message_page['page_count']; ?></span>
    <button type="button" class="sr-btn sr-btn-secondary srp-page" data-page="<?php echo $message_page['page'] + 1; ?>" <?php echo $message_page['page'] >= $message_page['page_count'] ? 'disabled' : ''; ?>>
        Next <i class="sr-symbol" aria-hidden="true">chevron_right</i>
    </button>
</div>
<?php } ?>
<?php } ?>
