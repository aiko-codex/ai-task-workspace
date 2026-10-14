<?php
include __DIR__ . '/header.php';

require_once __DIR__ . '/_parts/vms_send_report/helper.php';
require_once __DIR__ . '/_parts/vms_send_report/lib/report.php';

$user_events = srp_user_events($connection, $_SESSION['user_id']);
$filters     = srp_filters_from_request($user_events);
$sources     = srp_sources($connection, $user_events);

$shared = 'vms_admin/_parts/vms_send_reminder/';
$parts  = 'vms_admin/_parts/vms_send_report/';

$shared_styles = array(
    'css/tokens.css',
    'css/base.css',
    'css/ui/page.css',
    'css/ui/card.css',
    'css/ui/button.css',
    'css/ui/symbol.css',
    'css/ui/input.css',
    'css/ui/segmented.css',
    'css/ui/chip.css',
    'css/ui/note.css',
    'css/ui/state.css',
    'components/drawer/drawer.css',
);

$styles = array(
    'css/ui/report_table.css',
    'components/filters/filters.css',
    'components/summary/summary.css',
    'components/message_details/message_details.css',
);

$back_url = $filters['event_id'] > 0 ? 'vms_send_reminder.php?event_id=' . $filters['event_id'] : 'create_event.php';
?>
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,400,0,0&display=block">
<?php foreach ($shared_styles as $style) { ?>
<link rel="stylesheet" href="<?php echo asset_url($shared . $style); ?>">
<?php } ?>
<?php foreach ($styles as $style) { ?>
<link rel="stylesheet" href="<?php echo asset_url($parts . $style); ?>">
<?php } ?>

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/layout/header/header.php'; ?>

        <main class="sr" id="sr-main">
            <header class="sr-head">
                <a class="sr-btn sr-btn-secondary sr-icon-btn sr-head-back"
                    href="<?php echo htmlspecialchars($back_url); ?>" aria-label="Back" title="Back">
                    <i class="sr-symbol" aria-hidden="true">arrow_back</i>
                </a>
                <div class="sr-head-text">
                    <h1>Send report</h1>
                    <p>Emails and WhatsApp messages sent for your events.</p>
                </div>
                <div class="sr-head-actions">
                    <a class="sr-btn sr-btn-secondary"
                        href="vms_send_reminder.php<?php echo $filters['event_id'] > 0 ? '?event_id=' . $filters['event_id'] : ''; ?>">
                        <i class="sr-symbol" aria-hidden="true">send</i> Send reminder
                    </a>
                </div>
            </header>

            <?php if (empty($user_events)) { ?>
            <div class="sr-card">
                <div class="sr-empty">
                    <div class="sr-empty-icon"><i class="sr-symbol sr-symbol-lg" aria-hidden="true">calendar_month</i>
                    </div>
                    <p class="sr-empty-title">No events yet</p>
                    <p class="sr-empty-text">Create an event first. Its emails and WhatsApp messages show up here.</p>
                </div>
            </div>
            <?php } else { ?>

            <?php include __DIR__ . '/_parts/vms_send_report/components/filters/filters.php'; ?>

            <div id="srp-summary"></div>

            <section class="sr-card srp-section" id="srp-messages"></section>

            <?php
            $drawer_id    = 'srp-details-drawer';
            $drawer_title = 'Message details';
            $drawer_wide  = true;
            include __DIR__ . '/_parts/vms_send_reminder/components/drawer/drawer.php';
            ?>

            <template id="srp-tpl-loading">
                <div class="sr-loading"><span class="sr-spin" aria-hidden="true"></span> Loading…</div>
            </template>
            <template id="srp-tpl-error">
                <div class="sr-note sr-note-warn"><i class="sr-symbol" aria-hidden="true">error</i> <span></span></div>
            </template>
            <?php } ?>
        </main>

        <?php include __DIR__ . '/footer.php'; ?>
    </div>

    <?php if (!empty($user_events)) { ?>
    <script>
    window.SRP_CONFIG = {
        apiBase: '_parts/vms_send_report/api/'
    };
    </script>
    <script src="<?php echo asset_url($shared . 'components/drawer/drawer.js'); ?>"></script>
    <script src="<?php echo asset_url($parts . 'js/main.js'); ?>"></script>
    <?php } ?>
</body>

</html>