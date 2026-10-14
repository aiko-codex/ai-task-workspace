# Task: Registration Report v2 — Build New Admin Page

## 1. Objective

Build a new registration report page (`registration_report_v2.php`) for the VMS admin panel, replacing the old `registration_report.php`. The new page must handle 500k+ visitor records with high-performance keyset/cursor pagination, modern UI with design tokens, and a modular parts-based architecture.

**Key deliverables:**
- Shell entry page with zero DB queries on load
- Filters (date range, registration type, payment status, search)
- Summary cards (total matching, paid, unpaid, reg types)
- Data table with cursor pagination (50 records/page)
- Details drawer (visitor info, photo, attachments, custom fields)
- Offline payment modal
- Payment receipt modal
- Attachment preview modal
- Export (CSV/Excel streaming)
- Actions: resend email, delete visitor

## 2. Tech Stack & Environment

- **Language/Runtime:** PHP 5.6.40 (server) — no `??`, no PDO, no OOP MySQLi
- **Frontend:** Vanilla JS (`fetch` only), vanilla CSS with design tokens
- **Database:** MySQL via MySQLi procedural — `ruhaco_ruhavms`
- **Libraries:** Font Awesome. Bootstrap 4 / GSAP only if asked.
- **Patterns:** HTML API pattern (`api/html/` returns rendered HTML, JS sets `innerHTML`). No `XMLHttpRequest`. No HTML built with `echo` strings.
- **PHP rules:** Use `{ }` braces around HTML blocks, not `: / endif`. Includes use `__DIR__ . "/"` only. Lint with PHP 5.6 (`F:/Apache/xampp/php56/php.exe -l`).

## 3. Current Behavior / Error Logs

```text
No errors — this is a new build. The old registration_report.php works but does not scale
to 500k+ records (uses OFFSET pagination) and has an outdated UI.

The new page's folder structure exists with some initial files (see src/), but most
components and API endpoints are not yet built.
```

## 4. Files Included

### `src/vms_admin/` — Target code (new page)
- `registration_report_v2.php`: Entry page shell (exists, may need updates)
- `_parts/registration_report_v2/`: All parts for the new page
  - `lib/api_boot.php`: Session check, DB connection, $filters sanitization
  - `lib/query.php`: Query helper functions (keyset pagination, summary, filters)
  - `helper.php`: Helper utilities
  - `css/report.css`: Page-specific styles
  - `js/report.js`: Fetch calls, spinner, state management
  - `components/filters/filters.php`: Filter bar HTML
  - `components/summary_cards/summary_cards.php`: 4 metric cards
  - `components/table_row/table_row.php`: Single `<tr>` template
  - `api/html/rows.php`: Renders `<tbody>` rows with keyset pagination
  - `api/html/summary.php`: Renders summary cards metrics

### `src/reference/` — Reference files (do NOT modify)
- `registration_report.php`: Old report page (reference for feature parity)
- `registration_report/`: Old report parts
- `vms_send_report.php` + `vms_send_report/`: UI pattern reference
- `vms_send_reminder/css/tokens.css`: Shared design tokens to import

### `schemas/`
- `tables.md`: Complete database schemas for all 8 tables used

### `docs/` — Design & architecture documentation
- `architecture.md`: Full file layout, data decisions, query patterns
- `decisions.md`: All agreed design/code decisions
- `files.md`: Complete file map (what exists vs what needs building)
- `handoff.md`: Current status and build order
- `figma_review.md`: Figma design review and UI specifications
- `tables_notes.md`: Table usage notes (read/write)
- `todo/`: Daily todo files

## 5. Expected Output

### Build Order (from `docs/handoff.md`)
1. [ ] Migration SQL: `idx_event_pay_id` and `idx_event_date_pay_id` on `visitor_master`
2. [ ] Entry shell (`registration_report_v2.php`) — if updates needed
3. [ ] `api_boot.php` + `query.php` — verify/complete
4. [ ] `api/html/rows.php` + `api/html/summary.php` — verify/complete
5. [ ] Components: filters, summary_cards, table_row, row_menu
6. [ ] Details drawer + offline payment modal + receipt modal + attachment modal
7. [ ] `report.js` (fetch, spinner, state, drawer/modal triggers)
8. [ ] `api/html/details.php`
9. [ ] `api/json/counts.php`, `export.php`, `save_payment.php`, `resend_email.php`, `delete_visitor.php`
10. [ ] `css/report.css` — complete styling

### Acceptance Criteria
- [ ] Provide complete, working code for every file listed in `docs/files.md`
- [ ] All queries use keyset pagination (`WHERE auto_id < :last_id ORDER BY auto_id DESC LIMIT 50`)
- [ ] Payment chip logic: Free / Paid / Unpaid per `ex_reg_type.pay_mode` + `confirm_pay`
- [ ] Photo display governed by `ex_reg_type.photo_required`
- [ ] Custom fields loaded once per event, rendered per type in drawer
- [ ] Attachments fully supported (modal, drawer card, download attribute, export column)
- [ ] Export uses streaming `fputcsv()` — no memory exhaustion
- [ ] All download links have `download="filename.ext"` attribute
- [ ] No PHP 7+ syntax (`??`, etc.)
- [ ] Follow architecture in `docs/architecture.md` exactly
- [ ] Explain key implementation choices
