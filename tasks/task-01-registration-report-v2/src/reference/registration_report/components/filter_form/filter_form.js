function goto_data() {
    filterParams.frm_dt = document.getElementById("frm_dt").value;
    filterParams.to_dt = document.getElementById("to_dt").value;
    filterParams.event_id = document.getElementById("event_id").value;
    filterParams.mob_no = document.getElementById("mob_no").value;
    filterParams.sort_by = document.getElementById("sort_by").value;

    var eve_type_el = document.querySelector('input[name="eve_type"]:checked');
    filterParams.eve_type = eve_type_el ? eve_type_el.value : '';

    updateExportUrl();

    loadPage(1);
}
