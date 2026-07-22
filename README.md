# OneTrack — Transport & Shipment Booking Platform

A Laravel 12 web application for booking, pricing, and tracking transport shipments between cities — with a customer-facing booking flow and an admin CRM to manage routes, leads, payments, and quotes.

## What this project does

Customers add shipment items (dimensions, weight, pickup city, delivery city), get a live price estimate, review everything in a cart, and submit it for approval. Once submitted, the request becomes a **transport lead** that stays visible (read-only) in the customer's cart until an admin approves, rejects, or dispatches it. Customers can track any shipment by tracking number on a public Track & Trace page.

The same flow exists for **warehouse storage**: customers add items (dimensions, weight), pick a registered warehouse, give a pickup address/date and a number of storage days, get a live estimate, and submit it as a **warehouse lead**, trackable the same way as a shipment.

Admins manage the whole lifecycle from a separate CRM-style panel: city routes and their rates, warehouses and their rates, an item-type price catalog, incoming shipment and warehouse leads (approve/reject/dispatch/deliver), payments, and generated quotes.

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
- **Warehouse / Storage** (`resources/views/web/warehouse/`) — add an item (name, type, dimensions, weight), pick a registered warehouse, give a pickup address, pickup date and number of storage days, get a live estimate, review it in a storage cart, submit it as a warehouse lead, and track it (Request Received → Pickup Confirmed → In Storage → Stored) the same way as a shipment

### Admin panel (`resources/views/admin`, under `/admin`, requires the `admin` role)
- **Users** — list, edit, approve, delete
- **City Routes** — from/to city, rate per weight (₹/kg), transit time (1–3 days), minimum fair charge, active/inactive
- **Transport Prices** — a catalog of item types (used for categorizing items, not for pricing)
- **Transport Leads** — the CRM queue: review, approve/reject/dispatch/deliver, record payments
- **Warehouse Registration** (`resources/views/admin/warehouse/index.blade.php`, `manage.blade.php`) — name, city, address, price per day per kg, minimum charge, active/inactive
- **Warehouse Leads** (`resources/views/admin/warehouse/leads.blade.php`, `manage-lead.blade.php`) — the same review/approve/reject/dispatch/deliver flow as Transport Leads, plus payment recording and an invoice PDF once a request is marked delivered; warehouse payments also get their own table on the Payments page
- **Quotes & Payments** — generated quote documents per lead, downloadable; payment records per lead
- **Auth Settings** — toggle email login / mobile login on or off, and whether new signups require admin approval

## Pricing model

Pricing is driven entirely by the **city route**:

- Each item's charge = `actual weight (kg) × route.rate_per_weight`
- The route's `min_charge` (a flat "fair charge") is added **once per shipment** (a shipment = items sharing the same route + pickup/delivery dates), not per item
- `transit_days` on the route is shown to the customer as an estimated delivery window — it's informational, not part of the price
- Item dimensions are still collected and shown (as volume in cft) for logistics purposes, but volume no longer affects the charge

See `app/Services/ShipmentPricingService.php` for the calculation and `WebController::aggregateShipmentBreakdown()` for how the once-per-shipment minimum charge is applied.

## Warehouse / Storage pricing model

Pricing is driven by the **warehouse** the customer picks, and follows the same "whichever is higher" billing rule as shipments:

- `chargeable_weight_kg = max(actual weight, volumetric weight)` — actual weight is `weight_kg × quantity`; volumetric weight comes from the item's L×W×H the same way it does for shipments
- Each item's charge = `chargeable_weight_kg × warehouse.price_per_day_per_kg × storage_days` — storage is billed per kg **per day**, unlike shipment's flat per-kg rate
- The warehouse's `min_charge` is applied **once per request** (a request = items sharing the same warehouse + pickup date), not per item — identical floor rule to a shipment's route `min_charge`

See `app/Services/WarehousePricingService.php` for the calculation and `WarehouseController::aggregateWarehouseBreakdown()` for how the once-per-request minimum charge is applied.

## Project structure

```
app/
  Http/Controllers/
    auth/AuthController.php        Login, register, OTP flows
    web/WebController.php          Customer-facing shipment pages & cart/booking logic
    web/WarehouseController.php    Customer-facing warehouse pages & cart/booking logic
    admin/AdminController.php      Admin CRUD (routes, warehouses, leads, users, prices, payments)
    admin/ApiController.php        JSON endpoints backing the admin DataTables
  Models/                          CityRoute, TransportCartItem, TransportLead, TransportQuote,
                                    Warehouse, WarehouseCartItem, WarehouseLead, WarehousePayment, ...
  Services/
    ShipmentPricingService.php     Core shipment pricing calculation
    WarehousePricingService.php    Core warehouse (per-day) pricing calculation
    TransportQuotePdfService.php   Quote document generation
    ShipmentInvoicePdfService.php  Shipment invoice document generation
    WarehouseInvoicePdfService.php Warehouse invoice document generation
    GuestCartService.php           Guest cart id handling (pre-login), shared by both cart tables
resources/views/
  web/                             Customer-facing Blade templates (flat shipment-*.blade.php files)
  web/warehouse/                   Customer-facing warehouse templates (subfolder, not flat)
  admin/                           Admin panel Blade templates (kaiadmin theme, flat files)
  admin/warehouse/                 Admin warehouse registration + lead templates (subfolder, not flat)
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

Unit tests cover the pricing services (`tests/Unit/ShipmentPricingServiceTest.php`, `tests/Unit/WarehousePricingServiceTest.php`).

## Notes for future work

- The `admin` middleware gate expects a `role` column on `users` — see `app/Models/User.php` and the `admin` route group in `routes/web.php`.
- `TransportServicePrice` (Transport Prices catalog) only categorizes item types today; it is **not** used in the pricing calculation. Warehouse items don't have an equivalent catalog — `item_type` is free text.
- If you're on MariaDB < 10.5.2, note that Laravel's column-rename migrations fall back to a "legacy" rename path that introspects the live schema — this only works against a real, reachable database (it can't run under `--pretend`).
- `warehouses.price_per_day_per_kg` / `min_charge` are stored as `decimal(10,2)`, unlike `city_routes`' equivalent integer columns — a deliberate deviation since a per-day-per-kg rate needs fractional precision.
- There is no `WarehouseQuote` table — `WarehouseInvoicePdfService` reads invoice data straight off `WarehouseLead`, the same way `ShipmentInvoicePdfService` already does off `TransportLead` (it never touches `TransportQuote::quote_data` either).
