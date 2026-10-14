# Registration report v2 — decisions

- **2026-10-14: Attachments built and ready for future**: Although current main event 71 does not actively use attachments, attachment handling is NOT omitted. The attachment card in the details drawer, `attachment_modal.php` (matching Figma 58:9907), download attribute handling, and export columns are fully built and kept production-ready for events and types where `attachment` / `attachment2` are populated.

- **2026-10-14: Per-type custom fields load once**: `ex_typewise_form` rows for the event are fetched once at page load and passed server-side; `details.php` looks up `type_id` and renders only `flag='YES'` fields in `seq` order. Never queried per row.

- **2026-10-14: Photo shown per type config**: `ex_reg_type.photo_required='Yes'` → show `visitor_photo` in details drawer. `'No'` → photo section hidden entirely.

- **2026-10-14: Payment chip 3-state logic**: `pay_mode='No'` → Free chip. `pay_mode='Yes' + confirm_pay='YES'` → Paid chip. `pay_mode='Yes' + confirm_pay≠'YES'` → Unpaid chip + Complete offline payment action. `ex_reg_type` joined in the list query (small table, safe).

- **2026-10-14: Cursor-based pagination**: No `OFFSET`. Use `WHERE auto_id < :last_id ORDER BY auto_id DESC LIMIT 50`. Default page size 50. Frontend sends `last_id`, never a page number.

- **2026-10-14: New indexes needed**: Migration adds `(event_id, confirm_pay, auto_id)` and `(event_id, date, confirm_pay, auto_id)` to `visitor_master`. Migration file: `_Memory/sql/migrations/2026-10-14/2026-10-14_reg_report_v2_indexes.sql`.

- **2026-10-14: File structure decided**: Entry `registration_report_v2.php` is shell only (no DB at page load). Parts in `_parts/registration_report_v2/`. Sub-folders: `lib/`, `css/`, `js/`, `components/`, `api/html/`, `api/json/`. See [[architecture]] for full tree.

- **2026-10-14: HTML API pattern**: Follows `vms_send_report` pattern. `api/html/` returns rendered HTML; JS sets `innerHTML` with loading spinner. No `XMLHttpRequest`. Each endpoint guarded by `api_boot.php`.

- **2026-10-14: Refine Figma before coding**: User approved the review improvements except keyboard-behavior specifications. Update screens first; implementation remains a separate next phase. Do not add keyboard-behavior notes to this design work.
- **2026-10-14: Refined design behavior**: Matching registration/paid/unpaid counts follow applied filters; configured registration-type count remains event-wide. Free registrations have a distinct payment label. Selection is page-scoped and clears on page/filter changes. One Export entry opens format/field selection; selected export explicitly shows three records. Offline payment records the fixed full amount; partial payment is not offered in this design. Payment saving and email delivery have separate outcomes. These design choices are subject to user review before code.

- **2026-10-13: Build parallel new page**: Do not touch existing `vms_admin/registration_report.php`. Build new page with new UI tokens and parts architecture. Swap and link only after full verification.
- **2026-10-13: Workflow sequence**: Setup Obsidian work folder → Figma design for all pages → Decide folder structure and coding rules → Code.
