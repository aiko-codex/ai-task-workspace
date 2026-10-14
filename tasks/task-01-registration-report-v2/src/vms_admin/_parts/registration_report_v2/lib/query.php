<?php

/**
 * Database queries for Registration Report v2.
 * High-performance, keyset cursor pagination, index-aligned.
 * PHP 5.6.40 compatible, procedural MySQLi.
 */

function rr_user_events($connection, $userId)
{
    $safeUserId = intval($userId);
    $where = "";
    if ($safeUserId > 1) {
        $where = "WHERE user_login_id = $safeUserId OR user_login_id = 0";
    }
    $query = "SELECT event_id, event_title, start_dt, end_dt
              FROM pg_event_master
              $where
              ORDER BY event_id DESC";
    $result = mysqli_query($connection, $query);

    $events = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = array(
                'event_id'   => intval($row['event_id']),
                'event_name' => $row['event_title'],
                'start_date' => $row['start_dt'],
                'end_date'   => $row['end_dt'],
            );
        }
    }
    return $events;
}

function rr_event_types($connection, $eventId)
{
    $safeEventId = intval($eventId);
    $query = "SELECT id, type, description, pay_mode, amount, photo_required, attachment_required
              FROM ex_reg_type
              WHERE event_id = $safeEventId
              ORDER BY type ASC";
    $result = mysqli_query($connection, $query);

    $types = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $types[] = $row;
        }
    }
    return $types;
}

function rr_filters_from_request($availableEvents)
{
    $defaultEventId = !empty($availableEvents) ? intval($availableEvents[0]['event_id']) : 0;
    $eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : $defaultEventId;

    $typeId = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;
    $paymentStatus = isset($_GET['payment_status']) ? trim($_GET['payment_status']) : 'all';
    $validPaymentStatuses = array('all', 'paid', 'unpaid', 'free');
    if (!in_array($paymentStatus, $validPaymentStatuses)) {
        $paymentStatus = 'all';
    }

    $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $dateTo   = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    $search   = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sortBy   = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'newest';
    $lastId   = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
    $limit    = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    if ($limit < 10 || $limit > 200) {
        $limit = 50;
    }

    return array(
        'event_id'       => $eventId,
        'type_id'        => $typeId,
        'payment_status' => $paymentStatus,
        'date_from'      => $dateFrom,
        'date_to'        => $dateTo,
        'search'         => $search,
        'sort_by'        => $sortBy,
        'last_id'        => $lastId,
        'limit'          => $limit,
    );
}

function rr_build_where_clause($connection, $filters)
{
    $safeEventId = intval($filters['event_id']);
    $conditions = array("vm.event_id = $safeEventId");

    if (!empty($filters['type_id'])) {
        $safeTypeId = intval($filters['type_id']);
        $conditions[] = "vm.type_id = $safeTypeId";
    }

    if ($filters['payment_status'] === 'paid') {
        $conditions[] = "rt.pay_mode = 'Yes' AND vm.confirm_pay = 'YES'";
    } elseif ($filters['payment_status'] === 'unpaid') {
        $conditions[] = "rt.pay_mode = 'Yes' AND (vm.confirm_pay != 'YES' OR vm.confirm_pay IS NULL)";
    } elseif ($filters['payment_status'] === 'free') {
        $conditions[] = "rt.pay_mode = 'No'";
    }

    if (!empty($filters['date_from'])) {
        $safeDateFrom = mysqli_real_escape_string($connection, $filters['date_from']);
        $conditions[] = "vm.date >= '$safeDateFrom'";
    }

    if (!empty($filters['date_to'])) {
        $safeDateTo = mysqli_real_escape_string($connection, $filters['date_to']);
        $conditions[] = "vm.date <= '$safeDateTo'";
    }

    if (!empty($filters['search'])) {
        $safeSearch = mysqli_real_escape_string($connection, $filters['search']);
        $conditions[] = "(vm.name LIKE '%$safeSearch%' OR vm.mobile_number LIKE '%$safeSearch%' OR vm.email LIKE '%$safeSearch%' OR vm.unique_code LIKE '%$safeSearch%')";
    }

    if (!empty($filters['last_id'])) {
        $safeLastId = intval($filters['last_id']);
        $conditions[] = "vm.auto_id < $safeLastId";
    }

    return implode(' AND ', $conditions);
}

