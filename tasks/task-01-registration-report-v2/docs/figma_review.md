# Registration report — Figma review

Reviewed: 2026-10-14. Scope: all 12 registration report frames on `RUHA VMS` → `vms_admin`. The separate `vms admin v2` page is outside this work. Figma does not expose frame creation dates through this inspection; these are the existing report screens referenced by the user.

## Overall

The desktop visual direction is consistent: restrained blue accent, clear grouping, readable table rows, labelled payment chips, and coherent drawers. Approved refinements are now applied: 36 frames across core screens, actions/recovery, responsive layouts, and save/resend outcomes. Eight shared button components support 206 instances; 28 click-through links connect key flows. The 12 original frames remain identifiable. Code and live data were not changed.

The refined screens use consistent numeric dates and applied-filter counts, one Export entry, explicit selected-record export, paid/free details, fixed full offline payment, distinct payment/email outcomes, validation/retry states, attachment fallback/zoom, compact laptop layout, and three mobile views. Keyboard-behavior specifications are excluded at the user's request.

## Findings from the initial twelve-screen review

| Screen | Finding / improvement |
|---|---|
| [Main report](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=56-2640) | Date filter starts 12 October, but several rows show 11 October. Make the example match the applied filter. Specify whether cards show filtered totals or whole-event totals. Use the agreed dd-mm-yyyy display format. |
| [Rows selected](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-5222) | Selected shading, count, and indeterminate checkbox are clear. Define whether select-all means this page or all matches, and whether selections persist after filtering/pagination. Add the selected-export variant showing exactly 3 records. |
| [Unpaid row menu](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-5669) | Menu groups are readable. Explain unavailable receipts. Add edit and resend progress/failure states; menu placement near the last table row needs an upward-opening example. |
| [Paid row menu](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-6135) | Payment-only actions are appropriately omitted. Replace ambiguous “Resend email” with the actual message purpose. Receipt and edit destinations are not represented in this report set. |
| [Toast](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-6589) | Recipient-specific confirmation is useful. Add error/retry and long-address wrapping. Define viewport anchoring and avoid covering active row controls. |
| [Empty](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-7025) | Reset-filters recovery is clear. Separate no matches from an event with no registrations. Do not imply zero configured registration types merely because filtering finds no visitors. Decide whether empty export is unavailable or intentionally produces headers only. |
| [Loading](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-7462) | Skeletons match the table layout. Checkboxes and exports still appear usable; specify disabled loading states. Preserve table/footer height where useful to reduce layout movement. Add load failure with retry. |
| [Delete confirmation](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-7982) | Names the visitor and makes the action clear. Copy promises permanent removal of payment details; verify actual intended behavior before implementation. Add registration ID for disambiguation and a deletion-failure state. |
| [Export drawer](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-8422) | Field selection and filtered-record count are clear. Header CSV/Excel buttons duplicate the drawer format choice; one Export trigger would simplify the page. Add selected-only scope, no-fields validation, progress/failure, and dynamic custom-field handling across types. |
| [Details drawer](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-8951) | Sections and action grouping are clear. Only unpaid details are represented. Add paid/free variants, long/missing custom values, photo handling, and inner scrolling with reachable footer actions. |
| [Attachment preview](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-9481) | Filename, download, and external opening are clear. Add image zoom, PDF/unsupported-file fallback, and load failure. Specify return to the details drawer when closing a preview opened from there. |
| [Offline payment](https://www.figma.com/design/sliGJqxiDVQdTkkP8TFhff?node-id=58-9941) | Visitor, amount, mode, and date are visible. Clarify full versus partial payment: an editable amount currently ends in “Mark as paid.” Add required/invalid-field states, saving/duplicate-submit protection, and a separate “payment saved, email failed” outcome. |

## Implementation follow-through

1. Review the updated designs with the user, then agree filenames and coding structure.
2. Implement the designed filter counts, page-scoped selection, export scope, and full-payment behavior against actual event/type rules.
3. Verify real custom fields, attachments, receipt data, long content, and failure handling during implementation.
4. Implement drawer/body scrolling and the compact laptop/mobile layouts. Static Figma screens do not verify browser responsiveness.
5. Design checks found no text overflowing its immediate unclipped frame, duplicate screen names, or frames outside their sections. These checks do not replace runtime verification.

## Evidence and limits

- Layout, labels, dates, missing variants, and component/prototype counts above are direct Figma observations. Suggested simplifications are design judgments, not measured usability results.
- Date format, calm blue styling, and small controls come from the project's conventions, not universal standards.
- Ordinary surrounding UI must reflow at narrow widths; data tables have a two-dimensional-layout exception. This is a WCAG AA criterion, not a requirement to turn every table into cards: [Reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html).
- Static screenshots do not establish keyboard accessibility, screen-reader behavior, real responsiveness, backend correctness, or runtime performance. Those remain verification tasks. The design knowledge source provided accessibility guidance but no report-specific usability research.
