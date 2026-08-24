# DravenCMS Admin

Administration shell for DravenCMS applications. It provides authenticated presenters, navigation and notification components, account and sign-in screens, the admin layout, and the frontend assets used by feature modules.

## Features

- Secured admin presenter base class.
- ACL-aware navigation and breadcrumbs.
- User sign-in, sign-up, password reset, and account forms.
- Admin notifications and viewed-state tracking.
- Bootstrap, Datagrid, Select2, Dropzone, Summernote, CodeMirror, and FontAwesome assets.
- Doctrine entities for menus and notifications.

## Installation

```bash
composer require dravencms/admin
```

The DravenCMS package loader copies the package assets to `%wwwDir%/assets/admin` and loads `dravencms.config.neon`. Apply the package's Doctrine schema through the application's migration workflow and load its fixtures when the default administration menu is required.

The admin router handles URLs below `/admin` and supports an optional two-letter locale segment.

## Building Assets

Frontend dependencies are recorded in `assets/package.json`. Install or refresh them from the package's `assets` directory before publishing a release that changes the asset dependency tree:

```bash
npm install
```

After changing admin assets in an application, clear generated WebLoader bundles in `www/webtemp` so they are rebuilt.

## Extending the Administration

Feature presenters should extend `Dravencms\AdminModule\SecuredPresenter`. Package fixtures can add `Dravencms\Model\Admin\Entities\Menu` entries linked to ACL operations; the navbar automatically filters entries for the current user.

Use the established DravenCMS base-form and base-grid factories for consistent controls and translations.

## License

This package is licensed under the LGPL-3.0 license.
