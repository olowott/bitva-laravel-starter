# BitVa Laravel Starter

A reusable, production-focused Laravel starter application for building modern business web applications, internal systems, client portals, dashboards, and SaaS products.

The starter provides the common infrastructure required across BitVa Tech Laravel projects so new applications can begin with authentication, administration, permissions, settings, audit logging, notifications, private documents, exports, and a consistent UI already in place.

The goal is simple:

> Start new projects with the platform foundation already built, then focus on the project's actual business logic.

---

## Tech Stack

- Laravel 12
- PHP 8.3+
- MySQL
- Blade
- Tailwind CSS 4
- Alpine.js
- Vite
- Laravel Breeze
- Spatie Laravel Permission
- Spatie Laravel Activitylog
- Heroicons

The starter intentionally uses a server-rendered Blade architecture rather than requiring a JavaScript SPA framework.

---

## Core Features

### Authentication

- Login
- Registration
- Logout
- Email verification
- Password reset
- Password confirmation
- Login rate limiting
- Password-reset request rate limiting
- Active/inactive account enforcement

---

### Admin Dashboard

Responsive administration interface built with reusable Blade components.

Includes:

- Responsive sidebar
- Top navigation
- Mobile navigation
- User menu
- Breadcrumbs
- Page headers
- Flash messages
- Cards
- Statistics components
- Alerts
- Badges
- Modals
- Empty states
- Avatars
- Form components
- Table components

---

### User Management

Administrators can:

- View users
- Create users
- Edit users
- Activate/deactivate accounts
- Assign roles
- Search users
- Filter users
- Sort users
- Paginate results
- Export filtered user data

Users use public ULID identifiers in application URLs while internal numeric database IDs remain available for relationships and database operations.

Additional safeguards prevent:

- Unauthorized super administrator assignment
- Non-super administrators modifying super administrators
- Self-deactivation
- Self-deletion
- Removal or deactivation of the final super administrator

---

## Roles & Permissions

Role-based access control is provided using Spatie Laravel Permission.

Default roles:

- `super_admin`
- `admin`
- `user`

The `super_admin` role receives a global authorization bypass through Laravel's Gate.

Example permissions include:

- `users.view`
- `users.create`
- `users.update`
- `users.delete`
- `users.export`
- `roles.view`
- `roles.manage`
- `settings.view`
- `settings.manage`
- `activity.view`
- `activity.export`
- `documents.view`
- `documents.upload`
- `documents.download`
- `documents.delete`
- `notifications.send`

Project-specific permissions should be added by individual applications.

---

## Application Settings

Database-driven application settings provide configurable:

- Application name
- Application tagline
- Company name
- Company email
- Company phone
- Timezone
- Primary brand color
- Secondary brand color
- Logo
- Favicon

Settings are accessed through the reusable settings service and `setting()` helper.

Frequently accessed settings are cached.

---

## Branding

Public branding assets are stored separately from private application documents.

Public assets include:

- Logos
- Favicons
- User avatars

These are stored using the `public_assets` filesystem disk.

Default location:

```text
public/uploads
```

Private business documents must never use this disk.

---

## User Profiles

Users can manage:

- Name
- Email
- Phone
- Job title
- Biography
- Avatar
- Password

Avatar uploads use generated filenames and validated image types.

---

## Activity & Audit Logs

Administrative actions can be recorded using the reusable activity logging service.

Logs support:

- User attribution
- Event type
- Subject
- Before/after data
- Search
- Filtering
- Sorting
- Pagination
- CSV export

Activity logging is intended for important business and administrative actions rather than logging every application request.

---

## Private Document Management

The starter includes secure document management for sensitive files.

Documents are stored under:

```text
storage/app/private
```

Private files are not directly exposed through the web server.

Downloads must pass through authenticated and authorized Laravel controllers.

Features include:

- Private uploads
- Generated UUID filenames
- Original filename preservation
- MIME information
- File size tracking
- Categories
- Polymorphic document ownership
- Authorized downloads
- Authorized deletion
- Orphan cleanup when database persistence fails

Supported file validation can be customized per project.

