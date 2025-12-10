# Automated Monthly Report Generation Feature

## Overview
Implemented automated monthly report generation system that creates comprehensive sales reports for each month, with automatic generation on first admin access and manual generation capability.

## Implementation Details

### 1. Database Schema (`db_init.sql`)
Created `monthly_reports` table with the following structure:
```sql
CREATE TABLE IF NOT EXISTS `monthly_reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `year` INT NOT NULL,
  `month` INT NOT NULL,
  `total_orders` INT DEFAULT 0,
  `total_revenue` INT DEFAULT 0,
  `total_items` INT DEFAULT 0,
  `avg_order_value` INT DEFAULT 0,
  `top_product` VARCHAR(255),
  `top_product_qty` INT DEFAULT 0,
  `report_data` LONGTEXT,
  `generated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `year_month` (`year`, `month`)
);
```

**Key Features:**
- Stores aggregated monthly statistics
- Tracks top-selling product for the month
- Saves detailed JSON report data for future analysis
- UNIQUE constraint prevents duplicate reports for same month/year
- Auto-timestamp for report generation time

### 2. API Endpoint (`api/reports.php`)
Created RESTful API for monthly report operations with the following actions:

#### `?action=generate` (POST)
Generates a new monthly report or updates existing one.
- Queries all orders for specified month/year
- Calculates: total orders, revenue, items sold, average order value
- Identifies top-selling product
- Stores detailed report in JSON format
- Stores/updates in `monthly_reports` table

**Parameters:**
- `month` (POST) - Month number (1-12)
- `year` (POST) - Year (YYYY)

**Response:**
```json
{
  "ok": true,
  "msg": "Laporan bulanan berhasil dibuat",
  "report": {
    "month": 12,
    "year": 2025,
    "total_orders": 45,
    "total_revenue": 2250000,
    "total_items": 156,
    "avg_order_value": 50000,
    "top_product": "Beras 5kg",
    "top_product_qty": 32,
    "product_sales": { ... },
    "generated_at": "2025-12-01 15:30:45"
  }
}
```

#### `?action=list` (GET)
Retrieves all monthly reports, sorted by year/month descending.

**Response:**
```json
{
  "ok": true,
  "reports": [
    {
      "id": 1,
      "year": 2025,
      "month": 12,
      "total_orders": 45,
      "total_revenue": 2250000,
      ...
    }
  ]
}
```

#### `?action=get` (GET)
Retrieves specific monthly report with detailed JSON data.

**Parameters:**
- `year` (GET) - Year
- `month` (GET) - Month

**Response:**
```json
{
  "ok": true,
  "report": {
    "id": 1,
    "year": 2025,
    "month": 12,
    "report_data": {
      "product_sales": {
        "Beras 5kg": 32,
        "Gula 1kg": 28,
        ...
      },
      ...
    }
  }
}
```

#### `?action=export` (GET)
Exports monthly report as CSV file.

**Parameters:**
- `year` (GET) - Year
- `month` (GET) - Month

**Output:** CSV file with report summary

#### `?action=auto_generate` (POST)
Internal action for scheduled/cron tasks to generate previous month's report.

### 3. Admin Dashboard Integration (`admin.php`)

#### New Tab: "📈 Laporan Bulanan"
- Added 4th tab to admin navigation
- Displays comprehensive monthly reports interface

#### Table Features
Displays all monthly reports with columns:
- Bulan (Month and Year)
- Total Pesanan (Order Count)
- Total Penjualan (Revenue)
- Total Item (Items Sold)
- Rata-rata Order (Average Order Value)
- Produk Terlaris (Top Product)
- Tanggal Laporan (Generation Date/Time)
- Aksi (Actions: View, Download)

#### Action Buttons
1. **"Buat Laporan Bulan Ini"** - Manual button to generate current month report
2. **"Lihat"** - View detailed report with product breakdown
3. **"Unduh"** - Download report as CSV

### 4. Auto-Generation Mechanism

**Location:** `checkAndGenerateMonthlyReport()` function in admin.php

