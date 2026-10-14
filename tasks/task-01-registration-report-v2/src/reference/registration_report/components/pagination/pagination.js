function renderPagination(page, totalPages, totalRecords, perPage) {
    var area = document.getElementById('paginationArea');
    var info = document.getElementById('pageInfo');
    var controls = document.getElementById('paginationControls');

    if (totalPages <= 1) {
        area.style.display = totalRecords > 0 ? 'block' : 'none';
        info.textContent = 'Showing all ' + totalRecords + ' records';
        controls.innerHTML = '';
        return;
    }

    area.style.display = 'block';
    var from = ((page - 1) * perPage) + 1;
    var to = Math.min(page * perPage, totalRecords);
    info.textContent = 'Showing ' + from + ' to ' + to + ' of ' + totalRecords + ' records';

    var html = '';
    if (page > 1) {
        html += '<button class="btn btn-sm btn-outline-primary" onclick="loadPage(' + (page - 1) + ')">&laquo; Prev</button>';
    } else {
        html += '<button class="btn btn-sm btn-outline-secondary" disabled>&laquo; Prev</button>';
    }

    var startPage = Math.max(1, page - 2);
    var endPage = Math.min(totalPages, page + 2);

    if (startPage > 1) {
        html += '<button class="btn btn-sm btn-outline-primary" onclick="loadPage(1)">1</button>';
        if (startPage > 2) html += '<span style="padding:5px;">...</span>';
    }

    for (var p = startPage; p <= endPage; p++) {
        if (p === page) {
            html += '<button class="btn btn-sm btn-primary">' + p + '</button>';
        } else {
            html += '<button class="btn btn-sm btn-outline-primary" onclick="loadPage(' + p + ')">' + p + '</button>';
        }
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += '<span style="padding:5px;">...</span>';
        html += '<button class="btn btn-sm btn-outline-primary" onclick="loadPage(' + totalPages + ')">' + totalPages + '</button>';
    }

    if (page < totalPages) {
        html += '<button class="btn btn-sm btn-outline-primary" onclick="loadPage(' + (page + 1) + ')">Next &raquo;</button>';
    } else {
        html += '<button class="btn btn-sm btn-outline-secondary" disabled>Next &raquo;</button>';
    }
    controls.innerHTML = html;
}
