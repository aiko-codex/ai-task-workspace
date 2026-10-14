<?php
include __DIR__ . '/header.php';

require_once __DIR__ . '/_parts/registration_report_v2/helper.php';
require_once __DIR__ . '/_parts/registration_report_v2/lib/query.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$user_events = rr_user_events($connection, $user_id);
$filters = rr_filters_from_request($user_events);
$event_types = rr_event_types($connection, $filters['event_id']);

$current_event_name = !empty($user_events) ? $user_events[0]['event_name'] : 'Event';
foreach ($user_events as $eventItem) {
    if ($eventItem['event_id'] == $filters['event_id']) {
        $current_event_name = $eventItem['event_name'];
        break;
    }
}

$shared_dir = 'vms_admin/_parts/vms_send_reminder/';
$parts_dir  = 'vms_admin/_parts/registration_report_v2/';

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
);
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,400,0,0&display=block">

<?php foreach ($shared_styles as $styleFile) { ?>
    <link rel="stylesheet" href="<?php echo asset_url($shared_dir . $styleFile); ?>">
<?php } ?>

<link rel="stylesheet" href="<?php echo asset_url($parts_dir . 'css/report.css'); ?>">

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/layout/header/header.php'; ?>

        <main class="sr rr-page" id="sr-main">
            <!-- Page Header -->
            <div class="rr-page-head-wrap">
                <div class="rr-page-head">
                    <div class="rr-head-left">
                        <a href="create_event.php" class="rr-back-btn" aria-label="Back to events" title="Back">
                            <i class="sr-symbol" aria-hidden="true">arrow_back</i>
                        </a>
                        <div class="rr-head-text">
                            <h1>Registration report</h1>
                            <p id="rr-head-subtitle">Everyone registered for <?php echo htmlspecialchars($current_event_name); ?>.</p>
                        </div>
                    </div>
                    <div class="rr-head-actions">
                        <button type="button" class="sr-btn sr-btn-secondary" id="rr-btn-open-export">
                            <i class="sr-symbol" aria-hidden="true">download</i>
                            Export
                        </button>
                        <a href="create_event.php" class="sr-btn sr-btn-secondary">
                            View event list
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <?php include __DIR__ . '/_parts/registration_report_v2/components/filters/filters.php'; ?>

            <!-- Statistics Summary Cards -->
            <div id="rr-summary-cards">
                <section class="rr-summary-cards" aria-label="Loading registration statistics">
                    <div class="sr-card rr-stat-card">
                        <span class="rr-stat-label">Matching registrations</span>
                        <span class="rr-stat-value">—</span>
                        <span class="rr-stat-sub">Within applied filters</span>
                    </div>
                    <div class="sr-card rr-stat-card">
                        <span class="rr-stat-label">Paid</span>
                        <span class="rr-stat-value">—</span>
                        <span class="rr-stat-sub">Within applied filters</span>
                    </div>
                    <div class="sr-card rr-stat-card">
                        <span class="rr-stat-label">Unpaid</span>
                        <span class="rr-stat-value">—</span>
                        <span class="rr-stat-sub">Within applied filters</span>
                    </div>
                    <div class="sr-card rr-stat-card">
                        <span class="rr-stat-label">Registration types</span>
                        <span class="rr-stat-value">—</span>
                        <span class="rr-stat-sub">Configured for this event</span>
                    </div>
                </section>
            </div>

            <!-- Registrations Table -->
            <div class="sr-card rr-table-card">
                <div class="rr-section-head">
                    <h2 class="rr-section-title">Registrations</h2>
                    <span class="rr-section-count" id="rr-showing-count">Loading registrations…</span>
                </div>

                <div class="rr-table-wrap">
                    <table class="rr-table">
                        <thead>
                            <tr>
                                <th class="rr-cell-checkbox">
                                    <label class="rr-checkbox-label">
                                        <input type="checkbox" id="rr-master-checkbox" aria-label="Select all registrations">
                                    </label>
                                </th>
                                <th class="rr-cell-num">#</th>
                                <th class="rr-cell-name">Name</th>
                                <th class="rr-cell-mobile">Mobile</th>
                                <th class="rr-cell-email">Email</th>
                                <th class="rr-cell-type">Type</th>
                                <th class="rr-cell-payment">Payment</th>
                                <th class="rr-cell-date">Registered</th>
                                <th class="rr-cell-action"></th>
                            </tr>
                        </thead>
                        <tbody id="rr-table-body">
                            <!-- Rows injected asynchronously via api/html/rows.php -->
                        </tbody>
                    </table>
                </div>

                <!-- Keyset Pagination -->
                <div class="rr-pager">
                    <span class="rr-pager-info">Keyset cursor navigation</span>
                    <div class="rr-pager-nav">
                        <button type="button" id="rr-btn-prev" class="rr-pager-btn" disabled>
                            <i class="sr-symbol" aria-hidden="true">chevron_left</i> Previous
                        </button>
                        <button type="button" id="rr-btn-next" class="rr-pager-btn" disabled>
                            Next <i class="sr-symbol" aria-hidden="true">chevron_right</i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo asset_url($parts_dir . 'js/report.js'); ?>"></script>
</body>
</html>
