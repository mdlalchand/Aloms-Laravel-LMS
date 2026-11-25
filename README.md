# Redwan's Method — Custom LMS (Laravel 12)

A complete, production-ready Learning Management System modelled on
**redwansmethod.com**: courses, curriculum builder, video player, MCQ/CQ exams,
e-books, cart + bKash/Nagad checkout, certificates, reviews and a full admin panel.

Built with **Laravel 12 + Blade + Tailwind CSS**. The compiled Vite bundle is
already shipped in `public/build`, so it runs without `npm` (Tailwind falls back
to the CDN if you delete that folder).

---

## 1. Requirements

| Requirement | Version |
|---|---|
| PHP | 8.2+ (with `pdo_sqlite` / `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`) |
| Composer | 2.x |
| Database | MySQL 8 / MariaDB 10.4+ **or** SQLite (default) |

## 2. Installation

```bash
# 1. dependencies
composer install

# 2. environment
cp .env.example .env
php artisan key:generate

# 3. database — SQLite (already configured in .env.example)
touch database/database.sqlite
#    …or MySQL: set DB_CONNECTION=mysql + DB_DATABASE/DB_USERNAME/DB_PASSWORD in .env

# 4. schema + demo data
php artisan migrate --seed

# 5. public storage symlink (course thumbnails, e-book PDFs, avatars)
php artisan storage:link

# 6. run
php artisan serve
```

Open <http://127.0.0.1:8000>.

> **Verified fresh install:** a clean unzip of this release (no `vendor`, no
> `node_modules`) installs with the six steps above and runs the full suite
> green — 16 tests / 141 assertions. The writable directories
> (`storage/framework/{cache,sessions,views}`, `storage/logs`,
> `storage/app/public`, `bootstrap/cache`) ship with `.gitignore` placeholders;
> if your unzip/mover drops empty folders, recreate them with
> `mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/app/public bootstrap/cache`
> before serving, otherwise Blade/session writes fail with HTTP 500.

### Updating an existing install (no data loss)

```bash
./update.sh              # backup DB + .env -> maintenance mode -> composer ->
                         # php artisan migrate --force -> rebuild caches -> up
./update.sh --dry-run    # show every step without changing anything
./backup.sh              # database + .env only (safe for cron)
```

`migrate` only applies **new** migration files; every migration in this project
is additive, so existing users/orders/enrolments are untouched. Never run
`migrate:fresh`, `db:wipe`, `migrate:refresh` or `db:seed` on a live database.
Full step-by-step (VPS, cPanel, manual update, rollback, restore, backup cron)
lives in **[UPDATE.md](UPDATE.md)**.

### Demo accounts (created by the seeder)

| Role | Email | Password | Lands on |
|---|---|---|---|
| Admin | `admin@lms.test` | `password` | `/admin` |
| Instructor / teacher | `teacher1@lms.test` | `password` | `/teacher` |
| Student | `student@lms.test` | `password` | `/my` |
| Guardian | `guardian1@lms.test` | `password` | `/guardian` |
| Branch manager | `manager1@lms.test` | `password` | `/branch` |

> Every account uses the password `password`. Change them before going live
> (`/admin/students`). Each role is redirected to its own dashboard automatically
> and blocked from the others by `RoleMiddleware`; disabled users are logged out.

## 3. What's included

### Public site
- **Home** – hero, live stats, learning paths, featured & latest courses, student
  reviews, e-book strip, CTA (all DB driven).
- **Course catalogue** – search, category filter, level filter, sort by
  newest / popular / rating / price, pagination.
- **Course landing page** – outcomes, description, expandable curriculum with
  free-preview badges, live-class list, exam list, reviews, sticky buy box with
  progress bar for enrolled students.
- **E-books store** – cover grid, detail page, in-browser PDF reader.
- **Certificate verification** – public `/verify/{code}` page.
- **Cart & checkout** – session cart (courses + e-books), coupon codes,
  order summary, billing form, free-order handling.
- **Manual mobile-banking payment** – bKash / Nagad / Rocket / bank, student
  submits transaction ID, admin verifies → access granted instantly.

### Student area (`/my`)
- Dashboard: stats, "continue learning", progress bars, upcoming exams, activity.
- **Video course player** – YouTube/Vimeo/mp4 embed, lesson notes, attachments,
  sidebar curriculum with completion ticks, mark-complete, prev/next,
  announcements, live progress %.
- **Exam engine** – timed attempts, autosave every 15 s, question navigator,
  resume unfinished attempt, negative marking, auto-grading, per-question review
  with explanations, pass/fail, attempt limits.
- My courses, orders, e-books, certificates (printable), profile & password.

### Multi-role organisation layer
- **Branches** (`/admin/branches`) – CRUD, activate/close, per-branch student &
  user counts, revenue roll-up, branch-level student roster.