---

## Data Tables

Reusable table patterns support:

- Search
- Filtering
- Status filters
- Date filters
- Sorting
- Pagination
- Active filter display
- Empty states

Filtering logic can be extracted into reusable query classes so the same filters can power both screen results and exports.

---

## CSV Exports

The starter provides reusable streaming CSV exports.

Current exports include:

- Users
- Activity logs

Exports:

- Respect permissions
- Respect active filters
- Use streamed responses
- Use database cursors where appropriate
- Include UTF-8 BOM support

This avoids requiring an Excel library for simple tabular exports.

Project-specific XLSX or PDF exports can be added when required.

---

## Notifications

Database notifications are included.

Users can:

- View notifications
- Mark notifications as read
- Mark all notifications as read

Administrators with the appropriate permission can send notifications to active users.

Notifications support:

- Title
- Message
- Type
- Internal action URL
- Database delivery
- Optional email delivery

Supported types:

- `info`
- `success`
- `warning`
- `danger`

Internal notification URLs are validated to prevent external redirect abuse.

---

## Email Notifications

Email delivery uses Laravel's mail system and remains provider-neutral.

The starter can therefore work with SMTP providers such as:

- Brevo
- Zoho Mail
- Microsoft 365
- Amazon SES
- Postmark
- Resend
- Other SMTP-compatible providers

Queued notification emails use Laravel queues.

Database notification delivery remains immediate while email delivery can run asynchronously.

---

## Queues

The default queue driver is:

```env
QUEUE_CONNECTION=database
```

The Laravel jobs migration is included.

Database queue jobs are configured to dispatch after successful database commits.

This prevents queued work from running against data from a transaction that later rolls back.

### Local Worker

```bash
php artisan queue:work
```

### Shared Hosting / cPanel

Where persistent workers are unavailable, a cron job can execute:

```bash
php artisan queue:work --stop-when-empty --tries=3 --timeout=60
```

### VPS / DigitalOcean

Production VPS deployments should use a persistent queue worker managed by Supervisor or systemd.

Example worker command:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=60
```

---

## Security

The starter includes several production security defaults.

### Authentication Security

- Login throttling
- Password-reset throttling
- Email verification throttling
- Active-user enforcement

### Authorization

- Role and permission checks
- Super administrator protection
- Final super administrator protection

### Upload Security

- Private document storage
- Generated storage filenames
- MIME/type validation
- File-size validation
- Public/private filesystem separation

### HTTP Security Headers

Responses include:

```text
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy
```

Content Security Policy is intentionally not globally enforced by the starter because individual projects may require different external resources and integrations.

### Rate Limiting

Rate limits are applied to sensitive or resource-intensive operations including:

- Login
- Password-reset requests
- Email verification
- Notification sending
- Data exports

---

## Health Check

Laravel's application health endpoint is available at:

```text
/up
```

This can be used by hosting platforms and uptime-monitoring services.

Do not expose application diagnostics, environment variables, database information, or other sensitive system details through this endpoint.

---

## Requirements

Before installing, ensure the environment provides:

- PHP 8.3 or later
- Composer
- MySQL
- Node.js
- npm

Required PHP extensions depend on the Laravel installation and enabled project features.

---

## Installation

Clone the repository:

```bash
git clone https://github.com/olowott/bitva-laravel-starter.git
cd bitva-laravel-starter
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitva_starter
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Start frontend development:

```bash
npm run dev
```

Run Laravel using your preferred local development environment.

Examples include:

- Laravel Valet
- Laravel Herd
- `php artisan serve`

---

## Environment Configuration

Important application settings include:

```env
APP_NAME="BitVa Starter"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC

FILESYSTEM_DISK=local

QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=debug
LOG_DAILY_DAYS=14

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Never commit real production credentials or `.env` files.

---

## Production Environment

Typical production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=info
LOG_DAILY_DAYS=14

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
```

Production SMTP credentials should exist only in the server's `.env`.

---

## Production Optimization

After deployment:

```bash
php artisan migrate --force
php artisan optimize
```

Build frontend assets before deployment or on the production server:

