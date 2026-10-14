Status: active

# Registration report v2 — handoff

## Where it stands (2026-10-14)

Figma design phase complete including in-session corrections:
- Toast repositioned to bottom-right, no shadow, correct z-order (all 4 screens)
- Payment receipt modal rebuilt to match attachment preview UI shell
- Offline payment validation note boxes realigned (note 1 was 489px, fixed to 470px)

File structure and coding decisions agreed with user. See [[architecture]] for full detail.
Attachments: fully included and built for future use (attachment card in drawer + `attachment_modal.php` matching Figma 58:9907 + export).

Entry: `vms_admin/registration_report_v2.php`
Parts: `vms_admin/_parts/registration_report_v2/`

## Plan

1. Set up Obsidian work folder ✅
2. Refine Figma screens and action states ✅
3. User reviewed Figma and made corrections ✅
4. Decide folder structure and coding conventions ✅
5. **Build new registration report page and parts** ← next session starts here
6. Test and verify against event 71.
7. Swap links from old page to new page and decommission old page.

## Next step

Next session: start coding. Pick up from [[architecture]]. Run the migration first, then build in this order:
1. Migration SQL → user runs it
2. Entry shell (`registration_report_v2.php`)
3. `api_boot.php` + `query.php`
4. `api/html/rows.php` + `api/html/summary.php`
5. Components: filters, summary_cards, table_row, row_menu
6. Details drawer + offline payment modal + receipt modal
7. `report.js` (fetch, spinner, state)
8. Export
