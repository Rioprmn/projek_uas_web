<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    $pdo = getPDO(true);

    if ($action === 'generate') {
        // Generate monthly report
        $month = intval($_POST['month'] ?? date('m'));
        $year = intval($_POST['year'] ?? date('Y'));

        // Get all orders for the month
        $stmt = $pdo->prepare("
            SELECT * FROM `orders`
            WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
        ");
        $stmt->execute([$month, $year]);
        $orders = $stmt->fetchAll();

        // Calculate statistics
        $totalOrders = count($orders);
        $totalRevenue = 0;
        $totalItems = 0;
        $productSales = [];

        foreach ($orders as $order) {
            $totalRevenue += $order['final_amount'];

            // Get order items
            $itemStmt = $pdo->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();

            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                if (!isset($productSales[$item['product_name']])) {
                    $productSales[$item['product_name']] = 0;
                }
                $productSales[$item['product_name']] += $item['quantity'];
            }
        }

        arsort($productSales);
        $topProduct = key($productSales) ?? 'N/A';
        $topProductQty = reset($productSales) ?? 0;
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders) : 0;

        // Prepare report data
        $reportData = [
            'month' => $month,
            'year' => $year,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_items' => $totalItems,
            'avg_order_value' => $avgOrderValue,
            'top_product' => $topProduct,
            'top_product_qty' => $topProductQty,
            'product_sales' => $productSales,
            'generated_at' => date('Y-m-d H:i:s')
        ];

        // Save or update report
        $checkStmt = $pdo->prepare("SELECT id FROM `monthly_reports` WHERE `year` = ? AND `month` = ?");
        $checkStmt->execute([$year, $month]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            // Update existing report
            $stmt = $pdo->prepare("
                UPDATE `monthly_reports` 
                SET `total_orders` = ?, `total_revenue` = ?, `total_items` = ?,
                    `avg_order_value` = ?, `top_product` = ?, `top_product_qty` = ?,
                    `report_data` = ?, `generated_at` = NOW()
                WHERE `year` = ? AND `month` = ?
            ");
            $stmt->execute([
                $totalOrders, $totalRevenue, $totalItems, $avgOrderValue,
                $topProduct, $topProductQty, json_encode($reportData),
                $year, $month
            ]);
        } else {
            // Insert new report
            $stmt = $pdo->prepare("
                INSERT INTO `monthly_reports`
                (`year`, `month`, `total_orders`, `total_revenue`, `total_items`,
                 `avg_order_value`, `top_product`, `top_product_qty`, `report_data`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $year, $month, $totalOrders, $totalRevenue, $totalItems,
                $avgOrderValue, $topProduct, $topProductQty, json_encode($reportData)
            ]);
        }

        echo json_encode([
            'ok' => true,
            'msg' => 'Laporan bulanan berhasil dibuat',
            'report' => $reportData
        ]);
        exit;
    }

    if ($action === 'list') {
        // Get all monthly reports
        $stmt = $pdo->query("
            SELECT * FROM `monthly_reports`
            ORDER BY `year` DESC, `month` DESC
        ");
        $reports = $stmt->fetchAll();

        echo json_encode(['ok' => true, 'reports' => $reports]);
        exit;
    }

    if ($action === 'get') {
        // Get specific monthly report
        $month = intval($_GET['month'] ?? date('m'));
        $year = intval($_GET['year'] ?? date('Y'));

        $stmt = $pdo->prepare("
            SELECT * FROM `monthly_reports`
            WHERE `year` = ? AND `month` = ?
        ");
        $stmt->execute([$year, $month]);
        $report = $stmt->fetch();

        if (!$report) {
            echo json_encode(['ok' => false, 'msg' => 'Laporan tidak ditemukan']);
            exit;
        }

        $report['report_data'] = json_decode($report['report_data'], true);

        echo json_encode(['ok' => true, 'report' => $report]);
        exit;
    }

    if ($action === 'auto_generate') {
        // Auto-generate report for previous month (for cron/scheduled task)
        $lastMonth = date('m') == '01' ? 12 : date('m') - 1;
        $lastYear = date('m') == '01' ? date('Y') - 1 : date('Y');

        // Delegate to generate action
        $_POST['month'] = $lastMonth;
        $_POST['year'] = $lastYear;
        $_REQUEST['action'] = 'generate';

        // Re-execute generate logic
        include __FILE__;
        exit;
    }

    if ($action === 'export') {
        // Export report as CSV format
        $month = intval($_GET['month'] ?? date('m'));
        $year = intval($_GET['year'] ?? date('Y'));

        $stmt = $pdo->prepare("
            SELECT * FROM `monthly_reports`
            WHERE `year` = ? AND `month` = ?
        ");
        $stmt->execute([$year, $month]);
        $report = $stmt->fetch();

        if (!$report) {
            echo json_encode(['ok' => false, 'msg' => 'Laporan tidak ditemukan']);
            exit;
        }

        $monthName = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month];

        $csv = "LAPORAN PENJUALAN BULANAN - POS WARUNG\n";
        $csv .= "Bulan: $monthName $year\n";
        $csv .= "Tanggal Laporan: " . date('d-m-Y H:i:s') . "\n\n";
        $csv .= "RINGKASAN\n";
        $csv .= "Total Pesanan," . $report['total_orders'] . "\n";
        $csv .= "Total Penjualan,Rp " . number_format($report['total_revenue']) . "\n";
        $csv .= "Total Item Terjual," . $report['total_items'] . "\n";
        $csv .= "Rata-rata Order,Rp " . number_format($report['avg_order_value']) . "\n";
        $csv .= "Produk Terlaris," . $report['top_product'] . " (" . $report['top_product_qty'] . " unit)\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_penjualan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.csv"');
        echo $csv;
        exit;
    }

    if ($action === 'export_excel') {
        // Export report as Excel format
        $month = intval($_GET['month'] ?? date('m'));
        $year = intval($_GET['year'] ?? date('Y'));

        $stmt = $pdo->prepare("
            SELECT * FROM `monthly_reports`
            WHERE `year` = ? AND `month` = ?
        ");
        $stmt->execute([$year, $month]);
        $report = $stmt->fetch();

        if (!$report) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'msg' => 'Laporan tidak ditemukan']);
            exit;
        }

        $reportData = json_decode($report['report_data'], true);
        $monthName = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month];

        // Generate Excel HTML (compatible with Excel)
        $excel = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $excel .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $excel .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:o="urn:schemas-microsoft-com:office:office"
                   xmlns:x="urn:schemas-microsoft-com:office:excel"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $excel .= '<Styles>
            <Style ss:ID="Header" ss:Name="Header">
                <Font ss:Bold="1" ss:Size="14"/>
                <Interior ss:Color="#667EEA" ss:Pattern="Solid"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="Title" ss:Name="Title">
                <Font ss:Bold="1" ss:Size="12"/>
                <Interior ss:Color="#F0F0F0" ss:Pattern="Solid"/>
            </Style>
            <Style ss:ID="Currency">
                <NumberFormat ss:Format="Currency"/>
            </Style>
            <Style ss:ID="Bold">
                <Font ss:Bold="1"/>
            </Style>
        </Styles>' . "\n";
        $excel .= '<Worksheet ss:Name="Laporan Bulanan">' . "\n";
        $excel .= '<Table>' . "\n";

        // Header
        $excel .= '<Row><Cell ss:StyleID="Header" ss:MergeAcross="1"><Data ss:Type="String">LAPORAN PENJUALAN BULANAN - POS WARUNG</Data></Cell></Row>' . "\n";
        $excel .= '<Row><Cell><Data ss:Type="String">Bulan: ' . $monthName . ' ' . $year . '</Data></Cell></Row>' . "\n";
        $excel .= '<Row><Cell><Data ss:Type="String">Tanggal Laporan: ' . date('d-m-Y H:i:s') . '</Data></Cell></Row>' . "\n";
        $excel .= '<Row></Row>' . "\n";

        // Summary Section
        $excel .= '<Row><Cell ss:StyleID="Title"><Data ss:Type="String">RINGKASAN</Data></Cell></Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Total Pesanan</Data></Cell>
            <Cell><Data ss:Type="Number">' . $report['total_orders'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Total Penjualan</Data></Cell>
            <Cell ss:StyleID="Currency"><Data ss:Type="Number">' . $report['total_revenue'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Total Item Terjual</Data></Cell>
            <Cell><Data ss:Type="Number">' . $report['total_items'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Rata-rata Order</Data></Cell>
            <Cell ss:StyleID="Currency"><Data ss:Type="Number">' . $report['avg_order_value'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Produk Terlaris</Data></Cell>
            <Cell><Data ss:Type="String">' . $report['top_product'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row>
            <Cell><Data ss:Type="String">Qty Produk Terlaris</Data></Cell>
            <Cell><Data ss:Type="Number">' . $report['top_product_qty'] . '</Data></Cell>
        </Row>' . "\n";
        $excel .= '<Row></Row>' . "\n";

        // Product Sales Section
        if ($reportData && isset($reportData['product_sales']) && !empty($reportData['product_sales'])) {
            $excel .= '<Row><Cell ss:StyleID="Title"><Data ss:Type="String">PENJUALAN PER PRODUK</Data></Cell></Row>' . "\n";
            $excel .= '<Row>
                <Cell ss:StyleID="Bold"><Data ss:Type="String">Nama Produk</Data></Cell>
                <Cell ss:StyleID="Bold"><Data ss:Type="String">Qty</Data></Cell>
            </Row>' . "\n";
            
            foreach ($reportData['product_sales'] as $product => $qty) {
                $excel .= '<Row>
                    <Cell><Data ss:Type="String">' . htmlspecialchars($product) . '</Data></Cell>
                    <Cell><Data ss:Type="Number">' . $qty . '</Data></Cell>
                </Row>' . "\n";
            }
        }

        $excel .= '</Table>' . "\n";
        $excel .= '</Worksheet>' . "\n";
        $excel .= '</Workbook>' . "\n";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_penjualan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xls"');
        echo $excel;
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Action tidak ditemukan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
?>