```bash
npm run build
```

When deploying new application code while queue workers are running:

```bash
php artisan queue:restart
```

Ensure the following directories are writable by the application:

```text
storage
bootstrap/cache
```

---

## Storage

The application separates private and public storage.

### Private

```text
storage/app/private
```

Used for sensitive documents.

### Laravel Public Disk

```text
storage/app/public
```

May be exposed through:

```text
public/storage
```

using:

```bash
php artisan storage:link
```

### BitVa Public Assets

```text
public/uploads
```

Used for intentionally public branding assets and avatars.

Never store sensitive documents inside a public disk.

---

## Upload Limits

Laravel validation limits do not override PHP or web-server upload limits.

Production servers may also require adjustment of settings such as:

```ini
upload_max_filesize
post_max_size
```

Web-server limits may also apply.

---

## Testing

Run the complete test suite:

```bash
php artisan test
```

Before merging or releasing a version, the full test suite should pass.

---

## Frontend Development

During development:

```bash
npm run dev
```

For a production build:

```bash
npm run build
```

The production build should be verified before releases.

---

## Git Workflow

Development should occur on feature branches.

Example:

```bash
git checkout -b feature/example-feature
```

After implementation:

```bash
php artisan test
npm run build
```

Commit and push the feature branch before merging into `main`.

The `main` branch should remain the latest stable version of the starter.

---

## Versioning

The project follows semantic versioning.

Examples:

```text
v1.0.0  First production-ready starter
v1.1.0  New reusable core capability
v1.1.1  Bug/security fix
v1.2.0  Additional backwards-compatible core improvements
v2.0.0  Major architectural or breaking changes
```

Project-specific functionality should generally not be added to the core starter.

---

## What Belongs in the Starter?

A feature should be considered for the starter when nearly every BitVa Laravel project benefits from it.

Examples:

- Authentication
- User management
- Permissions
- Settings
- Notifications
- Audit logs
- Private uploads
- Data tables
- Exports

Features needed only by certain categories of applications should be developed as optional modules.

Examples:

- Dynamic forms
- Payments
- Invoicing
- Events
- Client management
- Project management
- Appointment booking
- KYC/account opening

Project-specific business logic should remain inside the individual project.

---

## Optional Modules

Future reusable modules may include:

- BitVa Forms
- Client Management
- Events & Registration
- Payments
- Invoicing
- Project Management
- Reporting

These modules should build on top of the stable starter rather than becoming mandatory starter dependencies.

---

## Roadmap

### Foundation

- [x] Laravel 12 foundation
- [x] PHP 8.3+
- [x] MySQL
- [x] Tailwind CSS 4
- [x] Alpine.js
- [x] Blade architecture
- [x] Laravel Breeze authentication

### Core Platform

- [x] Admin shell
- [x] Reusable Blade UI components
- [x] User management
- [x] Roles and permissions
- [x] Settings
- [x] Branding
- [x] User profiles and avatars
- [x] Activity logs
- [x] Reusable data tables
- [x] Private document management
- [x] CSV exports
- [x] Database notifications
- [x] Email notifications
- [x] Admin notification composer

### Production Polish

- [x] Authorization hardening
- [x] Upload/storage hardening
- [x] Security headers
- [x] Rate limiting
- [x] Production session defaults
- [x] Production logging
- [x] Queue production configuration
- [ ] Dark mode
- [ ] Final UI cleanup
- [ ] Deployment documentation
- [ ] v1.0.0 release

---

## Project Philosophy

The BitVa Laravel Starter is not intended to become a monolithic application containing every feature BitVa Tech has ever built.

It is the stable foundation.

The intended workflow is:

```text
New Project
    ↓
BitVa Laravel Starter
    ↓
Project Repository
    ↓
Optional Reusable Modules
    ↓
Project-Specific Business Logic
    ↓
Testing
    ↓
Deployment
```

This keeps new applications fast to start while allowing each project to remain focused on its actual requirements.

---

## License

This starter is maintained by BitVa Tech.

Licensing terms should be reviewed before public or commercial distribution.
