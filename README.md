# Credit Union System

Credit Union System is a multi-branch member signature-card and user accountability platform built for credit union operations. It is designed to help staff register members accurately, help branch admins supervise branch activity, and help the central admin monitor performance across all branches.

## What the application does

- Staff create member signature-card records.
- Branch admins create staff accounts, reset staff passwords, review member records, update signature cards, and delete member records when required.
- The central admin creates branches, assigns branch admins, resets admin passwords, deletes users when necessary, and monitors activity across the institution.
- Every important action is tied to a user for accountability and operational review.
- Staff in the same branch can view signature cards created within their branch.
- Central admin does not open member signature cards directly; member-card access stays at branch level.

## Core roles

### Central Admin

- Creates branches from inside the application.
- Creates branch admin accounts and assigns them to branches.
- Resets branch admin passwords.
- Deletes admin or staff users when necessary.
- Views institution-wide activity and branch performance summaries.

### Branch Admin

- Creates staff accounts within their branch.
- Resets passwords for staff they created.
- Views member records and updates signature cards.
- Deletes member records after reviewing the delete preview.
- Monitors staff activity within their branch scope.

### Staff

- Creates member records.
- Uploads member signature-card images.
- Searches and views member records.

## Current operational features

- Role-based access control for central admin, branch admin, and staff.
- Branch management by central admin.
- User management with password reset and deletion controls.
- Member creation with preview before save.
- Member signature-card update preview.
- Member delete preview for accuracy.
- Branch-scoped member visibility so branch teams can collaborate without cross-branch exposure.
- Private authenticated delivery for signature-card images.
- Branch performance summary on the dashboard.
- CSV export for member reports and activity logs.
- Activity logging for operational accountability.
- Image upload optimization hook for signature images.
- Activity-log pruning command for retention control.

## Production setup flow

The production flow is intentionally simple:

1. Deploy the application.
2. Run migrations.
3. Seed the central admin account.
4. Log in as central admin.
5. Create branches from the application.
6. Create a branch admin for each branch.
7. Each branch admin creates staff accounts.

Branch records are not intended to be pre-seeded in production.

## Technology stack

- PHP 8.2+
- Laravel 12
- Blade templates
- MySQL or MariaDB
- Private local disk storage for signature-card images

## Environment requirements

- PHP 8.2 or higher
- MySQL 8+ or MariaDB 10.6+
- Web server such as Apache or Nginx
- Writable `storage` and `bootstrap/cache` directories
- PHP `gd` extension recommended for image optimization

If `gd` is not enabled, the application still works, but signature images will be stored without optimization.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
# Set CENTRAL_ADMIN_EMAIL and CENTRAL_ADMIN_PASSWORD in production before seeding
php artisan db:seed
npm install
npm run build
```

## Useful commands

Run the development stack:

```bash
composer run dev
```

Prune old activity logs to control database growth:

```bash
php artisan activity-logs:prune 365
```

Move older public signature files into private storage after upgrading:

```bash
php artisan signatures:migrate-private
```

Production should keep `SIGNATURE_ALLOW_PUBLIC_FALLBACK=false` so signature cards are served only from private storage after migration.

## Storage and growth reality

The main long-term storage cost is signature-card images, not users or logs.

Approximate growth if average signature image size remains near 1 MB:

- 10,000 members: about 10 GB
- 50,000 members: about 50 GB
- 100,000 members: about 100 GB

If the server has PHP `gd` enabled and images are optimized effectively, average storage can drop significantly.

## Recommended production server baseline

For the current planned scale of about 16 branches, 1 admin per branch, and about 5 staff per branch:

- 4 vCPU
- 8 GB RAM
- 200 GB SSD minimum

Recommended for more comfortable long-term growth:

- 4 vCPU
- 8 GB RAM
- 500 GB SSD

## Maintenance considerations

- Monitor disk usage because signature images will grow fastest.
- Back up the database and `storage/app/private`, because signature cards are served from private storage.
- If the application is upgraded from an older public-storage setup, migrate those files with `php artisan signatures:migrate-private` and then keep `SIGNATURE_ALLOW_PUBLIC_FALLBACK=false` in production.
- Prune old activity logs based on company retention policy.
- Enable PHP `gd` in production for smaller stored images.
- Review branch growth and storage use periodically.

## Initial central admin seed

The database seeder creates the initial central admin account using environment variables:

- `CENTRAL_ADMIN_EMAIL`
- `CENTRAL_ADMIN_PASSWORD`

In production, `CENTRAL_ADMIN_PASSWORD` must be set before running `php artisan db:seed`.
In non-production environments, the seeder generates a strong password and prints it in the console if one is not provided.
