# OneTrack — Transport & Shipment Booking Platform

A Laravel 12 web application for booking, pricing, and tracking transport shipments between cities — with a customer-facing booking flow and an admin CRM to manage routes, leads, payments, and quotes.

## What this project does

Customers add shipment items (dimensions, weight, pickup city, delivery city), get a live price estimate, review everything in a cart, and submit it for approval. Once submitted, the request becomes a **transport lead** that stays visible (read-only) in the customer's cart until an admin approves, rejects, or dispatches it. Customers can track any shipment by tracking number on a public Track & Trace page.

Admins manage the whole lifecycle from a separate CRM-style panel: city routes and their rates, an item-type price catalog, incoming leads (approve/reject/dispatch/deliver), payments, and generated quotes.

## Tech stack

- **Backend:** PHP 8.2+, Laravel 12
- **Database:** MySQL / MariaDB (developed against MariaDB 10.4 via XAMPP)
- **Frontend:** Blade templates, Bootstrap 5, vanilla JS (customer pages) and jQuery + DataTables + Select2 (admin panel, using the "kaiadmin" admin theme)
- **Build tooling:** Vite + Tailwind (currently only used for the default Laravel scaffolding, not for the styled pages themselves — those pages ship their own CSS inline)
- **Auth:** Custom OTP-based login (email OTP or mobile OTP), plus password login and registration; optional admin-approval gate for new accounts

## Key features

### Customer-facing (`resources/views/web`)
- **Login / Register** — email OTP, mobile OTP, or password; "Remember me" keeps the session alive until explicit logout
- **Add Shipment** — pick a route, add one or more items (name, type, quantity, dimensions, weight), and get a **live price estimate** before saving anything (AJAX, no page reload)
- **Shipment Cart** — review items grouped by shipment (route + dates), edit/delete items, see a running total; once checked out, items become read-only with a "Pending Approval" badge until an admin decides on them
- **Track & Trace** — look up any shipment by tracking number; animated progress stepper (Request Received → Pickup Approved → In Transit → Delivered) with a rejected/cancelled state
- **My Leads / User Profile** — a customer's own submitted shipments and account details

### Admin panel (`resources/views/admin`, under `/admin`, requires the `admin` role)
- **Users** — list, edit, approve, delete
- **City Routes** — from/to city, rate per weight (₹/kg), transit time (1–3 days), minimum fair charge, active/inactive
- **Transport Prices** — a catalog of item types (used for categorizing items, not for pricing)
- **Transport Leads** — the CRM queue: review, approve/reject/dispatch/deliver, record payments
- **Quotes & Payments** — generated quote documents per lead, downloadable; payment records per lead
- **Auth Settings** — toggle email login / mobile login on or off, and whether new signups require admin approval

## Pricing model

Pricing is driven entirely by the **city route**:

- Each item's charge = `actual weight (kg) × route.rate_per_weight`
- The route's `min_charge` (a flat "fair charge") is added **once per shipment** (a shipment = items sharing the same route + pickup/delivery dates), not per item
- `transit_days` on the route is shown to the customer as an estimated delivery window — it's informational, not part of the price
- Item dimensions are still collected and shown (as volume in cft) for logistics purposes, but volume no longer affects the charge

See `app/Services/ShipmentPricingService.php` for the calculation and `WebController::aggregateShipmentBreakdown()` for how the once-per-shipment minimum charge is applied.

## Project structure

```
app/
  Http/Controllers/
    auth/AuthController.php        Login, register, OTP flows
    web/WebController.php          Customer-facing pages & cart/booking logic
    admin/AdminController.php      Admin CRUD (routes, leads, users, prices, payments)
    admin/ApiController.php        JSON endpoints backing the admin DataTables
  Models/                          CityRoute, TransportCartItem, TransportLead, TransportQuote, ...
  Services/
    ShipmentPricingService.php     Core pricing calculation
    TransportQuotePdfService.php   Quote document generation
    ShipmentInvoicePdfService.php  Invoice document generation
    GuestCartService.php           Guest cart id handling (pre-login)
resources/views/
  web/                             Customer-facing Blade templates
  admin/                           Admin panel Blade templates (kaiadmin theme)
database/
  migrations/                      Schema history
  seeders/TransportDemoSeeder.php  Demo users, routes, cart item, and leads (not auto-run)
routes/web.php                     All customer + admin routes
```

## Getting started (local / XAMPP)

1. **Install dependencies**
   ```
   composer install
   npm install
   ```
2. **Environment**
   ```
   copy .env.example .env
   php artisan key:generate
   ```
   Then in `.env`, point the DB connection at MySQL/MariaDB (the `.env.example` ships with SQLite defaults):
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=transport
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Also set mail credentials (`MAIL_*`) if you want email OTP login to actually send mail — otherwise it will fail when a customer requests an email OTP.
3. **Database**
   ```
   php artisan migrate
   ```
4. **Demo data (optional but recommended)** — this seeder is intentionally **not** wired into the default `db:seed`, so run it explicitly:
   ```
   php artisan db:seed --class=TransportDemoSeeder
   ```
   This creates demo accounts, a few city routes, a cart item, and a handful of transport leads in different statuses.
5. **Serve** — via XAMPP/Apache at `http://localhost/transport/public/`, or for a quick standalone server:
   ```
   php artisan serve
   ```

### Demo accounts (after seeding)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@transport.test` | `password` |
| Customer | `customer@transport.test` | `password` |

## Running tests

```
php artisan test
```

Unit tests cover the pricing service (`tests/Unit/ShipmentPricingServiceTest.php`).

## Notes for future work

- The `admin` middleware gate expects a `role` column on `users` — see `app/Models/User.php` and the `admin` route group in `routes/web.php`.
- `TransportServicePrice` (Transport Prices catalog) only categorizes item types today; it is **not** used in the pricing calculation.
- If you're on MariaDB < 10.5.2, note that Laravel's column-rename migrations fall back to a "legacy" rename path that introspects the live schema — this only works against a real, reachable database (it can't run under `--pretend`).
