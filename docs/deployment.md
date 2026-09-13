# BitVa Laravel Starter — Deployment Guide

This guide covers production deployment of applications built with the BitVa Laravel Starter.

Supported deployment patterns include:

- Shared hosting / cPanel / Hostinger
- VPS / DigitalOcean / similar Linux servers

Project-specific infrastructure may require additional configuration.

---

# 1. Pre-Deployment Checklist

Before deploying, verify the application locally.

Run:

```bash
php artisan test
```

All tests must pass.

Build the production frontend:

```bash
npm run build
```

Check the repository:

```bash
git status
```

Confirm that no sensitive or unnecessary files are being committed.

Never commit:

```text
.env
vendor/
node_modules/
SMTP credentials
API keys
database passwords
private keys
production secrets
```

Confirm the production build exists:

```text
public/build/
```

---

# 2. Production Environment

The production server should use:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
APP_TIMEZONE=UTC
```

`APP_DEBUG` must never be enabled on a public production application.

Generate an application key if the deployment does not already have one:

```bash
php artisan key:generate
```

Do not regenerate `APP_KEY` on an existing production application without understanding the consequences.

Changing the key can invalidate encrypted application data.

---

# 3. Database

Create a production MySQL database and database user.

Configure:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=database_user
DB_PASSWORD=secure_password
```

Run production migrations:

```bash
php artisan migrate --force
```

Do not use:

```bash
php artisan migrate:fresh
```

on an existing production application.

`migrate:fresh` destroys existing database tables and data.

---

# 4. Sessions

Recommended production configuration:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_PARTITIONED_COOKIE=false
```

Only use:

```env
SESSION_SECURE_COOKIE=true
```

when the application is served through HTTPS.

Avoid setting a shared session domain unless the project intentionally requires sessions across multiple subdomains.

---

# 5. Logging

Recommended production configuration:

```env
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=info
LOG_DAILY_DAYS=14
```

Application logs are stored under:

```text
storage/logs/
```

Production logs must not be publicly accessible.

---

# 6. Mail

The starter is mail-provider neutral.

Example SMTP configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=operations@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Use the exact SMTP configuration supplied by the selected provider.

Possible providers include:

- Brevo
- Zoho Mail
- Microsoft 365
- Amazon SES
- Postmark
- Resend
- Other SMTP-compatible services

Do not store SMTP credentials in Git.

After configuring mail, send a real application notification and confirm delivery.

---

# 7. Queues

The recommended queue connection is:

```env
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids
```

The worker timeout should remain lower than the database queue `retry_after` value.

The starter defaults are designed around:

```text
Worker timeout: 60 seconds
retry_after:    90 seconds
```

---

# 8. Queue Worker — Shared Hosting

Shared hosting providers may not provide Supervisor or persistent background processes.

A cron-based worker can be used instead.

Example:

```bash
php artisan queue:work --stop-when-empty --tries=3 --timeout=60
```

A cron job can execute this command regularly.

Example:

```cron
* * * * * cd /path/to/application && php artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> /dev/null 2>&1
```

Replace the application path and PHP executable with values appropriate for the hosting provider.

Some shared hosts may not allow one-minute cron intervals. Use the shortest reliable interval available.

---

# 9. Queue Worker — VPS

For VPS deployments, use a persistent queue worker managed by Supervisor or systemd.

Example:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=60
```

The process manager should:

- Start the worker automatically
- Restart failed workers
- Start workers after server reboot
- Capture worker logs

After deploying application code, restart existing workers:

```bash
php artisan queue:restart
```

---

# 10. Failed Jobs

Check failed queue jobs with:

```bash
php artisan queue:failed
```

Retry a failed job:

```bash
php artisan queue:retry <job-id>
```

Retry all failed jobs:

```bash
php artisan queue:retry all
```

Delete a failed job only after the cause has been investigated.

---

# 11. Private Storage

Sensitive documents are stored under:

```text
storage/app/private
```

These files must not be directly exposed through the web server.

Private documents must be downloaded through authenticated and authorized application routes.

Never move private documents into:

```text
public/
public/uploads/
storage/app/public/
```

---

# 12. Public Assets

Intentionally public BitVa application assets are stored under:

```text
public/uploads/
```

Examples include:

- Logos
- Favicons
- User avatars

The standard Laravel public disk may also use:

```text
storage/app/public/
```

with:

```text
public/storage
```

created through:

```bash
php artisan storage:link
```

Never store confidential documents on either public filesystem.

---

# 13. File Upload Limits

Application validation does not override PHP or web-server limits.

Check PHP settings such as:

```ini
upload_max_filesize
post_max_size
```

