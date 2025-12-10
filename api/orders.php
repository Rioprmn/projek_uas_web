<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    $pdo = getPDO(true);

    if ($action === 'create') {
        // Create new order
        $customerName = $_POST['customer_name'] ?? '';
        $customerPhone = $_POST['customer_phone'] ?? '';
        $customerAddress = $_POST['customer_address'] ?? '';
        $items = json_decode($_POST['items'] ?? '[]', true);
        $discount = intval($_POST['discount'] ?? 0);

        if (empty($customerName) || empty($items)) {
            echo json_encode(['ok' => false, 'msg' => 'Data tidak lengkap']);
            exit;
        }

        // Calculate totals
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['qty'];
        }
        $finalAmount = $totalAmount - $discount;

        // Generate order number
        $orderNumber = 'ORD' . date('YmdHis') . mt_rand(1000, 9999);

        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO `orders` 
            (order_number, customer_name, customer_phone, customer_address, total_amount, discount, final_amount, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$orderNumber, $customerName, $customerPhone, $customerAddress, $totalAmount, $discount, $finalAmount]);
        $orderId = $pdo->lastInsertId();

        // Insert order items
        $itemStmt = $pdo->prepare("
            INSERT INTO `order_items` 
            (order_id, product_id, product_name, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['qty'];
            $itemStmt->execute([
                $orderId,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['qty'],
                $subtotal
            ]);
        }

        echo json_encode([
            'ok' => true,
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'total' => $finalAmount,
            'msg' => 'Pesanan berhasil dibuat'
        ]);
        exit;
    }

    if ($action === 'list') {
        // Get all orders
        $stmt = $pdo->query("
            SELECT * FROM `orders` ORDER BY `created_at` DESC
        ");
        $orders = $stmt->fetchAll();

        // Get order items for each order
        foreach ($orders as &$order) {
            $itemStmt = $pdo->prepare("
                SELECT * FROM `order_items` WHERE `order_id` = ?
            ");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }

        echo json_encode(['ok' => true, 'orders' => $orders]);
        exit;
    }

    if ($action === 'get') {
        $orderId = intval($_GET['id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['ok' => false, 'msg' => 'Order ID not provided']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM `orders` WHERE `id` = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            echo json_encode(['ok' => false, 'msg' => 'Order not found']);
            exit;
        }

        $itemStmt = $pdo->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
        $itemStmt->execute([$orderId]);
        $order['items'] = $itemStmt->fetchAll();

        echo json_encode(['ok' => true, 'order' => $order]);
        exit;
    }

    if ($action === 'update_status') {
        $orderId = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';

        if (!$orderId) {
            echo json_encode(['ok' => false, 'msg' => 'Order ID required']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE `orders` SET `status` = ? WHERE `id` = ?");
        $stmt->execute([$status, $orderId]);

        echo json_encode(['ok' => true, 'msg' => 'Status updated']);
        exit;
    }

    if ($action === 'stats') {
        // Get sales statistics with date range filtering
        $period = $_GET['period'] ?? 'daily'; // daily, weekly, monthly
        $dateFilter = '';

        if ($period === 'daily') {
            $dateFilter = "DATE(created_at) = CURDATE()";
        } elseif ($period === 'weekly') {
            $dateFilter = "WEEK(created_at) = WEEK(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        } elseif ($period === 'monthly') {
            $dateFilter = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        } elseif ($period === 'all') {
            $dateFilter = "1=1";
        }

        // Get summary stats
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(final_amount) as total_revenue,
                AVG(final_amount) as avg_order_value
            FROM `orders`
            WHERE $dateFilter AND status = 'pending'
        ");
        $summary = $stmt->fetch();

        // Get daily breakdown
        $stmt = $pdo->query("
            SELECT 
                DATE(created_at) as order_date,
                COUNT(*) as daily_orders,
                SUM(final_amount) as daily_revenue
            FROM `orders`
            WHERE $dateFilter AND status = 'pending'
            GROUP BY DATE(created_at)
            ORDER BY order_date DESC
            LIMIT 30
        ");
        $daily = $stmt->fetchAll();

        echo json_encode([
            'ok' => true,
            'period' => $period,
            'summary' => $summary,
            'daily' => $daily
        ]);
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Action not found']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
?>
