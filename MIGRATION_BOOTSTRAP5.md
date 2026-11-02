# Bootstrap 5 / Tabler Migration

This is a breaking administration UI upgrade. The public-facing website is not affected unless it explicitly imports admin assets.

## Asset Stack

Use the package's current `webloader` configuration. Applications with copied package configuration must update their `dravencms/admin.neon` too.

- The theme is Tabler core (MIT), with Bootstrap 5 JavaScript.
- `admin.bundle.css` includes Tabler, Datagrid styling, Flatpickr, and Tom Select.
- `admin.bundle.js` initializes the shared widgets and grid client.
- Load the Bootstrap 5 build of Summernote, not its Bootstrap 3 build.
- Do not load PixelAdmin, old grid bundles, Bootstrap 3, Select2, or the replaced Bootstrap date/time pickers.
- Grid requests use Naja. Other existing AJAX components retain `nette.ajax.js`; the selectors deliberately do not overlap.

Build from `assets` with `npm ci` followed by `npm run build:admin`. Ship the generated bundles and the configured third-party runtime assets. New build-only dependencies do not need to be committed under `node_modules`.

## Feature Templates

Migrate all feature packages used by an application before deploying the new shell:

| Previous markup | Bootstrap 5 markup |
| --- | --- |
| `panel`, `panel-heading`, `panel-body`, `panel-footer` | `card`, `card-header`, `card-body`, `card-footer` |
| `form-group`, `control-label` | `mb-3`, `form-label` |
| `checkbox`, `px`, `lbl` | `form-check`, `form-check-input`, `form-check-label` |
| `btn-default`, `btn-xs` | `btn-outline-secondary`, `btn-sm` |
| `input-group-addon` | `input-group-text` |
| `data-toggle`, `data-target`, `data-dismiss` | `data-bs-toggle`, `data-bs-target`, `data-bs-dismiss` |
| active tab list item | `nav-link active` on the tab link |
| modal `close` button containing an x | empty `btn-close` button with an accessible label |

Remove `form-horizontal` and use responsive grid columns for form layout. Retain Nette form bindings, ACL conditions, validation, and field names.

`Grid` supplies Bootstrap 5 action defaults. Explicit custom action classes also need migration.

## Widgets And Layout

- `.date`, `.time`, and `.datetime` inputs use Flatpickr; `data-format` retains PHP-style formatting.
- Non-grid selects use Tom Select. Add `data-native-select` to retain a native select.
- Grid selects and dates are initialized through Datagrid plugins.
- The sidebar retains ACL-filtered menu generation. Menu groups use keyboard-accessible native disclosure controls.
- Chart.js replaces the old Morris chart initializer. It is not the ApexCharts library shipped with Tabler's demos.
- Existing file-manager modals retain Bootstrap's jQuery interface and use `data-bs-target` for their target.

## Deployment And Verification

Update the package configuration, synchronize assets, and rebuild generated DI and translation caches after adding new catalogs. A stale production DI container can register an obsolete resource list even when a debug container is current.

Review sign-in, password reset, sidebar navigation, locale tabs, form validation, grid filtering/sorting/pagination, file selection/upload, rich-text dialogs, and narrow-screen layouts. Release the shared admin and migrated feature packages together; do not deploy the new theme over unmigrated Bootstrap 3 templates.