**How It Works:**
1. Called automatically on admin page load (`init()` function)
2. Checks if report exists for current month in database
3. If not found, automatically generates report
4. Uses silent error handling (won't break UI if fails)

**Benefits:**
- Reports are always up-to-date
- No manual intervention needed
- Prevents duplicate generation (UNIQUE constraint)
- Runs on-demand when admin accesses dashboard

**Example Trigger:**
```javascript
// On page load, auto-check for current month report
fetch(`api/reports.php?action=get&year=${year}&month=${month}`)
  .then(r => r.json())
  .then(data => {
    if (!data.ok) {
      // Report missing, auto-generate
      fetch('api/reports.php?action=generate', {
        method: 'POST',
        body: formData
      });
    }
  });
```

### 5. JavaScript Functions

#### `loadMonthlyReports()`
Fetches all monthly reports from API and renders table.

#### `renderMonthlyReports(reports)`
Populates monthly reports table with data and action buttons.

#### `generateMonthlyReport()`
- Prompts user to confirm
- Generates report for current month
- Refreshes report table on success
- Shows success/error messages

#### `viewMonthlyReport(year, month)`
- Fetches detailed report
- Displays in formatted alert with:
  - Total orders, revenue, items
  - Average order value
  - Top product information
  - Per-product sales breakdown

#### `exportMonthlyReport(year, month)`
- Triggers CSV download via API

### 6. Data Flow

```
Admin Dashboard Load
    ↓
init() function
    ↓
checkAndGenerateMonthlyReport()
    ↓
Check if current month report exists
    ├─ Yes: Continue
    └─ No: Fetch api/reports.php?action=generate
         ↓
         Query orders for month
         Calculate aggregates
         Find top product
         Store in monthly_reports table
         ↓
         loadMonthlyReports() refreshes table
```

## Usage Instructions

### Automatic Generation
- Simply log in to admin dashboard
- Current month report auto-generates if missing
- No user action required

### Manual Generation
1. Click "📈 Laporan Bulanan" tab
2. Click "📄 Buat Laporan Bulan Ini" button
3. Confirm when prompted
4. Report appears in table below

### View Details
1. Click "Lihat" button on any report row
2. Popup displays:
   - Summary statistics
   - Per-product sales breakdown
   - Top product information

### Export Report
1. Click "Unduh" button on any report row
2. CSV file downloads with month_year naming
3. Open in Excel/Sheets for further analysis

## Technical Stack

- **Backend:** PHP with PDO MySQL
- **Frontend:** Vanilla JavaScript
- **Database:** MySQL with new `monthly_reports` table
- **API:** RESTful endpoints in `api/reports.php`
- **Data Format:** JSON for detailed reports, CSV for export

## Files Modified/Created

1. ✅ `api/reports.php` - NEW: Complete report API
2. ✅ `admin.php` - Added monthly reports tab and JS functions
3. ✅ `db_init.sql` - Added monthly_reports table schema
4. ✅ `config.php` - No changes needed

## Error Handling

- **Missing Report:** Returns JSON error, triggers auto-generation
- **Database Errors:** Returns JSON error with details
- **Duplicate Prevention:** UNIQUE constraint prevents duplicate month/year entries
- **Invalid Month/Year:** Validated and uses current date defaults

## Future Enhancements

Potential improvements for consideration:
1. **Scheduled Cron Job** - Generate previous month report automatically on 1st of month
2. **Email Reports** - Auto-email monthly reports to admin
3. **PDF Export** - Advanced export to PDF with formatting
4. **Report Archive** - Historical comparison and trend analysis
5. **Dashboard Charts** - Visual graphs of monthly trends
6. **Custom Date Range** - Generate reports for custom periods
7. **Multi-location Support** - Reports per store/branch
8. **Variance Analysis** - Compare month-to-month performance

## Testing Checklist

- [ ] Access admin dashboard → auto-generates current month report
- [ ] Click "Buat Laporan Bulan Ini" → generates/updates report
- [ ] Click "Lihat" → displays detailed product breakdown
- [ ] Click "Unduh" → downloads CSV file
- [ ] Generate same month twice → no duplicates (UNIQUE constraint)
- [ ] Create new orders → report reflects updated totals
- [ ] Change month in URL → retrieves correct report

## Notes

- Reports are based on `orders` table created_at timestamp
- Only paid/completed orders included (can be filtered by status if needed)
- Product sales calculated from order_items table
- All currency in Rupiah (Rp)
- Dates in Indonesian locale (id-ID format)
