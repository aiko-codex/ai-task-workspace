# Registration report v2 — architecture

## Overview & Constraints

- Target scale: 5–6 lakh (500,000–600,000) visitors.
- Tech stack: PHP 5.6.40, vanilla JS (`fetch`), MySQLi procedural, CSS design tokens.
- Entry point: `vms_admin/registration_report_v2.php` (shell only).
- Parts folder: `vms_admin/_parts/registration_report_v2/`.
- Attachments: fully built and supported (modal + drawer card + export). Even if event 71 does not currently use attachments, components and logic are kept production-ready for future events and types.

---

## File Layout

```text
vms_admin/
    registration_report_v2.php          ← entry page (shell only)

    _parts/registration_report_v2/
        lib/
            api_boot.php                ← session check, db connection, $filters sanitization
            query.php                   ← query helper functions (keyset pagination, summary, filters)
        css/
            report.css                  ← page-specific styles (imports tokens.css)
        js/
            report.js                   ← fetch calls, spinner, state management, drawer/modal triggers
        components/
            filters/
                filters.php             ← filter bar HTML (dates, reg type, payment status, search)
            table_row/
                table_row.php           ← single <tr> template included by rows.php
            row_menu/
                row_menu.php            ← ⋮ dropdown actions (view details, offline payment, receipt, resend)
            summary_cards/
                summary_cards.php       ← 4 metric cards (total matching, paid, unpaid, reg types)
            details_drawer/
                details_drawer.php      ← slide-in drawer shell
            offline_payment_modal/
                offline_payment_modal.php ← complete offline payment modal
            receipt_modal/
                receipt_modal.php       ← payment receipt preview modal
            attachment_modal/
                attachment_modal.php    ← document attachment preview modal (matches Figma 58:9907)
        api/
            html/
                rows.php                ← renders <tbody> rows with keyset pagination
                summary.php             ← renders summary cards metrics
                details.php             ← renders drawer content for single visitor (photo, info, attachment)
            json/
                counts.php              ← JSON summary counts for filter chips
                export.php              ← streaming CSV/Excel download (includes attachment link)
                save_payment.php        ← POST: record offline payment
                resend_email.php        ← POST: resend registration pass email
                delete_visitor.php      ← POST: delete visitor record
```

---

## Architecture & Data Decisions

### 1. Shell Entry Page
- `registration_report_v2.php` outputs HTML skeleton only (header, navigation, filter shell, table container, drawer/modal shells).
- Zero database queries on initial page load.
- JS triggers parallel fetch requests on `DOMContentLoaded` for:
  1. `api/html/summary.php` (populates summary cards)
  2. `api/html/rows.php` (populates table rows with loading spinner)

### 2. High-Performance Pagination (Keyset / Cursor)
- Avoid `OFFSET N` which does full table scans on large offsets (500k+ rows).
- Use keyset cursor pagination on primary key:
  ```sql
  WHERE auto_id < :last_id
    AND event_id = :event_id
    [...other filters...]
  ORDER BY auto_id DESC
  LIMIT 50
  ```
- Default page size: 50 records.
- Next page request sends `last_id` of the last rendered row.

### 3. Database Indexes (Migration Required)
- Existing on `visitor_master`:
  - `PRIMARY (auto_id)`
  - `idx_event_date (event_id, date)`
  - `idx_type_id (type_id)`
- Migration to add for report filtering at scale:
  - `(event_id, confirm_pay, auto_id)`
  - `(event_id, date, confirm_pay, auto_id)`
- Migration file path:
  `_Memory/sql/migrations/2026-10-14/2026-10-14_reg_report_v2_indexes.sql`

```sql
ALTER TABLE visitor_master
  ADD INDEX idx_event_pay_id (event_id, confirm_pay, auto_id),
  ADD INDEX idx_event_date_pay_id (event_id, date, confirm_pay, auto_id);
```

### 4. Registration Types & Payment Status Logic
- Small lookup table `ex_reg_type` joined on `visitor_master.type_id = ex_reg_type.id`.
- Payment chip display:
  - `ex_reg_type.pay_mode = 'No'`: **Free** chip (green/neutral, no payment action).
  - `ex_reg_type.pay_mode = 'Yes'` AND `confirm_pay = 'YES'`: **Paid** chip (green, receipt action).
  - `ex_reg_type.pay_mode = 'Yes'` AND `confirm_pay != 'YES'`: **Unpaid** chip (orange/red, "Complete offline payment" action).
- `online_mem_approve` transaction table joined ONLY in drawer/receipt modal queries, never in the main table list query.

### 5. Photo Display
- Governed by `ex_reg_type.photo_required`:
  - `'Yes'`: render visitor photo from `visitor_master.visitor_photo` in details drawer.
  - `'No'`: photo section completely hidden in drawer.

### 6. Attachments Logic & Modal (Built & Future-Ready)
- `visitor_master.attachment` and `visitor_master.attachment2` store file paths relative to `web_add`.
- `ex_reg_type.attachment_required`:
  - When attachment exists on the record, details drawer renders the Document Attachment card (filename, file extension icon, size, Preview button, Download button).
  - Clicking "Preview" opens `attachment_modal` (Figma `58:9907` design with zoom controls `− 100% + Fit`, full preview area, and action footer).
  - Download link strictly includes `download="filename.ext"` attribute.
  - Export CSV includes attachment URL column.

### 7. Per-Type Custom Fields (`ex_typewise_form` & `visitor_extra_info`)
- Load `ex_typewise_form` schema once per event (small dataset).
- Drawer `api/html/details.php` queries `visitor_extra_info` for the given visitor and only displays active fields (`flag = 'YES'`) in sequence order.
- Never query form schema per row.

### 8. Summary Cards Query
- Single grouped query for matching/paid/unpaid counts:
  ```sql
  SELECT confirm_pay, COUNT(*) AS cnt
  FROM visitor_master
  WHERE event_id = :event_id [filters]
  GROUP BY confirm_pay
  ```
- Total registration types count is event-wide:
  ```sql
  SELECT COUNT(*) FROM ex_reg_type WHERE event_id = :event_id
  ```

### 9. Export Streaming
- Direct output stream with `fputcsv()` inside loop to prevent memory exhaustion on large result sets.
- Strict requirement: download links must include `<a download="filename.csv" ...>` attribute.

### 10. CSS & Design Tokens
- Import shared tokens: `_parts/vms_send_reminder/css/tokens.css`.
- Page-specific styles in `_parts/registration_report_v2/css/report.css`.
