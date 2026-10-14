(function () {
    var API_BASE = window.SRP_CONFIG.apiBase;
    var currentPage = 1;

    function el(id) {
        return document.getElementById(id);
    }

    function cloneTemplate(templateId) {
        return document.getElementById(templateId).content.cloneNode(true);
    }

    function showLoading(container) {
        container.innerHTML = '';
        container.appendChild(cloneTemplate('srp-tpl-loading'));
    }

    function showError(container, message) {
        var errorNode = cloneTemplate('srp-tpl-error');
        errorNode.querySelector('span').textContent = message;
        container.innerHTML = '';
        container.appendChild(errorNode);
    }

    function filterParams(page) {
        return $(el('srp-filters')).serialize() + '&page=' + page;
    }

    function loadInto(container, endpoint, params) {
        showLoading(container);
        $.get(API_BASE + endpoint, params)
            .done(function (html) {
                container.innerHTML = html;
            })
            .fail(function (request) {
                showError(container, request.responseText || 'Could not load the report. Please try again.');
            });
    }

    function loadMessages(page) {
        currentPage = page;
        loadInto(el('srp-messages'), 'html/messages.php', filterParams(page));
    }

    function loadAll() {
        loadInto(el('srp-summary'), 'html/summary.php', filterParams(1));
        loadMessages(1);
    }

    function onSubmit(event) {
        event.preventDefault();
        loadAll();
    }

    function onReset() {
        el('srp-filters').reset();
        el('srp-event').value = '0';
        loadAll();
    }

    function onSummaryClick(event) {
        var button = event.target.closest('.srp-pick-event');
        if (!button) {
            return;
        }
        el('srp-event').value = button.dataset.eventId;
        loadAll();
        el('srp-filters').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    window.SR = { showLoading: showLoading, showError: showError };

    function openDetails(button) {
        SrDrawer.open('srp-details-drawer', {
            url: API_BASE + 'html/message_details.php',
            params: { channel: button.dataset.channel, id: button.dataset.id },
        });
    }

    function onMessagesClick(event) {
        var detailsButton = event.target.closest('.srp-details-btn');
        if (detailsButton) {
            openDetails(detailsButton);
            return;
        }

        var button = event.target.closest('.srp-page');
        if (button && !button.disabled) {
            loadMessages(parseInt(button.dataset.page, 10) || currentPage);
            el('srp-messages').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function onChoiceChange(event) {
        if (event.target.matches('input[type="radio"], select')) {
            loadAll();
        }
    }

    function keepThemeDragScrollOutOfFields(event) {
        if (event.target.closest('input, textarea, select')) {
            event.stopPropagation();
        }
    }

    el('sr-main').addEventListener('mousedown', keepThemeDragScrollOutOfFields);
    el('srp-filters').addEventListener('submit', onSubmit);
    el('srp-filters').addEventListener('change', onChoiceChange);
    el('srp-reset').addEventListener('click', onReset);
    el('srp-summary').addEventListener('click', onSummaryClick);
    el('srp-messages').addEventListener('click', onMessagesClick);

    loadAll();
})();
