<form class="main-form-container" action="" data-rel="myForm" method="post" enctype="multipart/form-data" id="frmGroup" name="frmGroup">
    <div class="row ">
        <input type="hidden" name="event_id" id="event_id" value="<?= $_REQUEST['event_id'] ?>" />
        <?php if ($_REQUEST['event_id'] != '') {
            $sqlt = mysqli_query($connection, "select * from ex_reg_type where event_id='$_REQUEST[event_id]'");
            $rwt = mysqli_num_rows($sqlt);
            if ($rwt > 0) { ?>
                <div class="col-md-12">
                    <div class="form-group float-label active">
                        <?php while ($rest = mysqli_fetch_assoc($sqlt)) { ?>
                            <input type="radio" name="eve_type" <?php if ($_REQUEST['eve_type'] == $rest['id']) { ?>checked<?php } ?> id="eve_type" value="<?= $rest['id'] ?>" /> &nbsp;<?= $rest['type'] ?>&nbsp;&nbsp;
                        <?php } ?>
                        <label class="form-control-label">Select Type</label>
                    </div>
                </div>
        <?php }
        } ?>

        <div class="col-6">
            <div class="form-group float-label active">
                <input type="date" id="frm_dt" name="frm_dt" class="form-control" value="<?= $frm_dt ?>">
                <label class="form-control-label">From Date</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group float-label active">
                <input type="date" id="to_dt" name="to_dt" class="form-control" value="<?= $to_dt ?>">
                <label class="form-control-label">To Date</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group float-label active">
                <input type="text" id="mob_no" name="mob_no" class="form-control" value="<?= isset($_REQUEST['mob_no']) ? $_REQUEST['mob_no'] : '' ?>" placeholder="Search mobile no.">
                <label class="form-control-label">Mobile No.</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group float-label active">
                <select id="sort_by" name="sort_by" class="form-control">
                    <option value="auto_id">Registration Order (Default)</option>
                    <option value="auto_id_name">Name A-Z</option>
                    <option value="auto_id_name_desc">Name Z-A</option>
                </select>
                <label class="form-control-label">Sort By</label>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-center">
        <button
            type="button"
            id="submitBtn"
            class="btn btn-default text-white shadow btn-block"
            onClick="goto_data()">
            <span id="btnSpinner" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
            Submit
        </button>
    </div>
</form>
