# DravenCMS Admin

Administration shell for DravenCMS applications. It provides authenticated presenters, navigation and notification components, account and sign-in screens, the admin layout, and the frontend assets used by feature modules.

## Features

- Secured admin presenter base class.
- ACL-aware navigation and breadcrumbs.
- User sign-in, sign-up, password reset, and account forms.
- Admin notifications and viewed-state tracking.
- Tabler / Bootstrap 5 administration, Contributte Datagrid 7, Tom Select, Flatpickr, Dropzone, Summernote, CodeMirror, and FontAwesome assets.
- Doctrine entities for menus and notifications.

## Installation

```bash
composer require dravencms/admin
```

The DravenCMS package loader copies the package assets to `%wwwDir%/assets/admin` and loads `dravencms.config.neon`. Apply the package's Doctrine schema through the application's migration workflow before using administration. Navigation and permission definitions come from providers.

The admin router handles URLs below `/admin` and supports an optional two-letter locale segment.

The theme menu is available in the admin header and on authentication pages. It follows the operating system by default, with optional light or dark overrides saved in the browser. Selecting System clears the override and resumes automatic changes. Preferences synchronize between tabs and do not affect the public website.

## Building Assets

Frontend dependencies are recorded in `assets/package.json`. Install or refresh them from the package's `assets` directory before publishing a release that changes the asset dependency tree:

```bash
npm ci
npm run build:admin
npm run test:theme
```

The generated `assets/js/admin.bundle.js` and `assets/css/admin.bundle.css` are shipped with the package. Rebuild both whenever the entry points or their dependencies change.

The theme uses [Tabler](https://github.com/tabler/tabler) (MIT) and Bootstrap 5. PixelAdmin and the Bootstrap 3 compatibility layer are no longer loaded. Naja handles grid requests; existing non-grid AJAX components continue using `nette.ajax.js`.

See [MIGRATION_BOOTSTRAP5.md](MIGRATION_BOOTSTRAP5.md) for application and feature-package migration requirements. This is a breaking admin UI change and must be released together with migrated feature templates.

After changing admin assets in an application, clear generated WebLoader bundles in `www/webtemp` so they are rebuilt.

## Extending the Administration

Feature presenters should extend `Dravencms\AdminModule\SecuredPresenter`.

### Plugin-provided admin navigation

Register services implementing `Dravencms\Admin\Menu\AdminMenuProviderInterface`. Admin discovers them through Nette DI. Each provider returns immutable `AdminMenuItem` definitions:

```php
public function getItems(): iterable
{
    yield new AdminMenuItem(
        id: 'example.records',
        label: 'example.menu.records',
        destination: ':Admin:Example:Record:default',
        parent: 'content',
        icon: 'fa-list',
        order: 100,
        resource: 'example',
        privilege: 'edit',
        activeDestinations: [':Admin:Example:Picture:*'],
    );
}
```

Register the provider only when `AdminExtension` is enabled if administration is optional for your plugin. Add a Composer conflict with `dravencms/admin <3.0` for such integrations. Shared core IDs are `admin.home` and `content`; other plugins can attach descendants using a parent's stable, namespaced ID. Missing parents, duplicate IDs, cycles and unknown overrides are errors. Provider discovery order does not affect sorting: entries sort by `order`, then ID.

Labels are translation keys resolved at rendering time. The sidebar, active groups, breadcrumbs and login homepage all use the same registry. Menu definitions are cached in memory, while ACL filtering runs for the current user on every call. Existing resource/privilege checks remain in force; presenters must continue enforcing access themselves. Groups without visible children are hidden. Match parameters distinguish links to the same action; `activeDestinations` associate detail presenters with their overview entry. A same-presenter fallback keeps overview entries selected on edit actions.

Applications can register their own providers and customize entries in configuration:

```neon
admin:
    menu:
        homepage: example.records
        overrides:
            example.records:
                parent: null
                order: 10
            example.unused:
                enabled: false
```

Disabled or unauthorized homepages fall back to an accessible provider-defined homepage, normally `admin.home`. Disabling a parent hides its descendants. All overrides must reference installed menu items.

### Upgrading from the database menu

Admin 3 no longer reads `adminMenu`. Menu fixtures and the old tree-recovery command are no longer registered. Existing menu rows and the mapped entity remain untouched, so this update does not require dropping or migrating a table. Recreate any custom database menu entries as a project provider and port custom ordering, visibility and homepage choices to the configuration above before deploying. ACL definitions now come from providers; group grants remain stored in the database. Clear the application's compiled Nette container cache as part of deployment.


Use the established DravenCMS base-form and base-grid factories for consistent controls and translations.

## License

This package is licensed under the LGPL-3.0 license.

## Permission assignment

Admin 3.1 integrates the User 3 provider-backed permission picker. Prerequisites are checked automatically and labeled with the permissions requiring them; **Assign independently** retains a prerequisite as a direct assignment. ACL definition editing is no longer available. See the User package migration instructions before deploying the identifier-only schema.

## Security transport and authentication (Admin 3.2)

Use User 3.1 and the matching plugin releases. Run `user:security:migrate --apply` in maintenance mode before enabling password reset; see the User upgrade instructions. Synchronize the admin assets, including `admin.bundle.js` and `layout-editor.js`, and clear compiled container/browser caches. Old sessions and old reset links require a fresh sign-in or reset request.

All destructive signal links and logout use POST with a session-bound action token. Read-only links stay GET. Grid bulk forms also use Nette form CSRF protection, and delete/bulk actions require confirmation. The shared transport covers Naja and legacy jQuery AJAX and never attaches tokens to another origin. New mutation links should carry `data-admin-mutation`; custom AJAX must send POST with the `X-Admin-CSRF` header. The token is exposed by the `admin-mutation-token` meta tag.

Login responses use a generic credentials error. Password-reset requests show the same success response for existing, missing, inactive and throttled accounts. Per-account and per-IP fixed-window limits are persisted across sessions/workers on this host: login allows 10/account and 30/IP per 15 minutes; reset allows 3/account and 10/IP per hour. `admin.authRateLimitDirectory` overrides the writable limiter directory (default `%tempDir%/admin-auth-limits`). Multi-host deployments must use an appropriate shared limiter backend or enforce equivalent limits at the edge.

Registration is rejected before form dispatch when disabled. Account activity writes are throttled by User rather than flushed on every request.


## Public assets

Released packages include a generated `public/` directory. Packager 3 links it to
`www/assets/` on every Composer install/update; existing asset URLs stay the same.
Development sources, package metadata and tests are kept outside the public directory.

After editing assets, install Composer dependencies and run `composer build:assets`.
The explicit runtime file list is `assets/public-assets.json`; local CSS dependencies
are included automatically. Commit `public/` and `public-assets.manifest.json` with
the source changes. The builder rejects missing dependencies and locally modified
or unmanaged generated files. Admin's `npm run build:admin` also rebuilds public assets.
