<?php
require_once "../../../../../connection.php";
require_once "../../../../../clog.php";

if ($_SESSION['user_id'] == '') {
    http_response_code(401);
    exit("Unauthorized");
}

$event_id = isset($_REQUEST['event_id']) ? mysqli_real_escape_string($connection, $_REQUEST['event_id']) : '';
$frm_dt = isset($_REQUEST['frm_dt']) ? $_REQUEST['frm_dt'] : '';
$to_dt = isset($_REQUEST['to_dt']) ? $_REQUEST['to_dt'] : '';
$mob_no = isset($_REQUEST['mob_no']) ? mysqli_real_escape_string($connection, $_REQUEST['mob_no']) : '';
$eve_type = isset($_REQUEST['eve_type']) ? mysqli_real_escape_string($connection, $_REQUEST['eve_type']) : '';
$sort_by = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'auto_id';
$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$per_page = isset($_REQUEST['per_page']) ? intval($_REQUEST['per_page']) : 50;

if ($page < 1) $page = 1;
if ($per_page < 1) $per_page = 50;
if ($per_page > 200) $per_page = 200;

// Get event details
$sqleve = mysqli_query($connection, "select * from pg_event_master where event_id='$event_id'");
$reseve = mysqli_fetch_assoc($sqleve);

// Resolve dates
if ($frm_dt == '' || $to_dt == '') {
    $frm_dt = isset($reseve['start_dt']) ? $reseve['start_dt'] : '';
    $to_dt = isset($reseve['end_dt']) ? $reseve['end_dt'] : '';
} else {
    $frm_dt = date('Y-m-d', strtotime($frm_dt));
    $to_dt = date('Y-m-d', strtotime($to_dt));
}

// Get web_add for photo URLs
$sql_c = mysqli_query($connection, "select web_add from ja_comp_master");
$res_c = mysqli_fetch_assoc($sql_c);
$web_add = $res_c['web_add'];

// Build WHERE clause
if ($mob_no != '') {
    $where = " mobile_number LIKE '%$mob_no%' and event_id='$event_id'";
} else {
    $where = "event_id='$event_id'";
    if ($frm_dt != '') $where .= " AND `date` >= '$frm_dt'";
    if ($to_dt != '') $where .= " AND `date` <= '$to_dt'";

    if ($eve_type != '' && $eve_type != 'undefined') {
        $where .= " AND type_id='$eve_type'";
    }
}

