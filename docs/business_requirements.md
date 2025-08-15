# PlusMove — Business Requirements Document (Delivery Tracking System)

**Version:** 1.0  
**Author:** (You / PlusMove Technical)  
**Date:** 2025-08-12

---

## 1. Executive summary
PlusMove needs a Laravel-based delivery management platform that reliably handles many daily deliveries across multiple cities. Each delivery can contain multiple packages and customers. The system must:
- Assign packages to drivers with minimal load,
- Track delivery progress in (near) real-time,
- Notify customers of status changes,
- Mark packages not delivered by end-of-day as “return” and include them in daily reports,
- Provide APIs for mobile/web clients and dashboards,
- Enforce role-based access and data security,
- Scale as traffic grows.

This document describes business & functional requirements, data design, API surface, workflows, non-functional requirements, implementation guidance (Laravel components), testing and acceptance criteria so developers can implement the system.

---

## 2. Goals & success criteria
**Primary goals**
- Reduce manual dispatch overhead via algorithmic assignment to drivers (minimal load).
- Provide accurate, near real-time delivery status and location visibility for customers and operations teams.
- Ensure no package is lost: undelivered by EOD → auto-return with audit trail & report.
- Secure, role-based access; auditable actions.

**Success criteria**
- System can handle thousands of deliveries/day across multiple cities.
- Average assignment and status update latency < 5 seconds for real-time flows.
- EOD job correctly marks returns (>=99% accuracy in test scenarios).
- Role-based rules prevent unauthorized data access (verified in tests).

---

## 3. Scope
**In scope**
- CRUD for core entities (Deliveries, Packages, Drivers, Vehicles, Customers, Cities, Orders).
- Driver assignment algorithm (minimal load, geo-aware).
- Driver mobile API for status updates & GPS pings.
- Real-time broadcast of driver location and status.
- Customer notifications (SMS/Email/Push) for status changes.
- End-of-day returns marking & daily reporting.
- Role-based authorization, API documentation, tests, deployment instructions.

**Out of scope (for MVP)**
- Dynamic route optimization (complex TSP) — optional later.
- Third-party logistics integrations (unless requested).
- Billing / invoicing features (optional future module).

---

## 4. Stakeholders & roles
- **Product Owner / PlusMove Ops** — accept features, business rules.
- **Dispatchers / City Managers** — assign/reassign deliveries, run reports.
- **Drivers** — receive assignments, update statuses, send GPS.
- **Customers (consignees)** — receive notifications, track packages.
- **Developers / DevOps** — implement, deploy, operate.
- **Support / QA** — test and validate.

System roles (RBAC):
- `admin` — full access across cities.
- `city_manager` — manage assets & deliveries within assigned city.
- `dispatcher` — create/manage deliveries, assign drivers.
- `driver` — limited mobile app access (own assignments).
- `customer` — view own deliveries & notifications.
- `readonly/auditor` — view reports/logs.

---

## 5. Core entities & data model (high level)

### Key tables (examples of important columns)
- `users` (id, name, email, password, role, city_id, phone, is_active, created_at)
- `cities` (id, name, timezone, geo_center)
- `drivers` (id, user_id, license_no, vehicle_id, status, current_load, last_ping_at, current_city_id)
- `vehicles` (id, reg_no, capacity, type)
- `orders` (id, order_no, customer_id, pickup_address, dropoff_address, city_id, status, created_at)
- `deliveries` (id, order_id, assigned_driver_id, status, scheduled_at, started_at, completed_at, city_id)
- `packages` (id, delivery_id, sku, weight, dimensions, status, return_reason, customer_id)
- `delivery_status_histories` (id, delivery_id, status, actor_id, note, lat, lng, created_at)
- `driver_location_pings` (id, driver_id, lat, lng, heading, speed, created_at)
- `notifications` (id, notifiable_type, notifiable_id, channel, payload, sent_at, status)
- `daily_reports` (id, city_id, report_date, delivered_count, returned_count, failed_count, generated_at)

### Entities & Relationships
- User: Central entity, can have multiple roles (via pivot), can be a customer (for orders/packages), or a driver (via Driver model).
- Role: Many-to-many with User.
- Driver: Belongs to User, Vehicle, and City; has many Deliveries and LocationPings.
- Vehicle: Has one Driver.
- Order: Belongs to City and User (as customer); has many Packages.
- Package: Belongs to Delivery, User (as customer), and City.
- Delivery: Belongs to Order, Driver, and City; has many Packages and StatusHistories.
- City: Has many Orders, Drivers, Deliveries, and Packages.
- DriverLocationPing: Belongs to Driver.
- DeliveryStatusHistory: Belongs to Delivery.

### Indexing & performance notes
- Index `deliveries` on `city_id`, `status`, `assigned_driver_id`, `scheduled_at`.
- Index `driver_location_pings` on `driver_id`, `created_at` (partition older data).
- Consider composite index for queries like `(city_id, status, scheduled_at)`.

---

## 6. Workflows & business rules

### 6.1 Delivery creation (dispatcher/system)
- Orders are created (manually or via API).
- Packages are linked to orders (one order can contain many packages).
- A `delivery` record is created with status `pending`/`unassigned`.

### 6.2 Assignment (automatic + manual)
**Business rule:** Prefer automatic minimal-load assignment; allow manual override.
- Automatic dispatcher job runs on event (new delivery) or batch:
  - Filter drivers in delivery’s city who are `active` and `available`.
  - Compute `load_score` — primary factor: `current_load` (number of packages or weight); secondary: distance from pickup.
  - Pick driver with minimal `load_score`.
  - Create assignment; update `driver.current_load` and `delivery.assigned_driver_id`.
  - Emit `DeliveryAssigned` event.
- Dispatchers can manually reassign; reassign triggers notifications and load adjustments.

### 6.3 Driver mobile lifecycle
Driver app interacts via API:
- Accept / decline assignment (if accept, delivery status → `accepted`).
- Update statuses: `enroute_to_pickup`, `picked_up`, `enroute_to_dropoff`, `delivered`, `failed_delivery`.
- Send periodic GPS pings (every X seconds) → create `driver_location_pings`.
- For `delivered`: record proof (photo, signature, OTP), timestamp, and update package statuses.

### 6.4 Undelivered & end-of-day returns
- If package status remains not `delivered` or `failed_delivery` by city EOD (configurable per city), scheduler job:
  - Marks packages/deliveries as `return`.
  - Record `return_reason` and status_history.
  - Include in daily report.
- If driver marks `failed_delivery`, include reason and allow attempt count and auto-schedule reattempts (configurable).

### 6.5 Notifications
- On assignment, status change, and final delivery → notify customer (channels configurable).
- Notifications go to customers (SMS/email/push) and to dispatchers via Ops dashboard.

### 6.6 Reporting
- Daily reports per city (aggregate delivered, returned, failed, avg delivery time).
- Exportable CSV/PDF.
- Ad-hoc filters for date range, driver, city.

---
