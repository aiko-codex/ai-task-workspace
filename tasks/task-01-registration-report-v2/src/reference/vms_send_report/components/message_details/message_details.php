<?php
/** @var array $message */
/** @var array $sections */
/** @var string $channel */
/** @var array|null $wa_preview */
?>
<div class="sr-drawer-body srp-details">
    <div class="srp-details-top">
        <?php if ($channel == 'email') { ?>
        <span class="srp-channel srp-channel-email"><i class="sr-symbol" aria-hidden="true">mail</i> Email</span>
        <?php } else { ?>
        <span class="srp-channel srp-channel-whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</span>
        <?php } ?>
        <?php if ($message['status'] == 'sent') { ?>
        <span class="sr-chip sr-chip-success">Sent</span>
        <?php } else { ?>
        <span class="sr-chip sr-chip-danger">Failed</span>
        <?php } ?>
        <span class="sr-hint">#<?php echo (int) $message['id']; ?> · <?php echo srp_format_time($message['created_at']); ?></span>
    </div>

    <?php if ($wa_preview) { ?>
    <div class="srp-details-block">
        <p class="sr-label">Message preview</p>
        <div class="srp-wa-bubble">
            <?php if ($wa_preview['image_url'] != '') { ?>
            <a href="<?php echo htmlspecialchars($wa_preview['image_url']); ?>" target="_blank" rel="noopener">
                <img class="srp-wa-img" src="<?php echo htmlspecialchars($wa_preview['image_url']); ?>" alt="Image sent with the message">
            </a>
            <?php } ?>
            <div><?php echo nl2br(preg_replace('/\*([^*\n]+)\*/', '<strong>$1</strong>', htmlspecialchars($wa_preview['text']))); ?></div>
            <?php if ($wa_preview['footer'] != '') { ?>
            <div class="srp-wa-foot"><?php echo htmlspecialchars($wa_preview['footer']); ?></div>
            <?php } ?>
        </div>
    </div>
    <?php } elseif ($channel == 'whatsapp') { ?>
    <div class="srp-details-block">
        <p class="sr-label">Message preview</p>
        <div class="srp-wa-bubble srp-wa-no-preview">
            <i class="sr-symbol sr-symbol-lg" aria-hidden="true">visibility_off</i>
            <span>No preview for this message. The values sent are in the request payload below.</span>
        </div>
    </div>
    <?php } ?>

    <dl class="srp-details-list">
        <?php foreach ($sections['fields'] as $field) { ?>
        <dt><?php echo htmlspecialchars($field['label']); ?></dt>
        <dd><?php echo htmlspecialchars($field['value']); ?></dd>
        <?php } ?>
    </dl>

    <?php foreach ($sections['blocks'] as $block) { ?>
    <div class="srp-details-block">
        <p class="sr-label"><?php echo htmlspecialchars($block['label']); ?></p>
        <pre><?php echo htmlspecialchars($block['value']); ?></pre>
    </div>
    <?php } ?>
</div>
