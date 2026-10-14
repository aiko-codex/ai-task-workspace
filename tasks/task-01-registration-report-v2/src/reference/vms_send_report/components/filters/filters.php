<?php
/** @var array $user_events */
/** @var array $sources */
/** @var array $filters */
?>
<form class="sr-card srp-filters" id="srp-filters" autocomplete="off">
    <div class="srp-filter srp-filter-wide">
        <label class="sr-label" for="srp-event">Event</label>
        <select class="sr-input" id="srp-event" name="event_id">
            <option value="0">All my events</option>
            <?php foreach ($user_events as $event_id => $event_title) { ?>
            <option value="<?php echo $event_id; ?>" <?php echo $filters['event_id'] == $event_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($event_title); ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="srp-filter">
        <span class="sr-label" id="srp-channel-label">Channel</span>
        <div class="sr-seg" role="radiogroup" aria-labelledby="srp-channel-label">
            <input type="radio" name="channel" id="srp-channel-all" value="" checked>
            <label for="srp-channel-all">All</label>
            <input type="radio" name="channel" id="srp-channel-email" value="email">
            <label for="srp-channel-email"><i class="sr-symbol" aria-hidden="true">mail</i> Email</label>
            <input type="radio" name="channel" id="srp-channel-whatsapp" value="whatsapp">
            <label for="srp-channel-whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</label>
        </div>
    </div>

    <div class="srp-filter">
        <span class="sr-label" id="srp-status-label">Status</span>
        <div class="sr-seg" role="radiogroup" aria-labelledby="srp-status-label">
            <input type="radio" name="status" id="srp-status-all" value="" checked>
            <label for="srp-status-all">All</label>
            <input type="radio" name="status" id="srp-status-sent" value="sent">
            <label for="srp-status-sent">Sent</label>
            <input type="radio" name="status" id="srp-status-failed" value="failed">
            <label for="srp-status-failed">Failed</label>
        </div>
    </div>

    <div class="srp-filter">
        <label class="sr-label" for="srp-source">Sent from</label>
        <select class="sr-input" id="srp-source" name="source">
            <option value="">All sources</option>
            <?php foreach ($sources as $source) { ?>
            <option value="<?php echo htmlspecialchars($source); ?>"><?php echo htmlspecialchars(srp_source_label($source)); ?></option>
            <?php } ?>
        </select>
    </div>

    <span class="srp-break" aria-hidden="true"></span>

    <div class="srp-filter">
        <label class="sr-label" for="srp-from">From</label>
        <input type="date" class="sr-input" id="srp-from" name="from">
    </div>

    <div class="srp-filter">
        <label class="sr-label" for="srp-to">To</label>
        <input type="date" class="sr-input" id="srp-to" name="to">
    </div>

    <div class="srp-filter srp-filter-wide">
        <label class="sr-label" for="srp-search">Search</label>
        <input type="search" class="sr-input" id="srp-search" name="search" placeholder="Email, mobile, subject or template">
    </div>

    <div class="srp-filter-actions">
        <button type="submit" class="sr-btn sr-btn-primary"><i class="sr-symbol" aria-hidden="true">filter_alt</i> Apply</button>
        <button type="button" class="sr-btn sr-btn-ghost" id="srp-reset">Reset</button>
    </div>
</form>
