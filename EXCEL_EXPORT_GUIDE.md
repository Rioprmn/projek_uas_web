# Excel Export Feature - Update

## What Was Added

Added **Excel (.xls) export** functionality to monthly reports alongside the existing CSV export.

## How to Use

In the **"📈 Laporan Bulanan"** tab of the admin dashboard:

1. Find the monthly report you want to export
2. Click one of the download buttons:
   - **CSV** button → Downloads as .csv file (text format)
   - **Excel** button → Downloads as .xls file (Excel format)

## File Details

### CSV Export
- Filename: `laporan_penjualan_YYYY_MM.csv`
- Format: Text-based comma-separated values
- Usable in: Excel, Google Sheets, text editors
- Lightweight, easy to share

### Excel Export (NEW)
- Filename: `laporan_penjualan_YYYY_MM.xls`
- Format: MS Excel XML format
- Usable in: Excel, Google Sheets, LibreOffice
- Rich formatting with colors and styles
- Better for printing and professional reports

## Excel Report Contents

The Excel file includes:

**Header Section:**
- Report title: "LAPORAN PENJUALAN BULANAN - POS WARUNG"
- Month and year
- Report generation date/time

**Summary Section:**
- Total Pesanan (Order Count)
- Total Penjualan (Revenue) - formatted as currency
- Total Item Terjual (Items Sold)
- Rata-rata Order (Average Order Value)
- Produk Terlaris (Top Product)
- Qty Produk Terlaris (Top Product Quantity)

**Product Sales Section:**
- Table with product names and quantities sold
- Includes all products sold that month

## Formatting in Excel

The Excel file includes professional formatting:
- **Header row**: Purple background with white text, centered, bold
- **Section titles**: Light gray background, bold text
- **Currency columns**: Formatted as numbers (can be converted to currency format in Excel)
- **Data rows**: Clean white background with borders

## File Structure

### Updated Files:
1. **`api/reports.php`** - Added `export_excel` action
   - Generates XML-based Excel file
   - Queries monthly_reports table
   - Includes detailed product sales breakdown
   - Creates professional formatted output

2. **`admin.php`** - Updated monthly reports table
   - Changed "Unduh" button to "CSV"
   - Added new "Excel" button (green color)
   - Added `exportMonthlyReportExcel()` function

## Export Format Comparison

| Feature | CSV | Excel |
|---------|-----|-------|
| Format | Text | XML/Binary |
| Filename | .csv | .xls |
| Color/Style | None | Yes |
| Size | Smaller | Slightly larger |
| Excel Compatibility | Good | Native |
| Google Sheets | Good | Good |
| Print Quality | Basic | Professional |
| Easy Editing | Yes | Yes |

## Technical Details

### CSV Export (`action=export`)
- Simple text format
- Comma-separated values
- No special formatting
- ~1KB per report

### Excel Export (`action=export_excel`)
- XML-based MS Excel format (.xls)
- Includes style definitions
- Color-coded sections
- Properly formatted tables
- ~5KB per report

## URL Format

You can also download directly via URL:

```
CSV:  /api/reports.php?action=export&year=2025&month=12
Excel: /api/reports.php?action=export_excel&year=2025&month=12
```

## Browser Behavior

When you click either download button:
1. Browser downloads the file automatically
2. File saved to Downloads folder
3. Filename includes month/year for easy identification
4. Can open immediately in Excel or desired application

## Editing in Excel

After downloading the Excel file, you can:
- ✅ Edit product names and quantities
- ✅ Add formulas and calculations
- ✅ Insert charts and graphs
- ✅ Change colors and formatting
- ✅ Add additional data
- ✅ Save in different formats (xlsx, pdf, etc.)

## Common Use Cases

1. **Monthly Report Distribution**
   - Generate monthly report
   - Download as Excel
   - Share with business partners
   - Print for filing

2. **Data Analysis**
   - Download Excel file
   - Create pivot tables
   - Generate charts
   - Compare with previous months

3. **Accounting Integration**
   - Export to Excel
   - Format for accounting software
   - Convert to PDF for compliance
   - Archive in organized folder

4. **Presentation**
   - Download Excel file
   - Add company logo and branding
   - Create charts and graphs
   - Present to stakeholders

## Error Handling

If Excel export fails:
- Check if monthly report exists
- Try CSV export as alternative
- Verify database connection
- Check browser download settings

## Browser Compatibility

Excel export works on all modern browsers:
- ✅ Chrome
- ✅ Firefox
- ✅ Safari
- ✅ Edge
- ✅ Opera

## Notes

- Both export formats pull from same database report
- If report data is updated, download fresh copy
- Excel files can be converted to newer formats (xlsx, etc.)
- No sensitive customer data in exports (only sales summary)

## Testing

To test Excel export:
1. Login as admin
2. Generate a monthly report
3. Click "Excel" button
4. File downloads as .xls
5. Open in Excel or Sheets
6. Verify all data is present and formatted

---
**Feature Added:** Excel Export for Monthly Reports
**Date:** January 2025
**Status:** Ready to Use