// Check if any user has photo or attachment in this event/filter
$sql_check = mysqli_query($connection, "SELECT
    SUM(CASE WHEN photo != '' AND photo IS NOT NULL THEN 1 ELSE 0 END) as has_photo,
    SUM(CASE WHEN attachment != '' AND attachment IS NOT NULL THEN 1 ELSE 0 END) as has_attachment,
    SUM(CASE WHEN reg_type_ticket_ids != '' AND reg_type_ticket_ids IS NOT NULL THEN 1 ELSE 0 END) as has_ticket
    FROM visitor_master WHERE $where");
$check_res = mysqli_fetch_assoc($sql_check);
$has_any_photo = ($check_res['has_photo'] > 0);
$has_any_attachment = ($check_res['has_attachment'] > 0);
$has_ticket = ($check_res['has_ticket'] > 0);

// Get total count
$count_sql = mysqli_query($connection, "SELECT COUNT(*) as total FROM visitor_master WHERE $where");
$count_row = mysqli_fetch_assoc($count_sql);
$total_records = intval($count_row['total']);
$total_pages = ceil($total_records / $per_page);
if ($total_pages == 0) $total_pages = 1;
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $per_page;

// Build ORDER BY
if ($sort_by === 'auto_id_name') {
    $order_by = "name ASC, auto_id ASC";
} elseif ($sort_by === 'auto_id_name_desc') {
    $order_by = "name DESC, auto_id ASC";
} else {
    $order_by = "auto_id DESC";
}

// Get paginated rows
$sqlo = mysqli_query($connection, "SELECT * FROM visitor_master WHERE $where ORDER BY $order_by LIMIT $offset, $per_page");

// Get registration type details
$sqlt_type = mysqli_query($connection, "select * from ex_reg_type where id='$eve_type'");
$rest_type = mysqli_fetch_assoc($sqlt_type);
$on_payment_global = (isset($rest_type['pay_mode']) && $rest_type['pay_mode'] == 'Yes');

// Get extra form fields
$sqlt1 = mysqli_query($connection, "select * from ex_typewise_form where type_id='$eve_type' and flag='YES' and `field_nm` NOT IN ('name','mob_no','email') order by seq");
$extra_fields = array();
while ($rest1 = mysqli_fetch_assoc($sqlt1)) {
    $extra_fields[] = $rest1;
}

header('X-Page: ' . $page);
header('X-Per-Page: ' . $per_page);
header('X-Total-Pages: ' . $total_pages);
header('X-Total-Records: ' . $total_records);
?>
<table class="table table-bordered table-striped" id="dataTable">
    <thead style="background:#3463c1;color:white;text-align: center;">
        <tr>
            <th>Id</th>
            <?php if ($on_payment_global) { ?>
                <th>Status</th>
            <?php } ?>
            <?php if ($has_any_photo) { ?>
                <th>Photo</th>
            <?php } ?>
            <?php if ($has_any_attachment) { ?>
                <th>Attachment</th>
            <?php } ?>
            <th>Date</th>
            <?php
            if($has_ticket){?>
                <th>Activity</th>
            <?php }
            ?>
            <th>Name</th>
            <th>Mobile no</th>
            <th>Email</th>
            <?php foreach ($extra_fields as $field) { ?>
                <th><?= $field['name'] ?></th>
            <?php } ?>
            <?php if ($on_payment_global) { ?>
                <th>Action</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody id="tableBody">
        <?php
        if ($total_records == 0) {
            ?>
            <tr>
                <td colspan="25" style="text-align:center; padding:30px;">No records found</td>
            </tr>
            <?php
        } else {
            $i = $offset + 1;
            while ($reso = mysqli_fetch_assoc($sqlo)) {
                $sqlus = mysqli_query($connection, "select * from visitor_extra_info where visitor_id='$reso[id]'");
                $resus = mysqli_fetch_assoc($sqlus);
                
                  $reg_type_ticket_ids=$reso['reg_type_ticket_ids'];;
                    
                    $tick_names='';
                    $sqltik=mysqli_query($connection,"select * from ex_reg_type_tickets where id IN ($reg_type_ticket_ids)");
                    while($restik=mysqli_fetch_assoc($sqltik))
                    {
                        $tick_names=$tick_names.",".$restik['ticket_name'];
                    }
                    $tick_names=trim($tick_names,",");

                $stat = '';
                $clr = '';
                if ($on_payment_global) {
                    $sqlo1 = mysqli_query($connection, "select * from online_mem_approve where cust_id='" . ($resus['visitor_id'] ? $resus['visitor_id'] : '') . "'");
                    $reso1 = mysqli_fetch_assoc($sqlo1);
                    if (isset($reso1['trans_status']) && $reso1['trans_status'] == 'success') {
                        $stat = 'Paid';
                        $clr = 'blue';
                    } else {
                        $stat = 'Unpaid';
                        $clr = 'red';
                    }
                }
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <?php if ($on_payment_global) { ?>
                        <td>
                            <p style="color:<?= $clr ?>"><?= $stat ?></p>
                        </td>
                    <?php } ?>
                    <?php if ($has_any_photo) { ?>
                        <td>
                            <?php if ($reso['photo'] != '') { ?>
                                <img src="<?= $web_add ?>/<?= $reso['photo'] ?>" class="img img-responsive" style="width:50px;height:50px;border-radius:100%;" />
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <?php if ($has_any_attachment) { ?>
                        <td>
                            <?php if ($reso['attachment'] != '') {
                                $file_ext = strtolower(pathinfo($reso['attachment'], PATHINFO_EXTENSION));
                                $is_image = in_array($file_ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'));
                                $attachment_url = $web_add . '/' . $reso['attachment'];
                            ?>
                                <button type="button" class="btn btn-sm btn-info" onclick="viewAttachment('<?= $reso['id'] ?>','<?= $attachment_url ?>', <?= $is_image ? 'true' : 'false' ?>)">
                                    View
                                </button>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td><?= date('d-m-Y', strtotime($reso['date'])) ?> <?= date('H:i:s', strtotime($reso['time'])) ?></td>
                    <?php
                     if($has_ticket){?>
                     <td><?=$tick_names?></td>
                     <?php }
                    ?>
                    <td><?= $reso['name'] ?></td>
                    <td><?= $reso['mobile_number'] ?></td>
                    <td><?= $reso['email'] ?></td>
                    <?php foreach ($extra_fields as $field) {
                        $sqlve = mysqli_query($connection, "select " . $field['field_nm'] . " as name from visitor_extra_info where visitor_id='$reso[id]'");
                        $resve = mysqli_fetch_assoc($sqlve);
                    ?>
                        <td><?= isset($resve['name']) ? $resve['name'] : '' ?></td>
                    <?php } ?>
                    <?php if ($on_payment_global && $stat == 'Paid') { ?>
                        <td>
                            <input type="button" class="btn btn-primary btn-sm" value="Resend Email" onClick="goto_resend('<?= $reso['id'] ?>')" />
                        </td>
                    <?php } else if ($on_payment_global && $stat != 'Paid') { ?>
                        <td>
                            <input type="button" class="btn btn-danger btn-sm" value="Complete Offline Payment" onClick="goto_confirm('<?= $reso['id'] ?>')" />
                            <input type="button" class="btn btn-warning btn-sm" value="Resend Payment Mail" onClick="goto_resend_payment('<?= $reso['id'] ?>')" />
                        </td>
                    <?php } ?>
                </tr>
                <?php
                $i++;
            }
        }
        ?>
    </tbody>
</table>