Depending on the server, Nginx, Apache, LiteSpeed, or the hosting control panel may impose additional upload limits.

The server limit must accommodate the application's maximum permitted upload size.

---

# 14. File Permissions

Laravel requires write access to:

```text
storage/
bootstrap/cache/
```

Do not solve permission problems by giving the entire application unrestricted permissions.

Avoid:

```bash
chmod -R 777 .
```

Use the correct server user/group and minimum required permissions.

---

# 15. HTTPS

Production applications should use HTTPS.

Set:

```env
APP_URL=https://example.com
SESSION_SECURE_COOKIE=true
```

Do not globally force HTTPS inside Laravel unless the hosting architecture specifically requires it.

If the application is behind Cloudflare, a load balancer, or another reverse proxy, configure trusted proxies specifically for that environment.

Do not configure the starter to trust every proxy by default.

---

# 16. Security Headers

The starter provides baseline response headers including:

```text
X-Content-Type-Options
X-Frame-Options
Referrer-Policy
Permissions-Policy
```

Review the `Permissions-Policy` when building applications requiring browser capabilities such as:

- Geolocation
- Camera
- Microphone

For example, a guard-attendance application requiring GPS will need its geolocation policy adjusted.

Content Security Policy should be designed per project rather than blindly copied between applications.

---

# 17. Production Optimization

After deployment:

```bash
php artisan optimize
```

This prepares Laravel's production caches.

After changing environment or configuration values:

```bash
php artisan optimize:clear
php artisan optimize
```

Do not leave production configuration uncached unnecessarily.

---

# 18. Frontend Assets

Build frontend assets using:

```bash
npm run build
```

Production should serve the compiled Vite assets.

Do not run:

```bash
npm run dev
```

as the production frontend process.

---

# 19. Health Check

The application exposes:

```text
/up
```

After deployment, verify:

```text
https://example.com/up
```

returns a healthy response.

This endpoint can be used by uptime-monitoring systems.

Do not expose sensitive diagnostics through the public health endpoint.

---

# 20. cPanel / Hostinger Deployment

A typical shared-hosting deployment flow is:

```text
1. Create domain/subdomain
2. Configure the document root
3. Upload or clone the repository
4. Create production .env
5. Install Composer dependencies
6. Create MySQL database/user
7. Run migrations
8. Build/upload Vite assets
9. Configure storage
10. Configure writable directories
11. Configure SMTP
12. Configure queue cron
13. Enable HTTPS
14. Optimize Laravel
15. Test application
```

The web-accessible document root should point to Laravel's:

```text
public/
```

directory whenever the hosting environment allows it.

Do not expose the project root as the website document root.

---

# 21. DigitalOcean / VPS Deployment

A typical VPS deployment flow is:

```text
1. Provision server
2. Install web server
3. Install PHP and required extensions
4. Install Composer
5. Install/configure MySQL
6. Clone repository
7. Create production .env
8. Install Composer dependencies
9. Run migrations
10. Build frontend assets
11. Configure Nginx/Apache document root
12. Configure file permissions
13. Configure SSL
14. Configure queue worker
15. Optimize Laravel
16. Test application
```

The web server root should point to:

```text
/path/to/application/public
```

---

# 22. Deployment Commands

A typical update deployment may use:

```bash
git pull origin main

composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader

php artisan migrate --force

npm ci
npm run build

php artisan optimize:clear
php artisan optimize

php artisan queue:restart
```

Exact commands depend on the hosting environment.

If frontend assets are built before deployment, Node.js does not need to be installed on the production server.

---

# 23. Post-Deployment Smoke Test

After every production deployment verify:

- Homepage/login loads
- HTTPS works
- Login works
- Logout works
- Password reset can be requested
- Admin dashboard loads
- Permissions work
- User management works
- Branding assets load
- Avatar upload works
- Private document upload works
- Private document download requires authorization
- CSV export works
- Database notification works
- Email notification works
- Queue worker processes jobs
- `/up` returns healthy
- Production errors do not expose stack traces

Also check:

```bash
php artisan queue:failed
```

and inspect:

```text
storage/logs/
```

for unexpected production errors.

---

# 24. Rollback Awareness

Before significant production deployments:

- Back up the database
- Back up user-uploaded files
- Know the previous stable Git revision/tag
- Review migrations for destructive changes

Code can often be rolled back quickly.

Database schema and user data may not be as easy to reverse.

Treat production migrations carefully.

---

# 25. Deployment Principle

A successful deployment is not simply:

```text
"The website opens."
```

A BitVa Laravel deployment is considered complete when:

```text
Application
+ Database
+ HTTPS
+ Storage
+ Mail
+ Queue
+ Permissions
+ Security
+ Monitoring
```

have all been verified.
