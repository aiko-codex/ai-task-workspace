<?php

/**
 * Single table row template for Registration Report v2.
 * Matches Figma 58:4930 cell structure and typography.
 */

// Expects $visitorItem, $rowNumber
?>
<tr class="rr-table-row" data-id="<?php echo $visitorItem['auto_id']; ?>" data-payment="<?php echo htmlspecialchars($visitorItem['payment_status']); ?>">
    <td class="rr-cell rr-cell-checkbox">
        <label class="rr-checkbox-label">
            <input type="checkbox" class="rr-row-checkbox" value="<?php echo $visitorItem['auto_id']; ?>" aria-label="Select <?php echo htmlspecialchars($visitorItem['name']); ?>">
        </label>
    </td>

    <td class="rr-cell rr-cell-num">
        <span class="rr-mono-num"><?php echo htmlspecialchars($rowNumber); ?></span>
    </td>

    <td class="rr-cell rr-cell-name">
        <div class="rr-name-wrap">
            <?php if (!empty($visitorItem['visitor_photo'])) { ?>
                <img src="<?php echo htmlspecialchars($visitorItem['visitor_photo']); ?>" alt="" class="rr-avatar rr-avatar-img">
            <?php } else { ?>
                <span class="rr-avatar rr-avatar-initials" aria-hidden="true"><?php echo htmlspecialchars($visitorItem['initials']); ?></span>
            <?php } ?>
            <span class="rr-name-text" title="<?php echo htmlspecialchars($visitorItem['name']); ?>"><?php echo htmlspecialchars($visitorItem['name']); ?></span>
        </div>
    </td>

    <td class="rr-cell rr-cell-mobile">
        <span class="rr-mono-text"><?php echo htmlspecialchars($visitorItem['mobile_number']); ?></span>
    </td>

    <td class="rr-cell rr-cell-email">
        <span class="rr-email-text" title="<?php echo htmlspecialchars($visitorItem['email']); ?>"><?php echo htmlspecialchars($visitorItem['email']); ?></span>
    </td>

    <td class="rr-cell rr-cell-type">
        <span class="rr-badge-type"><?php echo htmlspecialchars($visitorItem['reg_type_name']); ?></span>
    </td>

    <td class="rr-cell rr-cell-payment">
        <span class="sr-chip <?php echo htmlspecialchars($visitorItem['payment_class']); ?>">
            <?php echo htmlspecialchars($visitorItem['payment_status']); ?>
        </span>
    </td>

    <td class="rr-cell rr-cell-date">
        <span class="rr-date-text"><?php echo htmlspecialchars($visitorItem['formatted_date']); ?></span>
    </td>

    <td class="rr-cell rr-cell-action">
        <button type="button" class="sr-btn sr-btn-secondary sr-icon-btn rr-btn-menu" data-id="<?php echo $visitorItem['auto_id']; ?>" data-name="<?php echo htmlspecialchars($visitorItem['name']); ?>" data-pay="<?php echo htmlspecialchars($visitorItem['payment_status']); ?>" aria-label="Actions for <?php echo htmlspecialchars($visitorItem['name']); ?>" title="Actions">
            <i class="sr-symbol" aria-hidden="true">more_vert</i>
        </button>
    </td>
</tr>
