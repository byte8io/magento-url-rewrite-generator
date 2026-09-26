# URL Rewrite Generator for Magento 2

Rebuild and bulk-import Magento URL rewrites without fighting the admin. Regenerate category and product rewrites from the CLI, or import thousands of custom redirects from a CSV that's validated and processed asynchronously in the background — built for large catalogs and migrations where the native tooling falls short.

CLI generators slot straight into deploy pipelines; the CSV importer runs through Magento's message queue, so a 50k-row redirect map won't tie up a request or time out.

## Why another URL rewrite tool

Magento regenerates rewrites lazily and has no first-class "rebuild these now" or "bulk-load redirects" workflow. This module fills both gaps:

- **Targeted regeneration** — rebuild rewrites for the whole catalog, a single store, or a specific list of entity IDs, instead of an all-or-nothing reindex.
- **Async CSV import** — upload a redirect/rewrite map in admin; rows are validated, queued, and consumed in batches so huge files import without blocking.
- **Per-row validation & failure report** — bad rows are rejected with a reason and surfaced in a "failed operations" grid rather than silently dropped.
- **Unique-URL mode** — optionally pin each product to a single canonical path even when assigned to many categories, avoiding duplicate-content URLs.
- **Category-path product URLs** — honours products whose URLs include their category path.
- **Schedulable** — an optional cron worker regenerates product rewrites unattended, in configurable batches.

## Features

### CLI generation
- Regenerate URL rewrites for **categories** — all, or a specific set of IDs
- Regenerate URL rewrites for **products** — all, per store, or a specific set of IDs
- Backfill / regenerate the product **`url_key`** attribute (optionally derived from any source attribute)
- Respects both category and product **URL suffix** settings
- **Delete** rewrites filtered by entity type and/or store

### CSV import (admin)
- Upload a CSV of URL rewrites / redirects from **Byte8 → Import URL Rewrites**
- Rows validated for required columns and accepted redirect types before processing
- Processed **asynchronously** via a message-queue batch consumer — large files don't block the admin
- **Failed-rows report** grid shows every rejected row with its validation message

### Configuration (admin)

`Stores → Configuration → Byte8 → URL Rewrite Generator → Product Entity Settings`

- **Include Invisible Product** — include products with visibility *Not Visible Individually* in generation (default: **No**)
- **Enable Unique Category/Product URL** — restrict each product to one canonical path even when assigned to multiple categories, with selectable path-resolution options
- **Enable Scheduler** — regenerate product rewrites unattended via cron, with a configurable **cron schedule** (default `03:15`) and **batch size**

## CSV import format

The importer reads a headerless CSV with the following columns, in order:

| # | Column | Required | Notes |
|---|--------|----------|-------|
| 0 | `request_path` | ✅ | The URL a visitor requests |
| 1 | `target_path`  | ✅ | Where it resolves/redirects to |
| 2 | `redirect_type`| ✅ | `0` = no redirect, `301` = permanent, `302` = temporary |
| 3 | `store_code`   |    | Target store view code |
| 4 | `description`  |    | Free-text note |
| 5 | `metadata`     |    | Optional metadata |

Rows missing a required column, or with an unaccepted `redirect_type`, are rejected and listed in the failed-operations report.

## Compatibility

- Open Source (CE) >= 2.4.0
- Commerce On Prem (EE) >= 2.4.0
- Commerce On Cloud (ECE) >= 2.4.0
- PHP 8.2 – 8.5

## Installation

```bash
composer require byte8/module-url-rewrite-generator
bin/magento module:enable Byte8_UrlRewriteGenerator
bin/magento setup:upgrade
bin/magento setup:di:compile
```

In production mode, redeploy static content after compilation:

```bash
bin/magento deploy:mode:set production
```

> Requires [`byte8/module-core`](https://github.com/byte8io/magento-core), installed automatically by Composer.

## Usage

### Generate category URL rewrites

```bash
bin/magento url_rewrite:generate:category [-i <ids>]
```

```bash
# All categories
bin/magento url_rewrite:generate:category

# Only categories 25 and 26
bin/magento url_rewrite:generate:category -i 25,26
```

### Generate product URL rewrites

> Products with visibility *Not Visible Individually* are excluded unless **Include Invisible Product** is enabled in configuration.

```bash
bin/magento url:generate:product [-i <ids>] [-s <store_ids>]
```

```bash
# All products, all stores
bin/magento url:generate:product

# Products 25 and 26 for store IDs 1 and 2
bin/magento url:generate:product -i 25,26 -s 1,2
```

### Generate the product `url_key` attribute

Backfills or rebuilds the `url_key` value — useful before regenerating rewrites, or to derive keys from another attribute.

```bash
bin/magento url:product_url_key:generate [-c <attribute_code>] [-i <product_ids>] [-s <store_ids>]
```

```bash
# Regenerate url_key from product name for all products
bin/magento url:product_url_key:generate

# Derive url_key from a custom attribute for products 25 and 26 on store 1
bin/magento url:product_url_key:generate -c my_attribute -i 25,26 -s 1
```

### Delete URL rewrites

```bash
bin/magento url_rewrite:delete -e <entities> -s <store_ids>
```

```bash
# Delete product rewrites for stores 1 and 2
bin/magento url_rewrite:delete -e product -s 1,2

# Delete product and category rewrites for stores 1, 2 and 3
bin/magento url_rewrite:delete -e product,category -s 1,2,3
```

### Import URL rewrites from CSV

1. Go to **Byte8 → Import URL Rewrites** in the admin.
2. Upload your CSV (see [format](#csv-import-format) above).
3. Valid rows are queued and processed in the background; rejected rows appear in the failed-operations report.

The import is consumed by the `url.rewrite.import.processor` message queue. Ensure a consumer is running:

```bash
bin/magento queue:consumers:start url.rewrite.import.processor
```

On most setups this is managed automatically by Magento's consumer runner / cron.

## Support

Byte8 Ltd — support@byte8.io

## License

Each source file in this package is licensed under OSL 3.0 / AFL 3.0 — see [`LICENSE.txt`](LICENSE.txt) for full details.

[Open Software License (OSL 3.0)](https://opensource.org/licenses/osl-3.0.php)
