var currentPage = 1;
var isLoading = false;

function updateExportUrl() {
    var exportBtn = document.getElementById('exportBtn');
    if (!exportBtn) return;
    var url = "download_csv.php?event_id=" + encodeURIComponent(filterParams.event_id)
        + "&frm_dt=" + encodeURIComponent(filterParams.frm_dt)
        + "&to_dt=" + encodeURIComponent(filterParams.to_dt)
        + "&eve_type=" + encodeURIComponent(filterParams.eve_type)
        + "&sort_by=" + encodeURIComponent(filterParams.sort_by);
    exportBtn.href = url;
}

function loadPage(page) {
    if (isLoading) return;
    isLoading = true;

    var btn = document.getElementById('submitBtn');
    var btnSpinner = document.getElementById('btnSpinner');
    if (btn) btn.disabled = true;
    if (btnSpinner) btnSpinner.style.display = 'inline-block';

    var container = document.getElementById('reportContainer');
    container.innerHTML = '<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading records...</p></div>';

    var formData = new FormData();
    formData.append('event_id', filterParams.event_id);
    formData.append('frm_dt', filterParams.frm_dt);
    formData.append('to_dt', filterParams.to_dt);
    formData.append('eve_type', filterParams.eve_type);
    formData.append('mob_no', filterParams.mob_no);
    formData.append('sort_by', filterParams.sort_by);
    formData.append('page', page);
    formData.append('per_page', 50);

    fetch('_parts/registration_report/api/html/get_rows.php', {
            method: 'POST',
            body: formData
        })
        .then(function(res) {
            if (btn) btn.disabled = false;
            if (btnSpinner) btnSpinner.style.display = 'none';
            var meta = {
                page: parseInt(res.headers.get('X-Page')) || 1,
                perPage: parseInt(res.headers.get('X-Per-Page')) || 50,
                totalPages: parseInt(res.headers.get('X-Total-Pages')) || 1,
                totalRecords: parseInt(res.headers.get('X-Total-Records')) || 0
            };
            return res.text().then(function(html) {
                return {
                    html: html,
                    meta: meta,
                    ok: res.ok
                };
            });
        })
        .then(function(data) {
            isLoading = false;
            if (!data.ok) {
                container.innerHTML = '<div class="alert alert-danger">Error loading data</div>';
                return;
            }

            container.innerHTML = data.html;
            currentPage = data.meta.page;
            document.getElementById('totalCount').textContent = data.meta.totalRecords;
            renderPagination(data.meta.page, data.meta.totalPages, data.meta.totalRecords, data.meta.perPage);

            updateExportUrl();
        })
        .catch(function(err) {
            if (btn) btn.disabled = false;
            if (btnSpinner) btnSpinner.style.display = 'none';
            isLoading = false;
            container.innerHTML = '<div class="alert alert-danger">Failed to load data</div>';
        });
}

function goto_confirm(id) {
    window.open("confirm_pay.php?id=" + id, "", "500", "200")
}

function goto_resend(id) {
    window.open("resend_email.php?id=" + id, "", "500", "200")
}

function goto_resend_payment(id) {
    window.open("resend_payment_email.php?id=" + id, "", "500", "200")
}

// Load first page on page load
window.addEventListener('load', function() {
    loadPage(1);
});
