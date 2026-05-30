# Order Module

The Order Module handles the full sales workflow: managing banks, job services, products (with price history), customers, invoices, and payments. It lives under `Modules/Orders/` and exposes a REST API under the `/api/order` prefix.

---

## Table of Contents

1. [Directory Structure](#directory-structure)
2. [Database Schema](#database-schema)
3. [Models](#models)
4. [Routes](#routes)
5. [Controllers](#controllers)
6. [Form Requests & Validation](#form-requests--validation)
7. [Services (Managers)](#services-managers)
8. [Filters](#filters)
9. [Policies & Permissions](#policies--permissions)
10. [Email Notifications](#email-notifications)
11. [Frontend Pages](#frontend-pages)
12. [Key Business Rules](#key-business-rules)

---

## Directory Structure

```
Modules/Orders/
├── app/
│   ├── Filters/
│   │   ├── BankFilter.php
│   │   ├── InvoiceFilter.php
│   │   ├── JobServiceFilter.php
│   │   └── ProductFilter.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BankController.php
│   │   │   ├── InvoiceController.php
│   │   │   ├── JobServiceController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ProductController.php
│   │   └── Requests/
│   │       ├── StoreBankRequest.php / UpdateBankRequest.php
│   │       ├── StoreCustomerRequest.php
│   │       ├── StoreInvoiceRequest.php / UpdateInvoiceRequest.php
│   │       ├── StoreJobServiceRequest.php / UpdateJobServiceRequest.php
│   │       ├── StorePaymentRequest.php / UpdatePaymentRequest.php
│   │       └── StoreProductRequest.php / UpdateProductRequest.php
│   ├── Models/
│   │   ├── Bank.php
│   │   ├── Invoice.php
│   │   ├── InvoiceJob.php
│   │   ├── JobService.php
│   │   ├── Payment.php
│   │   ├── PaymentPrice.php
│   │   └── Product.php
│   ├── Policies/
│   │   ├── OrderPolicy.php          (base class)
│   │   ├── BankPolicy.php
│   │   ├── InvoicePolicy.php
│   │   ├── JobServicePolicy.php
│   │   ├── PaymentPolicy.php
│   │   ├── ProductPolicy.php
│   │   └── ProductPricePolicy.php
│   └── Services/
│       ├── BankManager.php
│       ├── InvoiceManager.php
│       ├── JobServiceManager.php
│       ├── PaymentManager.php
│       └── ProductManager.php
├── database/migrations/
└── routes/api.php
```

---

## Database Schema

### `banks`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar(50) | |
| `created_by` | FK → users | nullable on delete |
| `timestamps` | | |

### `invoices`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `invoice_no` | varchar(8) | unique, auto-generated (8-char alphanumeric) |
| `customer_id` | FK → users | **RESTRICT** — delete blocked if invoices exist |
| `discount` | uint64 | default 0 |
| `total` | uint64 | subtotal − discount |
| `note` | text | nullable |
| `created_by` | FK → users | null on delete |
| `timestamps` | | index on `created_at` |

### `payments`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `invoice_id` | FK → invoices | **CASCADE** — deleted with invoice |
| `type_id` | uint8 | 1=Cash, 2=Bank, 3=Other |
| `bank_id` | FK → banks | nullable, **RESTRICT** — delete blocked if payments exist |
| `stage` | uint8 | 1=Advance, 2=Final |
| `amount` | uint64 | |
| `note` | text | nullable |
| `payment_date` | date | |
| `created_by` | FK → users | null on delete |
| `timestamps` | | composite index on `(invoice_id, stage)` |

### `products`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | fulltext indexed |
| `description` | text | nullable, fulltext indexed |
| `created_by` | FK → users | null on delete |
| `timestamps` | | index on `name`, `created_at` |

### `product_prices`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `product_id` | FK → products | cascade delete |
| `per_price` | uint32 | |
| `created_by` | FK → users | null on delete |
| `timestamps` | | index on `updated_at` |

### `job_services`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | varchar | fulltext indexed |
| `description` | text | nullable, fulltext indexed |
| `created_by` | FK → users | null on delete |
| `timestamps` | | index on `name`, `created_at` |

### `invoice_jobs`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `invoice_id` | FK → invoices | **CASCADE** — deleted with invoice |
| `service_id` | FK → job_services | **RESTRICT** — delete blocked if jobs exist |
| `product_id` | FK → products | **RESTRICT** — delete blocked if jobs exist |
| `quantity` | uint32 | |
| `unit_price` | uint64 | |
| `total` | uint64 | quantity × unit_price |
| `delivery_date` | date | |
| `created_by` | FK → users | null on delete |
| `timestamps` | | |

---

## Models

### `Payment`

```php
Constants:
  TYPE_CASH   = 1
  TYPE_BANK   = 2
  TYPE_OTHER  = 3
  STAGE_ADVANCE = 1
  STAGE_FINAL   = 2

Fillable: invoice_id, type_id, bank_id, stage, amount, note, payment_date
Casts:    payment_date → date, amount → integer

Relationships:
  invoice()    BelongsTo Invoice
  bank()       BelongsTo Bank (nullable)
  createdBy()  BelongsTo User

Static helpers:
  typeOptions()   → array of {id, name} for types
  stageOptions()  → array of {id, name} for stages
```

### `Invoice`

```php
Fillable: customer_id, discount, total, note
Casts:    discount → integer, total → integer

Auto-generated on creating:
  invoice_no = 8-character random alphanumeric (unique)

Relationships:
  customer()   BelongsTo User
  jobs()       HasMany InvoiceJob
  payments()   HasMany Payment
  createdBy()  BelongsTo User
```

### `InvoiceJob`

```php
Fillable: invoice_id, service_id, product_id, quantity, unit_price, total,
          delivery_date, created_by
Casts:    quantity → integer, unit_price → integer, total → integer,
          delivery_date → date

Relationships:
  invoice()    BelongsTo Invoice
  service()    BelongsTo JobService (FK: service_id)
  product()    BelongsTo Product
  createdBy()  BelongsTo User
```

### `Product`

```php
Fillable: name, description, created_by

Relationships:
  prices()     HasMany PaymentPrice
  createdBy()  BelongsTo User
```

### `PaymentPrice`

```php
Table:    product_prices
Fillable: product_id, per_price, created_by
Casts:    per_price → integer

Relationships:
  product()    BelongsTo Product
  createdBy()  BelongsTo User
```

### `JobService`

```php
Table:    job_services
Fillable: name, description, created_by

Relationships:
  invoiceJobs()  HasMany InvoiceJob (FK: service_id)
  createdBy()    BelongsTo User
```

### `Bank`

```php
Fillable: name, created_by

Relationships:
  createdBy()  BelongsTo User
```

---

## Routes

All routes: `auth:sanctum` + `role:admin|user` middleware, prefix `/api/order`.

### Banks

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/banks` | `index` | `viewAny` |
| POST | `/banks` | `store` | `create` |
| GET | `/banks/{bank}` | `show` | `view` |
| PUT | `/banks/{bank}` | `update` | `update` |
| DELETE | `/banks/{bank}` | `destroy` | `delete` |

### Job Services

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/services` | `index` | `viewAny` |
| POST | `/services` | `store` | `create` |
| GET | `/services/{job_service}` | `show` | `view` |
| PUT | `/services/{job_service}` | `update` | `update` |
| DELETE | `/services/{job_service}` | `destroy` | `delete` |

### Products

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/products` | `index` | `viewAny` |
| POST | `/products` | `store` | `create` |
| GET | `/products/{product}` | `show` | `view` |
| PUT | `/products/{product}` | `update` | `update` |
| DELETE | `/products/{product}` | `destroy` | `delete` |
| GET | `/products/{product}/prices` | `prices` | `viewAny PaymentPrice` |
| DELETE | `/products/{product}/prices/{price}` | `destroyPrice` | `delete` |

### Customers

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/customers` | `customers` | `viewAny Invoice` |
| POST | `/customers` | `registerCustomer` | `create Invoice` |

### Invoices

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/invoices` | `index` | `viewAny` |
| POST | `/invoices` | `store` | `create` |
| GET | `/invoices/{invoice}` | `show` | `view` |
| PUT | `/invoices/{invoice}` | `update` | `update` |
| DELETE | `/invoices/{invoice}` | `destroy` | `delete` |
| POST | `/invoices/{invoice}/send` | `send` | `view` |

### Payments

| Method | URI | Action | Policy |
|---|---|---|---|
| GET | `/payments/meta` | `meta` | — |
| POST | `/payments` | `store` | `create` |
| GET | `/payments/{payment}` | `show` | `view` |
| PUT | `/payments/{payment}` | `update` | `update` |
| DELETE | `/payments/{payment}` | `destroy` | `delete` |
| POST | `/payments/{payment}/send-receipt` | `sendReceipt` | `view` |

---

## Controllers

### `InvoiceController`

**`customers(Request)`**
Returns a searchable list of customers (users with `customer` role). Requires minimum 2 characters. Returns up to 20 matches with company profile. Used for the customer typeahead in the invoice form.

**`registerCustomer(StoreCustomerRequest)`**
Creates a new user account with the `customer` role. Auto-generates a 12-character random password, sets a 24-hour email verification token, creates a company profile, and sends a `CustomerCredentialsNotification` email with login details.

**`index(Request)`**
Returns a paginated list of invoices. Each row includes a `paid_amount` subquery, customer name, and the caller's `can` abilities.

**`store(StoreInvoiceRequest)`**
Creates an invoice with all its jobs and an initial payment in a single DB transaction. Audits the creation.

**`show(Invoice)`**
Returns the full invoice with customer, company profile, jobs, payments (with bank and created-by), and the caller's `can` abilities.

**`update(UpdateInvoiceRequest, Invoice)`**
Updates the invoice, syncs jobs (deletes removed, updates existing, inserts new), and updates payments. Runs in a DB transaction. Audits the change.

**`destroy(Invoice)`**
Deletes the invoice (cascades to jobs and payments). Audits the deletion.

**`send(Invoice)`**
Guards against unverified customer email (returns 422 if unverified). Sends an `InvoiceNotification` email to the customer.

---

### `PaymentController`

**`meta()`**
Returns payment type options, stage options, and the `TYPE_BANK` and `STAGE_FINAL` constants. Used by the frontend to build dropdowns without hardcoding values.

**`store(StorePaymentRequest)`**
Creates a payment and audits the creation.

**`show(Payment)`**
Returns the payment with bank and created-by relationships.

**`update(UpdatePaymentRequest, Payment)`**
Updates the payment. Nullifies `bank_id` if type is not `TYPE_BANK`. Audits the change.

**`sendReceipt(Payment)`**
Guards against unverified or missing customer email (returns 422). Sends a `PaymentReceiptNotification` email to the customer with the payment details.

**`destroy(Payment)`**
Deletes the payment and audits the deletion.

---

### `ProductController`

**`index(Request)`**
Returns paginated products. Each row includes a `current_price` subquery (most recently updated price). Supports fulltext search and sorting by name, price, or date.

**`store(StoreProductRequest)`**
Creates a product and its initial price record.

**`show(Product)`**
Returns the product with all prices ordered by `updated_at DESC` and the created-by user.

**`update(UpdateProductRequest, Product)`**
Updates the product. If `per_price` is provided, creates a new price record (preserving history).

**`destroy(Product)`**
Deletes the product and all its prices. Catches `QueryException` code `23000` and returns 422 if the product is referenced by existing invoice jobs.

**`prices(Product)`**
Returns the full price history for a product, ordered newest first.

**`destroyPrice(Product, PaymentPrice)`**
Deletes a price record. Returns 422 if it is the only remaining price for the product.

---

### `JobServiceController`

Standard CRUD: `index` (paginated, fulltext search), `store`, `show`, `update`, `destroy`. All operations are audited.

**`destroy`** catches `QueryException` code `23000` (FK constraint violation) and returns 422 with a user-friendly message if the service is referenced by existing invoice jobs.

### `BankController`

Standard CRUD: `index` (paginated, name search), `store`, `show`, `update`, `destroy`. All operations are audited.

**`destroy`** catches `QueryException` code `23000` and returns 422 if the bank is referenced by existing payments.

---

## Form Requests & Validation

### Invoice Validation

**`StoreInvoiceRequest`**

| Field | Rules |
|---|---|
| `customer_id` | required, exists:users |
| `discount` | nullable, integer, min:0 |
| `note` | nullable, string, max:5000 |
| `jobs` | required array, min:1 item |
| `jobs.*.service_id` | required, exists:job_services |
| `jobs.*.product_id` | required, exists:products |
| `jobs.*.quantity` | required, integer, min:1 |
| `jobs.*.unit_price` | required, integer, min:0 |
| `jobs.*.delivery_date` | required, date |
| `payment.type_id` | required, in [1,2,3] |
| `payment.bank_id` | required if type_id=2 |
| `payment.stage` | required, in [1,2] |
| `payment.amount` | required, integer, min:0 |
| `payment.payment_date` | required, date |

Custom rules:
- Amount must not exceed invoice total
- If stage=Final: amount must equal remaining balance
- If stage=Advance: amount must be less than total

**`UpdateInvoiceRequest`**

Same field rules as Store, with the following additions:
- `payments` is nullable array (allows editing existing payments)
- Each payment in the array must belong to the invoice being updated
- Total of all payments must not exceed invoice total
- At most one final payment; if final exists, its amount must equal total

---

### Payment Validation

**`StorePaymentRequest`**

| Field | Rules |
|---|---|
| `invoice_id` | required, exists:invoices |
| `type_id` | required, in [1,2,3] |
| `bank_id` | required if type_id=2 |
| `stage` | required, in [1,2] |
| `amount` | required, integer, min:0 |
| `payment_date` | required, date |
| `note` | nullable, string, max:5000 |

Custom rules:
- No payments allowed if a final payment already exists on the invoice
- Amount must not exceed remaining balance
- Final payment amount must equal remaining balance

---

### Product Validation

| Field | Store | Update |
|---|---|---|
| `name` | required, max:255, unique | required, max:255, unique (ignore self) |
| `description` | nullable string | nullable string |
| `per_price` | required, integer, min:0 | nullable, integer, min:0 |

### Bank & Service Validation

| Field | Rules |
|---|---|
| `name` | required, max:255 (Bank: max:50), unique (ignore self on update) |
| `description` | (Services only) nullable string |

### Customer Registration

| Field | Rules |
|---|---|
| `name` | required, max:255 |
| `email` | required, email, unique:users |
| `company_profile.name` | required, max:255 |
| `company_profile.role` | required, max:255 |
| `company_profile.phone` | required, max:50 |
| `company_profile.address` | required, max:1000 |

---

## Services (Managers)

### `InvoiceManager`

**`registerCustomer(array $data)`**
Wraps in `DB::transaction`. Creates the user, assigns `customer` role, creates company profile, generates 12-char random password (stored hashed), sets `email_verification_token` with 24h expiry, sends `CustomerCredentialsNotification`.

**`list(Request $request, int $perPage)`**
Queries invoices with a `paid_amount` subquery (`SUM` of payments per invoice), eager-loads customer and created-by, passes through `InvoiceFilter` for search/sort, and paginates.

**`create(array $data)`**
`DB::transaction`:
1. Calculates total = `sum(job.quantity × job.unit_price) - discount`
2. Inserts invoice
3. Batch-inserts `invoice_jobs` via `syncJobs()`
4. Creates initial payment record
5. Reloads with all relationships

**`update(Invoice $invoice, array $data)`**
`DB::transaction`:
1. Recalculates total from updated jobs
2. Updates invoice fields
3. Deletes jobs not present in submitted array
4. Updates existing jobs, inserts new ones
5. Removes payments not in submitted array
6. Updates existing payments, inserts new ones
7. Reloads with all relationships

**`show(Invoice $invoice)`**
Eager-loads: `customer`, `customer.companyProfile`, `jobs.service`, `jobs.product`, `payments.bank`, `payments.createdBy`, `createdBy`.

**`sendToCustomer(Invoice $invoice)`**
Loads all relationships needed by the email template, then dispatches `InvoiceNotification` to the customer.

**`delete(Invoice $invoice)`**
Deletes the invoice (cascade handles jobs and payments).

---

### `PaymentManager`

**`create(array $data)`**
Creates payment, sets `created_by` = current auth user, eager-loads bank and createdBy.

**`show(Payment $payment)`**
Loads `bank:id,name` and `createdBy:id,name`.

**`update(Payment $payment, array $data)`**
Updates the payment. Forces `bank_id = null` if `type_id !== TYPE_BANK`. Returns payment with relationships reloaded.

**`delete(Payment $payment)`**
Deletes the payment.

---

### `ProductManager`

**`list(Request $request, int $perPage)`**
Selects products with a `current_price` subquery (most recent `per_price` by `updated_at`) and an `invoice_jobs_count` subquery for FK-disable logic. Passes through `ProductFilter` for fulltext search and sorting.

**`create(array $data)`**
Creates product and initial `PaymentPrice` record in one transaction.

**`update(Product $product, array $data)`**
Updates product fields. If `per_price` is provided, inserts a new `PaymentPrice` row (preserving history).

**`show(Product $product)`**
Loads `createdBy` and `prices` ordered by `updated_at DESC`.

**`delete(Product $product)`**
Deletes product and cascade removes all price records.

---

### `JobServiceManager` / `BankManager`

Both follow the same pattern: `list()` → paginate via filter (includes `withCount` for FK-disable: `invoice_jobs_count` for services, `payments_count` for banks), `create()` → insert + audit, `update()` → save + audit, `delete()` → delete + audit, `show()` → load createdBy.

---

## Filters

All filters extend a base `Filter` class with a static `for(Builder $query)` factory method and chainable methods.

### `InvoiceFilter`

| Method | Behaviour |
|---|---|
| `search(string)` | LIKE on `invoice_no` OR JOIN customers LIKE on `name` |
| `sort(string, string)` | Sortable: `id`, `invoice_no`, `total`, `created_at`. Default: `created_at DESC` |

### `ProductFilter`

| Method | Behaviour |
|---|---|
| `search(string)` | `MATCH(name, description) AGAINST (? IN BOOLEAN MODE)` |
| `sort(string, string)` | Sortable: `id`, `name`, `created_at`, `per_price` (sorts by `current_price` alias). Default: `created_at DESC` |

### `JobServiceFilter`

| Method | Behaviour |
|---|---|
| `search(string)` | `MATCH(name, description) AGAINST (? IN BOOLEAN MODE)` |
| `sort(string, string)` | Sortable: `id`, `name`, `created_at`. Default: `created_at DESC` |

### `BankFilter`

| Method | Behaviour |
|---|---|
| `search(string)` | LIKE on `name` |
| `sort(string, string)` | Sortable: `id`, `name`, `created_at`, `updated_at`. Default: `created_at DESC` |

---

## Policies & Permissions

### Permission Hierarchy

Defined in `OrderPolicy` (base class):

| Ability | Required permissions (any) |
|---|---|
| `viewAny` (list) | `order_list`, `order_view`, `order_create`, `order_update`, `order_delete`, `super` |
| `view` | `order_view`, `order_create`, `order_update`, `order_delete`, `super` |
| `create` | `order_create`, `order_update`, `order_delete`, `super` |
| `update` | `order_update`, `order_delete`, `super` |
| `delete` | `order_delete`, `super` |

The `userPolicyCheck()` helper verifies the user has role `user` AND any of the listed permissions.

### Policy Classes

| Policy | Notes |
|---|---|
| `InvoicePolicy` | Both admin and user roles allowed for all abilities |
| `PaymentPolicy` | `view` mapped to CREATE permissions; `delete` restricted to admin only |
| `ProductPolicy` | create/update/delete require admin role |
| `JobServicePolicy` | create/update/delete require admin role |
| `BankPolicy` | create/update/delete require admin role |
| `ProductPricePolicy` | Follows ProductPolicy restrictions |

### `can` Array in API Responses

Controllers return a `can` array with each resource response so the frontend can conditionally render action buttons:

```json
{
  "id": 1,
  "invoice_no": "AB12CD34",
  "can": {
    "view": true,
    "edit": true,
    "delete": false,
    "add_payment": true
  }
}
```

---

## Email Notifications

### `InvoiceNotification`

- **Trigger:** `POST /invoices/{invoice}/send`
- **Guard:** Customer must have verified email (`email_verified_at` not null)
- **Subject:** `Invoice {invoice_no} from {COMPANY_NAME}`
- **Template:** `resources/views/emails/invoice.blade.php`
- **Content:** Company header, bill-to details, invoice summary, itemised jobs table, payment summary

### `PaymentReceiptNotification`

- **Trigger:** `POST /payments/{payment}/send-receipt`
- **Guard:** Customer must have verified email
- **Subject:** `Payment Receipt — {invoice_no} from {COMPANY_NAME}`
- **Template:** `resources/views/emails/payment-receipt.blade.php`
- **Content:** Company header, bill-to details, invoice reference (invoice no + amount paid), payment details (type, bank, stage, amount, date, created by), optional note

Both templates use the shared email partials in `resources/views/emails/partials/` and read company info from `VITE_COMPANY_*` environment variables via `env()`.

---

## Frontend Pages

All pages are in `resources/js/pages/order/` and registered in the Vue Router.

### Banks — `/order/banks`

| Route | Component | Description |
|---|---|---|
| `/order/banks` | `AdminBanksPage.vue` | Paginated list with name search and sort |
| `/order/banks/create` | `AdminCreateBankPage.vue` | Create form |
| `/order/banks/:id` | `AdminViewBankPage.vue` | Detail view |
| `/order/banks/:id/edit` | `AdminEditBankPage.vue` | Edit form |

### Job Services — `/order/services`

| Route | Component | Description |
|---|---|---|
| `/order/services` | `AdminServicesPage.vue` | Paginated list with fulltext search |
| `/order/services/create` | `AdminCreateServicePage.vue` | Create form |
| `/order/services/:id` | `AdminViewServicePage.vue` | Detail view |
| `/order/services/:id/edit` | `AdminEditServicePage.vue` | Edit form |

### Products — `/order/products`

| Route | Component | Description |
|---|---|---|
| `/order/products` | `AdminProductsPage.vue` | List with fulltext search, current price column |
| `/order/products/create` | `AdminCreateProductPage.vue` | Create form with initial price |
| `/order/products/:id` | `AdminViewProductPage.vue` | Detail with price history |
| `/order/products/:id/edit` | `AdminEditProductPage.vue` | Edit form; new price field creates a price record |
| `/order/products/:id/prices` | `AdminProductPricesPage.vue` | Full price history; delete individual prices (blocked if last) |

### Invoices — `/order/invoices`

| Route | Component | Description |
|---|---|---|
| `/order/invoices` | `AdminInvoicesPage.vue` | List with fuzzy search (invoice no / customer), balance status badges |
| `/order/invoices/create` | `AdminCreateInvoicePage.vue` | Complex form: customer typeahead, dynamic jobs array, initial payment |
| `/order/invoices/:id` | `AdminViewInvoicePage.vue` | Full detail: jobs table, payments table with send/print receipt per row, send invoice button, print invoice button |
| `/order/invoices/:id/edit` | `AdminEditInvoicePage.vue` | Edit jobs and payments in place |

#### Invoice View Page — Notable Features

- **Send Invoice** button: disabled with tooltip when customer email is unverified
- **Payments table**: compact layout with per-row actions:
  - Envelope icon → `sendReceipt(pmt)`: POST to send receipt email (disabled if email unverified)
  - Printer icon → `printReceipt(pmt)`: opens browser print dialog with receipt layout
  - Chat bubble icon → opens payment note modal (only shown if note exists)
- **Print Invoice** button: opens browser print dialog with full invoice layout
- **Print layouts**: two hidden `<div>` elements (`#invoice-print`, `#receipt-print`) with `@media print` CSS that shows only the relevant one based on `document.body.dataset.print`

---

## Key Business Rules

### Invoice Lifecycle

1. Invoice is created with at least one job and an initial payment
2. `invoice_no` is auto-generated as a unique 8-character alphanumeric string
3. `total = sum(job.quantity × job.unit_price) − discount`
4. Invoices can be updated (jobs and payments edited) until a final payment is recorded
5. Customers can be registered inline from the invoice creation form

### Payment Rules

- **Types:** Cash (1), Bank (2), Other (3). Bank type requires a `bank_id`.
- **Stages:** Advance/Deposit (1), Final (2).
- **Advance payments** must be less than the invoice total.
- **Final payment** must equal the remaining balance (total − sum of prior payments).
- **No further payments** can be added after a final payment exists.
- Multiple advance payments are allowed before the final payment.

### Product Pricing

- Products always have at least one price record.
- Each price update creates a new `product_prices` row, preserving history.
- The "current price" is always the most recently updated price row.
- Deleting a price is blocked if it is the only remaining price for that product.

### Customer Registration (from Invoice Form)

- Staff can register new customers directly from the invoice create page without leaving the workflow.
- A random 12-character password is generated and emailed to the customer.
- A 24-hour email verification token is set; receipt/invoice emails are blocked until verified.

### FK Deletion Protection

Records with RESTRICT foreign keys cannot be deleted when referenced. The system enforces this at two levels:

**Backend** — Controllers catch `QueryException` with code `23000` and return HTTP 422 with a user-friendly message (belt-and-suspenders if the UI check is bypassed).

**Frontend** — List pages include a `withCount` subquery so each row knows if it is referenced. Delete icons are disabled (greyed out, `cursor-not-allowed`) with a hover tooltip when the count is non-zero.

| Entity | Blocked by | Count field |
|---|---|---|
| User (customer) | `invoices.customer_id` | `invoices_count` (raw subquery) |
| Bank | `payments.bank_id` | `payments_count` |
| Product | `invoice_jobs.product_id` | `invoice_jobs_count` |
| Job Service | `invoice_jobs.service_id` | `invoice_jobs_count` |

Deleting an **invoice** cascades automatically to its `invoice_jobs` and `payments` rows — no restriction applies.

---

### Audit Trail

All CRUD operations on Banks, Job Services, Products, Invoices, and Payments are recorded via the `AuditableCrud` concern, capturing before/after snapshots with nested relationship data (bank name, created-by name, etc.).

### Print & Email Receipts

- **Print:** The invoice view page contains two hidden print layouts (`#invoice-print`, `#receipt-print`). `document.body.dataset.print` selects which one to show. `@media print` CSS hides all other page content via `display: none`, preventing blank pages.
- **Email:** Both invoice and receipt emails read company details (`name`, `address`, `phone`, `email`, `Facebook`) from `VITE_COMPANY_*` environment variables and use shared Blade partials for the header and footer.