- **Home slider** (`/admin/sliders`) – CRUD, image upload or URL, ordering,
  show/hide; the public hero renders active slides with autoplay dots/arrows.
- **Live classes** (`/admin/live-classes`) – schedule per course/branch, meeting
  link, start/end time, join button inside the student dashboard.
- **Teacher portal** (`/teacher`) – my courses, my students, upcoming live
  classes, exam results; instructors can only touch their own courses.
- **Guardian portal** (`/guardian`) – linked children, per-child attendance,
  results, exam history and enrolled courses (read-only).
- **Branch manager portal** (`/branch`) – students, staff, enrolments, revenue
  and live classes **scoped to the manager's own branch** only.

### Admin / instructor panel (`/admin`)
- Dashboard: revenue, students, enrollments, pending orders, 6-month revenue
  chart, top courses, recent orders/enrollments, pending-review queue.
- **Courses** – full CRUD, thumbnail upload, outcomes, pricing/discount,
  publish & feature flags, certificate toggle, SEO fields.
- **Curriculum builder** – sections and lessons inline (video, text, PDF, live,
  exam types), free-preview flag, attachments.
- **Exams** – CRUD, schedule window, duration, negative marking, attempt limit,
  shuffle; **question builder** for MCQ (4 options + correct answer, explanation)
  and descriptive CQ/short questions.
- Categories, e-books (cover + PDF upload), announcements, coupons,
  enrollments (manual enrol / remove), students & staff (roles, activate,
  disable, password reset), orders (status change, payment verification),
  review moderation, site settings (branding, contact, bKash/Nagad numbers).
- Role protection: instructors only see/manage **their own** courses,
  students can never reach `/admin` (403).

### Architecture

```
app/
├── Http/Controllers/
│   ├── Auth/AuthController.php          login / register / logout
│   ├── HomeController.php               home page
│   ├── CourseController.php             catalogue, detail, free enrol
│   ├── EbookController.php              store + reader
│   ├── CartController.php               session cart, coupons, totals
│   ├── CheckoutController.php           orders + manual payment
│   ├── ReviewController.php             course reviews
│   ├── Student/                         dashboard, player, exams, certificate
│   ├── Teacher/                         teacher portal (own courses/students)
│   ├── Guardian/                        guardian portal (linked children)
│   ├── Branch/                          branch-scoped manager portal
│   └── Admin/                           full back-office (20+ controllers)
├── Models/                              21 Eloquent models
├── Services/OrderService.php            fulfilment + certificate issuing
└── Http/Middleware/RoleMiddleware.php   role:admin,instructor guard
```

Database (5 migrations): `categories, courses, sections, lessons, enrollments,
lesson_progress, exams, questions, options, exam_attempts, attempt_answers,
coupons, orders, order_items, payments, ebooks, ebook_purchases, reviews,
certificates, announcements, live_classes, settings` (+ `users` extended with
`role, phone, avatar, bio, designation, is_active`).

## 4. Tests

```bash
php artisan test --filter=LmsFlowTest
```

16 feature tests / 141 assertions cover: public pages, student dashboard & player,
enrolment authorisation, cart → coupon → checkout → admin payment verification →
access granted, exam auto-grading, admin CRUD (courses, sections, lessons, exams,
questions), role protection, each role's dashboard + auto-redirect, teacher and
guardian portals, branch scoping, and the organisation modules (branches,
sliders, live classes) - including toggle/delete flows.

```bash
php artisan test            # full suite
```

The suite is written to be deterministic: the seeder randomises enrollments and
branch assignment, so tests resolve their fixtures through explicit queries
(courses the student does not own, students outside the guardian's children,
students outside the manager's branch) instead of relying on seed order.

## 5. Production notes

1. **Tailwind**: the production bundle (`public/build`) is included, so
   `@vite()` is used out of the box. To restyle, edit the Blade files and run
   `npm install && npm run build`. Delete `public/build` and the layout falls
   back to the Tailwind CDN (`resources/views/partials/assets.blade.php`).
2. **Payments**: the checkout currently uses manual bKash/Nagad verification
   (set the numbers in *Admin → Settings*). To plug in a gateway (SSLCommerz,
   bKash PGW, Stripe), create the payment in `CheckoutController::store`, then
   call `OrderService::fulfill($order)` from the gateway callback — that single
   call grants course/e-book access.
3. **Media**: use a CDN or S3 by changing `FILESYSTEM_DISK` and the `->store(..., 'public')`
   calls to your disk; YouTube/Vimeo URLs are already supported for videos.
4. Set `APP_ENV=production`, `APP_DEBUG=false`, run `php artisan config:cache`
   `route:cache` `view:cache`, and point the web root at `public/`.
5. Seed data uses Unsplash course photos, Unsplash slider images and
   `ui-avatars.com` avatars; upload real thumbnails in the admin panel to
   replace them.
6. Routes: 130+ named routes; run `php artisan route:list` for the full map.
