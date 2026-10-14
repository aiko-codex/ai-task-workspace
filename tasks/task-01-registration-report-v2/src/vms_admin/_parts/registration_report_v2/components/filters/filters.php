<?php

/**
 * Filter bar component for Registration Report v2.
 * Matches Figma 58:4802 layout, typography and controls.
 */

// Expects $user_events, $filters, $event_types
?>
<section class="sr-card rr-filters" id="rr-filters" aria-label="Filters">
    <form id="rr-filters-form" onsubmit="return false;">
        <div class="rr-filter-row rr-filter-row-1">
            <div class="rr-field rr-field-event">
                <label for="rr-filter-event" class="rr-label">Event</label>
                <div class="rr-select-wrap">
                    <select id="rr-filter-event" name="event_id" class="sr-input rr-input">
                        <?php foreach ($user_events as $eventItem) { ?>
                            <option value="<?php echo htmlspecialchars($eventItem['event_id']); ?>" <?php echo ($eventItem['event_id'] == $filters['event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($eventItem['event_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="rr-field rr-field-type">
                <label for="rr-filter-type" class="rr-label">Type</label>
                <div class="rr-select-wrap">
                    <select id="rr-filter-type" name="type_id" class="sr-input rr-input">
                        <option value="0">All types</option>
                        <?php foreach ($event_types as $typeItem) { ?>
                            <option value="<?php echo htmlspecialchars($typeItem['id']); ?>" <?php echo ($typeItem['id'] == $filters['type_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($typeItem['type']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="rr-field rr-field-payment">
                <span class="rr-label">Payment</span>
                <div class="sr-segmented rr-segmented" id="rr-payment-segmented" role="radiogroup" aria-label="Payment status">
                    <button type="button" class="sr-segmented-btn <?php echo ($filters['payment_status'] === 'all') ? 'is-active' : ''; ?>" data-value="all" role="radio" aria-checked="<?php echo ($filters['payment_status'] === 'all') ? 'true' : 'false'; ?>">All</button>
                    <button type="button" class="sr-segmented-btn <?php echo ($filters['payment_status'] === 'paid') ? 'is-active' : ''; ?>" data-value="paid" role="radio" aria-checked="<?php echo ($filters['payment_status'] === 'paid') ? 'true' : 'false'; ?>">Paid</button>
                    <button type="button" class="sr-segmented-btn <?php echo ($filters['payment_status'] === 'unpaid') ? 'is-active' : ''; ?>" data-value="unpaid" role="radio" aria-checked="<?php echo ($filters['payment_status'] === 'unpaid') ? 'true' : 'false'; ?>">Unpaid</button>
                    <button type="button" class="sr-segmented-btn <?php echo ($filters['payment_status'] === 'free') ? 'is-active' : ''; ?>" data-value="free" role="radio" aria-checked="<?php echo ($filters['payment_status'] === 'free') ? 'true' : 'false'; ?>">Free</button>
                    <input type="hidden" name="payment_status" id="rr-filter-payment" value="<?php echo htmlspecialchars($filters['payment_status']); ?>">
                </div>
            </div>
        </div>

        <div class="rr-filter-row rr-filter-row-2">
            <div class="rr-field rr-field-date">
                <label for="rr-filter-from" class="rr-label">From</label>
                <input type="date" id="rr-filter-from" name="date_from" class="sr-input rr-input" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
            </div>

            <div class="rr-field rr-field-date">
                <label for="rr-filter-to" class="rr-label">To</label>
                <input type="date" id="rr-filter-to" name="date_to" class="sr-input rr-input" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
            </div>

            <div class="rr-field rr-field-search">
                <label for="rr-filter-search" class="rr-label">Search</label>
                <div class="rr-search-input-wrap">
                    <i class="sr-symbol rr-search-icon" aria-hidden="true">search</i>
                    <input type="text" id="rr-filter-search" name="search" class="sr-input rr-input rr-input-search" placeholder="Name, mobile or email" value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>
            </div>

            <div class="rr-field rr-field-sort">
                <label for="rr-filter-sort" class="rr-label">Sort by</label>
                <div class="rr-select-wrap">
                    <select id="rr-filter-sort" name="sort_by" class="sr-input rr-input">
                        <option value="newest" <?php echo ($filters['sort_by'] === 'newest') ? 'selected' : ''; ?>>Newest first</option>
                        <option value="oldest" <?php echo ($filters['sort_by'] === 'oldest') ? 'selected' : ''; ?>>Oldest first</option>
                    </select>
                </div>
            </div>

            <div class="rr-field rr-field-actions">
                <button type="button" id="rr-btn-apply" class="sr-btn sr-btn-primary rr-btn-apply">
                    Apply
                </button>
                <button type="button" id="rr-btn-reset" class="sr-btn sr-btn-secondary rr-btn-reset">
                    Reset
                </button>
            </div>
        </div>
    </form>
</section>