function rr_fetch_summary($connection, $filters)
{
    $safeEventId = intval($filters['event_id']);
    $summaryFilters = $filters;
    $summaryFilters['last_id'] = 0;
    $whereClause = rr_build_where_clause($connection, $summaryFilters);

    $countsQuery = "SELECT 
                        COUNT(*) AS total_matching,
                        SUM(CASE WHEN rt.pay_mode = 'Yes' AND vm.confirm_pay = 'YES' THEN 1 ELSE 0 END) AS paid_count,
                        SUM(CASE WHEN rt.pay_mode = 'Yes' AND (vm.confirm_pay != 'YES' OR vm.confirm_pay IS NULL) THEN 1 ELSE 0 END) AS unpaid_count,
                        SUM(CASE WHEN rt.pay_mode = 'No' THEN 1 ELSE 0 END) AS free_count
                    FROM visitor_master vm
                    LEFT JOIN ex_reg_type rt ON rt.id = vm.type_id
                    WHERE $whereClause";
    $countsResult = mysqli_query($connection, $countsQuery);
    $countsRow = $countsResult ? mysqli_fetch_assoc($countsResult) : array();

    $typesCountQuery = "SELECT COUNT(*) AS total_types FROM ex_reg_type WHERE event_id = $safeEventId";
    $typesResult = mysqli_query($connection, $typesCountQuery);
    $typesRow = $typesResult ? mysqli_fetch_assoc($typesResult) : array();

    return array(
        'total_matching' => isset($countsRow['total_matching']) ? intval($countsRow['total_matching']) : 0,
        'paid_count'     => isset($countsRow['paid_count']) ? intval($countsRow['paid_count']) : 0,
        'unpaid_count'   => isset($countsRow['unpaid_count']) ? intval($countsRow['unpaid_count']) : 0,
        'free_count'     => isset($countsRow['free_count']) ? intval($countsRow['free_count']) : 0,
        'total_types'    => isset($typesRow['total_types']) ? intval($typesRow['total_types']) : 0,
    );
}

function rr_fetch_rows($connection, $filters)
{
    $whereClause = rr_build_where_clause($connection, $filters);
    $limit = intval($filters['limit']);

    $orderDirection = 'DESC';
    if ($filters['sort_by'] === 'oldest') {
        $orderDirection = 'ASC';
    }

    $query = "SELECT 
                vm.auto_id,
                vm.name,
                vm.mobile_number,
                vm.email,
                vm.date,
                vm.time,
                vm.confirm_pay,
                vm.unique_code,
                vm.visitor_photo,
                vm.attachment,
                vm.attachment2,
                vm.type_id,
                rt.type AS reg_type_name,
                rt.pay_mode,
                rt.amount AS reg_type_amount,
                rt.photo_required,
                rt.attachment_required
              FROM visitor_master vm
              LEFT JOIN ex_reg_type rt ON rt.id = vm.type_id
              WHERE $whereClause
              ORDER BY vm.auto_id $orderDirection
              LIMIT $limit";

    $result = mysqli_query($connection, $query);
    $rows = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = rr_format_visitor_row($row);
        }
    }
    return $rows;
}

function rr_format_visitor_row($row)
{
    $initials = '';
    $nameParts = preg_split('/\s+/', trim($row['name']));
    if (!empty($nameParts[0])) {
        $initials .= strtoupper(substr($nameParts[0], 0, 1));
    }
    if (count($nameParts) > 1) {
        $initials .= strtoupper(substr($nameParts[count($nameParts) - 1], 0, 1));
    }
    if ($initials === '') {
        $initials = 'V';
    }

    $paymentStatus = 'Free';
    $paymentClass = 'chip-free';
    if ($row['pay_mode'] === 'Yes') {
        if ($row['confirm_pay'] === 'YES') {
            $paymentStatus = 'Paid';
            $paymentClass  = 'chip-paid';
        } else {
            $paymentStatus = 'Unpaid';
            $paymentClass  = 'chip-unpaid';
        }
    }

    $formattedDate = '';
    if (!empty($row['date'])) {
        $timePart = !empty($row['time']) ? ' ' . $row['time'] : '';
        $timestamp = strtotime($row['date'] . $timePart);
        if ($timestamp !== false) {
            $formattedDate = date('d-m-Y, h:i a', $timestamp);
        } else {
            $formattedDate = $row['date'];
        }
    }

    $formattedMobile = $row['mobile_number'];
    if (strlen($formattedMobile) === 10) {
        $formattedMobile = substr($formattedMobile, 0, 5) . ' ' . substr($formattedMobile, 5);
    }

    return array(
        'auto_id'          => intval($row['auto_id']),
        'name'             => $row['name'],
        'initials'         => $initials,
        'mobile_number'    => $formattedMobile,
        'email'            => $row['email'],
        'reg_type_name'    => !empty($row['reg_type_name']) ? $row['reg_type_name'] : 'Visitor',
        'pay_mode'         => $row['pay_mode'],
        'payment_status'   => $paymentStatus,
        'payment_class'    => $paymentClass,
        'confirm_pay'      => $row['confirm_pay'],
        'formatted_date'   => $formattedDate,
        'unique_code'      => $row['unique_code'],
        'visitor_photo'    => $row['visitor_photo'],
        'has_attachment'   => (!empty($row['attachment']) || !empty($row['attachment2'])),
    );
}
