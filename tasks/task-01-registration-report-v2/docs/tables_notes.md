# Registration report v2 — tables

- `visitor_master` (read): visitor records, photos, attachments, ticket IDs.
- `visitor_extra_info` (read): custom form field values linked by `visitor_id`.
- `pg_event_master` (read): event details, dates, banner, title.
- `ex_reg_type` (read): registration categories/types (`pay_mode`, etc.).
- `ex_typewise_form` (read): custom form field definitions per registration type.
- `ex_reg_type_tickets` (read): ticket names for activities.
- `online_mem_approve` (read): transaction status for paid events.
- `ja_comp_master` (read): company `web_add` for asset URLs.
