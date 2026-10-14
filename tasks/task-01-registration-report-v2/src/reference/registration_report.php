<?php include "header.php"; ?>

<?php
require_once "../helper.php";
require_once "../clog.php";

$frm_dt = $_REQUEST['frm_dt'];
$to_dt = $_REQUEST['to_dt'];
if ($_REQUEST['event_id'] != '') {
    $sqleve = mysqli_query($connection, "select * from pg_event_master where event_id='$_REQUEST[event_id]'");
    $reseve = mysqli_fetch_assoc($sqleve);
    $frm_dt = $reseve['start_dt'];
    $to_dt = $reseve['end_dt'];
}

if ($_REQUEST['frm_dt'] != '' && $_REQUEST['to_dt'] != '') {
    $frm_dt = date('Y-m-d', strtotime($_REQUEST['frm_dt']));
    $to_dt = date('Y-m-d', strtotime($_REQUEST['to_dt']));
}
?>

<link rel="stylesheet" href="<?= asset_url('vms_admin/_parts/registration_report/css/main.css') ?>">
<link rel="stylesheet" href="<?= asset_url('vms_admin/_parts/registration_report/components/filter_form/filter_form.css') ?>">
<link rel="stylesheet" href="<?= asset_url('vms_admin/_parts/registration_report/components/report_table/report_table.css') ?>">
<link rel="stylesheet" href="<?= asset_url('vms_admin/_parts/registration_report/components/pagination/pagination.css') ?>">
<link rel="stylesheet" href="<?= asset_url('vms_admin/_parts/registration_report/components/attachment_modal/attachment_modal.css') ?>">

<body>
    <div class="wrapper">
        <div class="header">
            <div class="row no-gutters">
                <div class="col-auto"></div>

                <div class="col text-center header-logo-div">
                    <?php if ($resset1['top_logo'] != '') { ?>
                        <img src="<?= $resset1['top_logo'] ?>" alt="logo" class="header-logo">
                    <?php } else { ?>
                        <img src="../img/RUHA VMS (1).png" alt="" class="header-logo">
                    <?php } ?>
                </div>
                <div class="col-auto">
                    <a href="" class="btn  btn-link text-dark"><i class="material-icons">account_circle</i></a>
                </div>
            </div>
        </div>
        <div class="container" style="padding-top:100px;">
            <div class="card_shw">
                <div class="d-flex" style="flex-wrap: wrap; gap: 5px">
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="if (document.referrer) { history.back(); } else { window.location.href='create_event.php'; }">
                        Go Back
                    </button>
                    <a href="create_event.php" class="btn btn-info text-white">
                        View Event List
                    </a>
                </div>

                <hr>
                <div class="d-flex align-items-center justify-content-center flex-column">
                    <h3><?= isset($reseve['event_title']) ? $reseve['event_title'] : '' ?></h3>
                    <span>Registration Report Page</span>
                </div>
                <hr>

                <?php include "_parts/registration_report/components/filter_form/filter_form.php"; ?>

                <?php if (isset($reseve['banner']) && $reseve['banner'] != '') { ?>
                    <div class="row my-4">
                        <div class="col-md-12">
                            <img src="<?= $reseve['banner'] ?>" class="img img-responsive" style="width:100%" />
                        </div>
                    </div> <br />
                <?php } ?>

                <?php include "_parts/registration_report/components/report_table/report_table.php"; ?>

                <?php include "_parts/registration_report/components/pagination/pagination.php"; ?>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>

    <?php include "_parts/registration_report/components/attachment_modal/attachment_modal.php"; ?>

    <script>
        var filterParams = {
            event_id: '<?= addslashes(isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : '') ?>',
            frm_dt: '<?= addslashes(isset($frm_dt) ? $frm_dt : '') ?>',
            to_dt: '<?= addslashes(isset($to_dt) ? $to_dt : '') ?>',
            eve_type: '<?= addslashes(isset($_REQUEST['eve_type']) ? $_REQUEST['eve_type'] : '') ?>',
            mob_no: '<?= addslashes(isset($_REQUEST['mob_no']) ? $_REQUEST['mob_no'] : '') ?>',
            sort_by: 'auto_id'
        };
    </script>
    <script src="<?= asset_url('vms_admin/_parts/registration_report/components/pagination/pagination.js') ?>"></script>
    <script src="<?= asset_url('vms_admin/_parts/registration_report/components/report_table/report_table.js') ?>"></script>
    <script src="<?= asset_url('vms_admin/_parts/registration_report/components/filter_form/filter_form.js') ?>"></script>
    <script src="<?= asset_url('vms_admin/_parts/registration_report/components/attachment_modal/attachment_modal.js') ?>"></script>
</body>

</html>
