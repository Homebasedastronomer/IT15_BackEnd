# IT15_BACKEND - Enrollment System API

Laravel backend API for the IT15 Enrollment System.

## 1. Required Deliverables Coverage

- Complete backend source code: included in this repository
- Detailed README with setup instructions: this file
- .env.example with required environment variables: included at project root
- API documentation (endpoints and expected responses): see API_DOCUMENTATION.md
- List of technologies with versions: see Technology Stack section

## 2. Technology Stack (with versions)

### Core Backend

- PHP: ^8.2
- Laravel Framework: ^12.0
- Laravel Sanctum: ^4.3
- Laravel Fortify: ^1.30
- MySQL or SQLite (supported by Laravel configuration)

### Additional Packages

- inertiajs/inertia-laravel: ^2.0
- laravel/wayfinder: ^0.1.9
- laravel/tinker: ^2.10.1

### Tooling

- PHPUnit: ^11.5.3
- Laravel Pint: ^1.24

## 3. Setup Instructions

Use the following commands in order.

### Backend Setup

```bash
cd IT15_BackEnd
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Default backend URL:

- http://127.0.0.1:8000
- API base URL: http://127.0.0.1:8000/api

## 4. Default API Test Credentials

These credentials are created by `php artisan migrate --seed`:

- Email: registrar@dollente.edu
- Password: password

## 5. Environment Variables

Use `.env.example` as template and configure at least:

- APP_NAME
- APP_ENV
- APP_KEY
- APP_DEBUG
- APP_URL
- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- SANCTUM_STATEFUL_DOMAINS (if SPA stateful flow is needed)

## 6. API Documentation

Detailed endpoint documentation with expected request and response payloads is in:

- API_DOCUMENTATION.md

## 7. Run Validation Commands

```bash
php artisan route:list --path=api
php artisan migrate --seed
```

## 8. Submission Documentation Checklist

- Screenshots of the working application (minimum 5)
- API documentation (completed)
- List of technologies with versions (completed)
- 3-5 minute video demonstration