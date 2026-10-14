/**
 * Registration Report v2 Frontend Controller
 * Vanilla JS, fetch-based, clean functional structure.
 */

(function () {
    'use strict';

    var state = {
        eventId: 0,
        typeId: 0,
        paymentStatus: 'all',
        dateFrom: '',
        dateTo: '',
        search: '',
        sortBy: 'newest',
        limit: 50,
        lastId: 0,
        pageStack: [],
        totalMatching: 0,
        isLoading: false
    };

    function init() {
        bindFilterControls();
        bindPaginationControls();
        bindMasterCheckbox();

        // Initial fetch
        syncStateFromForm();
        loadSummary();
        loadRows();
    }

    function syncStateFromForm() {
        var eventSelect = document.getElementById('rr-filter-event');
        var typeSelect = document.getElementById('rr-filter-type');
        var paymentInput = document.getElementById('rr-filter-payment');
        var fromInput = document.getElementById('rr-filter-from');
        var toInput = document.getElementById('rr-filter-to');
        var searchInput = document.getElementById('rr-filter-search');
        var sortSelect = document.getElementById('rr-filter-sort');

        if (eventSelect) state.eventId = parseInt(eventSelect.value, 10) || 0;
        if (typeSelect) state.typeId = parseInt(typeSelect.value, 10) || 0;
        if (paymentInput) state.paymentStatus = paymentInput.value || 'all';
        if (fromInput) state.dateFrom = fromInput.value.trim();
        if (toInput) state.dateTo = toInput.value.trim();
        if (searchInput) state.search = searchInput.value.trim();
        if (sortSelect) state.sortBy = sortSelect.value || 'newest';
    }

    function buildQueryString(params) {
        var pairs = [];
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                var value = params[key];
                if (value !== undefined && value !== null && value !== '') {
                    pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
                }
            }
        }
        return pairs.length > 0 ? '?' + pairs.join('&') : '';
    }

    function getFilterParams() {
        return {
            event_id: state.eventId,
            type_id: state.typeId,
            payment_status: state.paymentStatus,
            date_from: state.dateFrom,
            date_to: state.dateTo,
            search: state.search,
            sort_by: state.sortBy,
            limit: state.limit,
            last_id: state.lastId
        };
    }

    function showSkeletonRows() {
        var tbody = document.getElementById('rr-table-body');
        if (!tbody) return;

        var skeletonHtml = '';
        for (var i = 0; i < 5; i++) {
            skeletonHtml += '<tr class="rr-table-row rr-skeleton-row">' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 16px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 28px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 140px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 90px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 160px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 70px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 60px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 120px;"></div></td>' +
                '<td class="rr-cell"><div class="rr-skeleton-line" style="width: 24px;"></div></td>' +
                '</tr>';
        }
        tbody.innerHTML = skeletonHtml;
    }

    function loadSummary() {
        var queryString = buildQueryString(getFilterParams());
        fetch('_parts/registration_report_v2/api/html/summary.php' + queryString, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {
            var container = document.getElementById('rr-summary-cards');
            if (container) {
                container.outerHTML = html;
                updateHeaderSubtitle();
            }
        })
        .catch(function (error) {
            console.error('Failed to load summary stats:', error);
        });
    }

    function updateHeaderSubtitle() {
        var eventSelect = document.getElementById('rr-filter-event');
        var subtitle = document.getElementById('rr-head-subtitle');
        if (eventSelect && subtitle) {
            var selectedText = eventSelect.options[eventSelect.selectedIndex] ? eventSelect.options[eventSelect.selectedIndex].text : '';
            if (selectedText) {
                subtitle.textContent = 'Everyone registered for ' + selectedText + '.';
            }
        }
    }

    function loadRows() {
        if (state.isLoading) return;
        state.isLoading = true;

        showSkeletonRows();

        var queryString = buildQueryString(getFilterParams());
        fetch('_parts/registration_report_v2/api/html/rows.php' + queryString, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {
            var tbody = document.getElementById('rr-table-body');
            if (tbody) {
                tbody.innerHTML = html;
                updatePaginationInfo();
            }
            state.isLoading = false;
        })
        .catch(function (error) {
            console.error('Failed to load table rows:', error);
            state.isLoading = false;
            var tbody = document.getElementById('rr-table-body');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding: 32px; color: var(--red);">' +
                    'Error loading registrations. Please try again.</td></tr>';
            }
        });
    }

    function updatePaginationInfo() {
        var tbody = document.getElementById('rr-table-body');
        var infoElem = document.getElementById('rr-showing-count');
        var prevBtn = document.getElementById('rr-btn-prev');
        var nextBtn = document.getElementById('rr-btn-next');

        if (!tbody) return;

        var rows = tbody.querySelectorAll('tr.rr-table-row:not(.rr-skeleton-row)');
        var rowCount = rows.length;

        if (infoElem) {
            if (rowCount === 0) {
                infoElem.textContent = 'Showing 0 registrations';
            } else {
                infoElem.textContent = 'Showing ' + rowCount + ' registrations on this page';
            }
        }

        if (prevBtn) {
            prevBtn.disabled = (state.pageStack.length === 0);
        }

        if (nextBtn) {
            nextBtn.disabled = (rowCount < state.limit);
        }
    }

    function nextPage() {
        var tbody = document.getElementById('rr-table-body');
        if (!tbody) return;

        var rows = tbody.querySelectorAll('tr.rr-table-row:not(.rr-skeleton-row)');
        if (rows.length === 0) return;

        var lastRow = rows[rows.length - 1];
        var autoId = parseInt(lastRow.getAttribute('data-id'), 10);
        if (autoId > 0) {
            state.pageStack.push(state.lastId);
            state.lastId = autoId;
            loadRows();
        }
    }

    function prevPage() {
        if (state.pageStack.length > 0) {
            state.lastId = state.pageStack.pop();
            loadRows();
        }
    }

    function applyFilters() {
        syncStateFromForm();
        state.lastId = 0;
        state.pageStack = [];
        loadSummary();
        loadRows();
    }

    function resetFilters() {
        var form = document.getElementById('rr-filters-form');
        if (form) {
            var eventSelect = document.getElementById('rr-filter-event');
            var savedEventId = eventSelect ? eventSelect.value : '';

            form.reset();

            if (eventSelect && savedEventId) {
                eventSelect.value = savedEventId;
            }

            var paymentInput = document.getElementById('rr-filter-payment');
            if (paymentInput) paymentInput.value = 'all';

            var segmentedBtns = document.querySelectorAll('#rr-payment-segmented .sr-segmented-btn');
            for (var i = 0; i < segmentedBtns.length; i++) {
                if (segmentedBtns[i].getAttribute('data-value') === 'all') {
                    segmentedBtns[i].classList.add('is-active');
                    segmentedBtns[i].setAttribute('aria-checked', 'true');
                } else {
                    segmentedBtns[i].classList.remove('is-active');
                    segmentedBtns[i].setAttribute('aria-checked', 'false');
                }
            }
        }

        applyFilters();
    }

    function bindFilterControls() {
        var segmentedWrap = document.getElementById('rr-payment-segmented');
        if (segmentedWrap) {
            segmentedWrap.addEventListener('click', function (e) {
                var btn = e.target.closest('.sr-segmented-btn');
                if (!btn) return;

                var value = btn.getAttribute('data-value');
                var hiddenInput = document.getElementById('rr-filter-payment');
                if (hiddenInput) hiddenInput.value = value;

                var allBtns = segmentedWrap.querySelectorAll('.sr-segmented-btn');
                for (var i = 0; i < allBtns.length; i++) {
                    allBtns[i].classList.remove('is-active');
                    allBtns[i].setAttribute('aria-checked', 'false');
                }
                btn.classList.add('is-active');
                btn.setAttribute('aria-checked', 'true');

                applyFilters();
            });
        }

        var applyBtn = document.getElementById('rr-btn-apply');
        if (applyBtn) {
            applyBtn.addEventListener('click', applyFilters);
        }

        var resetBtn = document.getElementById('rr-btn-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetFilters);
        }

        var eventSelect = document.getElementById('rr-filter-event');
        if (eventSelect) {
            eventSelect.addEventListener('change', function () {
                updateTypesForEvent();
                applyFilters();
            });
        }

        var searchInput = document.getElementById('rr-filter-search');
        if (searchInput) {
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    applyFilters();
                }, 400);
            });
        }
    }

    function updateTypesForEvent() {
        // Will be wired to update type options dynamically when switching events
    }

    function bindPaginationControls() {
        var prevBtn = document.getElementById('rr-btn-prev');
        if (prevBtn) {
            prevBtn.addEventListener('click', prevPage);
        }

        var nextBtn = document.getElementById('rr-btn-next');
        if (nextBtn) {
            nextBtn.addEventListener('click', nextPage);
        }
    }

    function bindMasterCheckbox() {
        var master = document.getElementById('rr-master-checkbox');
        if (!master) return;

        master.addEventListener('change', function () {
            var isChecked = master.checked;
            var rowBoxes = document.querySelectorAll('#rr-table-body .rr-row-checkbox');
            for (var i = 0; i < rowBoxes.length; i++) {
                rowBoxes[i].checked = isChecked;
            }
        });
    }

    // Expose controller globally
    window.rrApp = {
        init: init,
        applyFilters: applyFilters,
        resetFilters: resetFilters,
        nextPage: nextPage,
        prevPage: prevPage
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
