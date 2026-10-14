<?php

if (!function_exists('srp_user_events')) {

    define('SRP_PAGE_SIZE', 50);

    function srp_fetch_all($connection, $sql)
    {
        $rows   = array();
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            plog('Query failed: ' . mysqli_error($connection), 'ERROR');
        }
        while ($result && $row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    function srp_user_events($connection, $user_id)
    {
        $user_id = mysqli_real_escape_string($connection, (string) $user_id);
        $events  = array();

        foreach (srp_fetch_all($connection, "SELECT event_id, event_title FROM pg_event_master WHERE user_login_id = '$user_id' ORDER BY event_id DESC") as $event) {
            $events[(int) $event['event_id']] = $event['event_title'];
        }

        return $events;
    }

    function srp_request($key)
    {
        return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
    }

    function srp_valid_date($date)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : '';
    }

    function srp_filters_from_request($user_events)
    {
        $event_id = (int) srp_request('event_id');

        return array(
            'event_id' => isset($user_events[$event_id]) ? $event_id : 0,
            'channel'  => in_array(srp_request('channel'), array('email', 'whatsapp')) ? srp_request('channel') : '',
            'status'   => in_array(srp_request('status'), array('sent', 'failed')) ? srp_request('status') : '',
            'source'   => srp_request('source'),
            'from'     => srp_valid_date(srp_request('from')),
            'to'       => srp_valid_date(srp_request('to')),
            'search'   => srp_request('search'),
            'page'     => max(1, (int) srp_request('page')),
        );
    }

    function srp_event_ids($filters, $user_events)
    {
        if ($filters['event_id'] > 0) {
            return array($filters['event_id']);
        }

        return empty($user_events) ? array(0) : array_keys($user_events);
    }

    function srp_conditions($connection, $filters, $user_events, $recipient_column, $title_column)
    {
        $conditions = array('event_id IN (' . implode(',', srp_event_ids($filters, $user_events)) . ')');

        if ($filters['status'] != '') {
            $conditions[] = "status = '" . $filters['status'] . "'";
        }
        if ($filters['source'] != '') {
            $conditions[] = "source = '" . mysqli_real_escape_string($connection, $filters['source']) . "'";
        }
        if ($filters['from'] != '') {
            $conditions[] = "created_at >= '" . $filters['from'] . " 00:00:00'";
        }
        if ($filters['to'] != '') {
            $conditions[] = "created_at <= '" . $filters['to'] . " 23:59:59'";
        }
        if ($filters['search'] != '') {
            $search       = mysqli_real_escape_string($connection, $filters['search']);
            $conditions[] = "($recipient_column LIKE '%$search%' OR $title_column LIKE '%$search%')";
        }

        return implode(' AND ', $conditions);
    }

    function srp_email_select($connection, $filters, $user_events)
    {
        $where = srp_conditions($connection, $filters, $user_events, 'to_email', 'subject');

        return "SELECT 'email' AS channel, id, event_id,
                CAST(to_email AS CHAR) AS recipient, CAST(subject AS CHAR) AS title,
                CAST(source AS CHAR) AS source, status, CAST(error_message AS CHAR) AS error_message, created_at
            FROM mail_engine_logs WHERE $where";
    }

    function srp_whatsapp_select($connection, $filters, $user_events)
    {
        $where = srp_conditions($connection, $filters, $user_events, 'to_number', 'template_name');

        return "SELECT 'whatsapp' AS channel, id, event_id,
                CAST(to_number AS CHAR) AS recipient, CAST(template_name AS CHAR) AS title,
                CAST(source AS CHAR) AS source, status, CAST(error_message AS CHAR) AS error_message, created_at
            FROM wa_engine_logs WHERE $where";
    }

    function srp_messages_sql($connection, $filters, $user_events)
    {
        $selects = array();

        if ($filters['channel'] != 'whatsapp') {
            $selects[] = srp_email_select($connection, $filters, $user_events);
        }
        if ($filters['channel'] != 'email') {
            $selects[] = srp_whatsapp_select($connection, $filters, $user_events);
        }

        return '(' . implode(') UNION ALL (', $selects) . ')';
    }

    function srp_empty_counts()
    {
        return array('email_sent' => 0, 'email_failed' => 0, 'whatsapp_sent' => 0, 'whatsapp_failed' => 0, 'last_at' => '');
    }

    function srp_add_count($counts, $row)
    {
        $status = $row['status'] == 'sent' ? 'sent' : 'failed';
        $counts[$row['channel'] . '_' . $status] += (int) $row['total'];
        $counts['last_at'] = max($counts['last_at'], (string) $row['last_at']);

        return $counts;
    }

    function srp_totals($connection, $filters, $user_events)
    {
        $rows = srp_fetch_all($connection, "SELECT channel, status, COUNT(*) AS total, MAX(created_at) AS last_at
            FROM (" . srp_messages_sql($connection, $filters, $user_events) . ") messages
            GROUP BY channel, status");

        return array_reduce($rows, 'srp_add_count', srp_empty_counts());
    }

    function srp_by_event($connection, $filters, $user_events)
    {
        $rows = srp_fetch_all($connection, "SELECT event_id, channel, status, COUNT(*) AS total, MAX(created_at) AS last_at
            FROM (" . srp_messages_sql($connection, $filters, $user_events) . ") messages
            GROUP BY event_id, channel, status");

        $events = array();
        foreach ($rows as $row) {
            $event_id = (int) $row['event_id'];
            if (!isset($events[$event_id])) {
                $events[$event_id] = srp_empty_counts();
                $events[$event_id]['event_id']    = $event_id;
                $events[$event_id]['event_title'] = isset($user_events[$event_id]) ? $user_events[$event_id] : 'Event ' . $event_id;
            }
            $events[$event_id] = srp_add_count($events[$event_id], $row);
        }

        usort($events, 'srp_sort_by_last_sent');

        return $events;
    }

    function srp_sort_by_last_sent($first, $second)
    {
        return strcmp($second['last_at'], $first['last_at']);
    }

    function srp_message_page($connection, $filters, $user_events)
    {
        $messages_sql = srp_messages_sql($connection, $filters, $user_events);
        $count_row    = srp_fetch_all($connection, "SELECT COUNT(*) AS total FROM ($messages_sql) messages");
        $total        = $count_row ? (int) $count_row[0]['total'] : 0;
        $page_count   = max(1, (int) ceil($total / SRP_PAGE_SIZE));
        $page         = min($filters['page'], $page_count);
        $offset       = ($page - 1) * SRP_PAGE_SIZE;

        return array(
            'rows'       => srp_fetch_all($connection, "SELECT * FROM ($messages_sql) messages ORDER BY created_at DESC LIMIT $offset, " . SRP_PAGE_SIZE),
            'total'      => $total,
            'page'       => $page,
            'page_count' => $page_count,
            'first_row'  => $total ? $offset + 1 : 0,
            'last_row'   => min($offset + SRP_PAGE_SIZE, $total),
        );
    }

    function srp_sources($connection, $user_events)
    {
        $event_ids = implode(',', empty($user_events) ? array(0) : array_keys($user_events));
        $rows      = srp_fetch_all($connection, "SELECT DISTINCT CAST(source AS CHAR) AS source FROM mail_engine_logs WHERE event_id IN ($event_ids) AND source <> ''
            UNION SELECT DISTINCT CAST(source AS CHAR) FROM wa_engine_logs WHERE event_id IN ($event_ids) AND source <> ''
            ORDER BY source");

        return array_map('srp_source_name', $rows);
    }

    function srp_source_name($row)
    {
        return $row['source'];
    }

    function srp_source_label($source)
    {
        $labels = array('send_event_whatsapp' => 'Event WhatsApp (automatic)');

        return isset($labels[$source]) ? $labels[$source] : $source;
    }

    function srp_success_rate($counts)
    {
        $sent  = $counts['email_sent'] + $counts['whatsapp_sent'];
        $total = $sent + $counts['email_failed'] + $counts['whatsapp_failed'];

        return $total ? round($sent * 100 / $total, 1) . '%' : '-';
    }

    function srp_format_time($datetime)
    {
        return $datetime ? date('d M Y, h:i A', strtotime($datetime)) : '-';
    }

    function srp_message_row($connection, $channel, $message_id, $user_events)
    {
        $tables    = array('email' => 'mail_engine_logs', 'whatsapp' => 'wa_engine_logs');
        $event_ids = implode(',', empty($user_events) ? array(0) : array_keys($user_events));

        if (!isset($tables[$channel])) {
            return null;
        }

        $rows = srp_fetch_all($connection, "SELECT * FROM " . $tables[$channel] . "
            WHERE id = '" . (int) $message_id . "' AND event_id IN ($event_ids) LIMIT 1");

        return $rows ? $rows[0] : null;
    }

    function srp_is_long_field($key)
    {
        return in_array($key, array('error_message', 'description', 'meta_json', 'request_payload', 'response_payload', 'delivery_error', 'to_email'));
    }

    function srp_field_label($key)
    {
        $labels = array(
            'ip_address' => 'IP address',
            'wa_driver'  => 'WhatsApp provider',
            'mail_driver' => 'Mail provider',
            'to_email'   => 'To',
            'to_number'  => 'To',
            'meta_json'  => 'Extra data',
        );

        return isset($labels[$key]) ? $labels[$key] : ucfirst(str_replace('_', ' ', $key));
    }

    function srp_pretty_value($value)
    {
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string) $value;
    }

    /**
     * Splits a log row into short label/value fields and long text blocks.
     */
    function srp_detail_sections($row)
    {
        $fields = array();
        $blocks = array();

        foreach ($row as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (srp_is_long_field($key)) {
                $blocks[] = array('label' => srp_field_label($key), 'value' => srp_pretty_value($value));
            } else {
                $fields[] = array('label' => srp_field_label($key), 'value' => (string) $value);
            }
        }

        return array('fields' => $fields, 'blocks' => $blocks);
    }

    function srp_number($value)
    {
        return number_format((int) $value);
    }
}
