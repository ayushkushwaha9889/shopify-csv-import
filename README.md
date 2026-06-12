# Laravel CSV to Shopify Product Import System

## Overview

This project is a Laravel 12 application that allows users to upload CSV files containing product data, process them asynchronously using Laravel Queues, and import products into Shopify using the GraphQL API.

The application provides import tracking, status monitoring, error logging, and a dashboard for viewing import history and product import results.

---

## Features

### CSV Upload

* Upload CSV files containing product information
* File validation for type and size
* Upload status tracking

### Queue-Based Processing

* Asynchronous CSV processing using Laravel Queues
* Background Shopify product imports
* Improved performance and scalability

### Shopify GraphQL Integration

* Create products in Shopify
* Update existing products
* Add imported products to a specific Shopify Collection
* GraphQL error handling

### Dashboard

* View uploaded CSV files
* Track import progress
* View imported products
* Monitor product statuses
* View import logs

### Logging

* Success and failure logging
* Product-level error tracking
* Dashboard log viewer

---

## Technology Stack

* Laravel 12
* PHP 8.2+
* MySQL
* Shopify GraphQL API
* Laravel Queue System
* Bootstrap 5
* League CSV

---

## Installation

### Clone Repository

```bash
git clone <repository-url>
cd shopify-import-system
```

### Install Dependencies

```bash
composer install
```

### Environment Configuration

Copy the environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure database credentials in `.env`.

Configure Shopify credentials:

```env
SHOPIFY_STORE_URL=https://laravel-import-test.myshopify.com
SHOPIFY_ACCESS_TOKEN=YOUR_ACCESS_TOKEN
SHOPIFY_COLLECTION_ID=464337174767
```

### Run Migrations

```bash
php artisan migrate
```

### Create Storage Link

```bash
php artisan storage:link
```

### Start Queue Worker

```bash
php artisan queue:work
```

### Start Application

```bash
php artisan serve
```

---

## Workflow

1. Upload a CSV file.
2. Upload record is created.
3. CSV processing job is dispatched.
4. Product records are stored in database.
5. Shopify import jobs are dispatched.
6. Products are created/updated in Shopify.
7. Products are added to the configured collection.
8. Statuses and logs are updated.
9. Dashboard displays import results.

---

## Database Structure

### uploads

Stores uploaded CSV file information and import progress.

### products

Stores product details, Shopify IDs, statuses, and errors.

### import_logs

Stores import activity and error logs.

---

## Assumptions

* Product SKU is used as the unique identifier.
* Shopify GraphQL API is available and accessible.
* Queue worker is running during imports.
* Products are automatically assigned to the configured Shopify Collection.

---

## Testing

1. Start the queue worker.
2. Upload the provided sample CSV file.
3. Verify products are imported into Shopify.
4. Verify products are added to the configured collection.
5. Verify dashboard statuses and logs.

---



# Project Structure Explanation

## Architecture Overview

The application follows a queue-driven architecture to ensure large CSV imports do not block user requests. CSV processing and Shopify imports are executed asynchronously using Laravel Jobs.

```text
User Uploads CSV
        │
        ▼
UploadController
        │
        ▼
Uploads Table
        │
        ▼
ProcessCsvImport Job
        │
        ▼
Products Table
        │
        ▼
ImportProductToShopify Job
        │
        ▼
Shopify GraphQL API
        │
        ▼
Import Logs + Dashboard
```

---

## Main Components

### Controllers

#### UploadController

Responsible for:

* Displaying the upload page
* Validating uploaded CSV files
* Storing uploaded files
* Creating upload records
* Dispatching CSV processing jobs

#### DashboardController

Responsible for:

* Loading upload history
* Displaying imported products
* Showing import statuses
* Displaying import logs

---

## Models

### Upload

Represents a CSV upload.

Responsibilities:

* Store file information
* Track import progress
* Maintain relationship with products
* Maintain relationship with logs

Relationships:

```php
public function products()
{
    return $this->hasMany(Product::class);
}

public function logs()
{
    return $this->hasMany(ImportLog::class);
}
```

---

### Product

Represents an imported product.

Responsibilities:

* Store CSV product data
* Store Shopify Product ID
* Track import status
* Store error messages

Important Fields:

* sku
* title
* vendor
* product_type
* shopify_product_id
* status

---

### ImportLog

Stores application and import events.

Responsibilities:

* Track successful imports
* Track failures
* Store error messages
* Support dashboard log viewer

---

## Jobs

### ProcessCsvImport

Purpose:

Process uploaded CSV files asynchronously.

Responsibilities:

* Read CSV file
* Validate data
* Create or update product records
* Dispatch Shopify import jobs

Benefits:

* Faster user experience
* Supports large files
* Prevents request timeouts

---

### ImportProductToShopify

Purpose:

Import products into Shopify.

Responsibilities:

* Create Shopify products
* Update existing Shopify products
* Add products to Shopify collection
* Update statuses
* Create import logs

Benefits:

* Separate responsibility
* Better error handling
* Improved scalability

---

## Services

### ShopifyService

Centralized Shopify integration layer.

Responsibilities:

* Execute GraphQL requests
* Create products
* Update products
* Add products to collections

Benefits:

* Reusable code
* Cleaner controllers and jobs
* Easier maintenance

---

## Database Tables

### uploads

Stores uploaded CSV information.

Example:

```text
id
file_name
file_path
status
total_records
processed_records
```

---

### products

Stores product import information.

Example:

```text
id
upload_id
sku
title
vendor
product_type
shopify_product_id
status
error_message
```

---

### import_logs

Stores import activity.

Example:

```text
id
upload_id
product_id
level
message
created_at
```

---

## Queue Flow

```text
CSV Upload
    │
    ▼
ProcessCsvImport
    │
    ▼
Create Product Records
    │
    ▼
ImportProductToShopify
    │
    ▼
Shopify GraphQL API
    │
    ▼
Status Updates + Logs

This architecture ensures the application remains responsive while handling large product imports efficiently.


## Author

Ayush Singh Kushwaha

Laravel Developer
