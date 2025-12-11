<?php
require_once __DIR__ . '/../config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

try {
    $pdo = getPDO(true);

    if ($action === 'create') {
        // Create new order
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');
        $customerAddress = trim($_POST['customer_address'] ?? '');
        $items = json_decode($_POST['items'] ?? '[]', true);
        $discount = intval($_POST['discount'] ?? 0);

        if (empty($customerName) || !is_array($items) || count($items) === 0) {
            echo json_encode(['ok' => false, 'msg' => 'Data tidak lengkap']);
            exit;
        }

        // Calculate totals safely
        $totalAmount = 0;
        foreach ($items as $item) {
            $price = isset($item['price']) ? intval($item['price']) : 0;
            $qty = isset($item['qty']) ? intval($item['qty']) : 0;
            $totalAmount += $price * $qty;
        }
        $finalAmount = max(0, $totalAmount - $discount);

        // Generate order number
        $orderNumber = 'ORD' . date('YmdHis') . mt_rand(1000, 9999);

        // Payment method and optional proof
        $paymentMethod = $_POST['payment_method'] ?? 'TUNAI';
        $paymentProof = null;

        if (!empty($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
            $tmp = $_FILES['payment_proof']['tmp_name'];
            $orig = basename($_FILES['payment_proof']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $nameFile = 'pay_' . time() . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . $ext : '');
            $dest = $uploadsDir . '/' . $nameFile;
            if (move_uploaded_file($tmp, $dest)) {
                $paymentProof = 'uploads/' . $nameFile;
            }
        }

        $initialPaymentStatus = ($paymentMethod === 'TUNAI') ? 'paid' : 'pending';
        $orderStatus = 'pending';

        // Insert order and items transactionally
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO `orders` 
                (order_number, customer_name, customer_phone, customer_address, total_amount, discount, final_amount, payment_method, payment_proof, payment_status, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$orderNumber, $customerName, $customerPhone, $customerAddress, $totalAmount, $discount, $finalAmount, $paymentMethod, $paymentProof, $initialPaymentStatus, $orderStatus]);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO `order_items` (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)"
            );

            foreach ($items as $item) {
                $price = isset($item['price']) ? intval($item['price']) : 0;
                $qty = isset($item['qty']) ? intval($item['qty']) : 0;
                $subtotal = $price * $qty;
                $itemStmt->execute([
                    $orderId,
                    $item['id'] ?? null,
                    $item['name'] ?? '',
                    $price,
                    $qty,
                    $subtotal
                ]);
            }

            $pdo->commit();

            echo json_encode([
                'ok' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => (int)$finalAmount,
                'payment_status' => $initialPaymentStatus,
                'payment_proof' => $paymentProof,
                'msg' => 'Pesanan berhasil dibuat'
            ]);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['ok' => false, 'msg' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
            exit;
        }
    }

    if ($action === 'list') {
        // Get all orders
        $stmt = $pdo->query("SELECT * FROM `orders` ORDER BY `created_at` DESC");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            // normalize numeric fields
            $order['id'] = isset($order['id']) ? (int)$order['id'] : null;
            $order['total_amount'] = isset($order['total_amount']) ? (int)$order['total_amount'] : 0;
            $order['discount'] = isset($order['discount']) ? (int)$order['discount'] : 0;
            $order['final_amount'] = isset($order['final_amount']) ? (int)$order['final_amount'] : 0;

            $itemStmt = $pdo->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as &$it) {
                $it['id'] = isset($it['id']) ? (int)$it['id'] : null;
                $it['product_id'] = isset($it['product_id']) ? (int)$it['product_id'] : null;
                $it['price'] = isset($it['price']) ? (int)$it['price'] : 0;
                $it['quantity'] = isset($it['quantity']) ? (int)$it['quantity'] : 0;
                $it['subtotal'] = isset($it['subtotal']) ? (int)$it['subtotal'] : 0;
            }
            $order['items'] = $items;
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
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['ok' => false, 'msg' => 'Order not found']);
            exit;
        }

        $itemStmt = $pdo->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
        $itemStmt->execute([$orderId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as &$it) {
            $it['id'] = isset($it['id']) ? (int)$it['id'] : null;
            $it['product_id'] = isset($it['product_id']) ? (int)$it['product_id'] : null;
            $it['price'] = isset($it['price']) ? (int)$it['price'] : 0;
            $it['quantity'] = isset($it['quantity']) ? (int)$it['quantity'] : 0;
            $it['subtotal'] = isset($it['subtotal']) ? (int)$it['subtotal'] : 0;
        }
        $order['items'] = $items;
        // normalize numeric fields on order
        $order['id'] = isset($order['id']) ? (int)$order['id'] : null;
        $order['total_amount'] = isset($order['total_amount']) ? (int)$order['total_amount'] : 0;
        $order['discount'] = isset($order['discount']) ? (int)$order['discount'] : 0;
        $order['final_amount'] = isset($order['final_amount']) ? (int)$order['final_amount'] : 0;

        echo json_encode(['ok' => true, 'order' => $order]);
        exit;
    }

    if ($action === 'update_status') {
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Admin only']);
            exit;
        }
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

    if ($action === 'reset') {
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Admin only']);
            exit;
        }
        try {
            $pdo->beginTransaction();
            $pdo->exec("DELETE FROM `order_items`");
            $pdo->exec("DELETE FROM `orders`");
            $pdo->commit();
            echo json_encode(['ok' => true, 'msg' => 'All orders deleted']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['ok' => false, 'msg' => 'Failed to reset: ' . $e->getMessage()]);
            exit;
        }
    }

    if ($action === 'upload_proof') {
        // Upload payment proof (customer) and set payment_status to 'pending'
        $orderId = intval($_POST['id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['ok' => false, 'msg' => 'Order ID required']);
            exit;
        }

        if (empty($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['ok' => false, 'msg' => 'No file uploaded']);
            exit;
        }

        // basic validation: size <= 5MB and allowed mime
        $file = $_FILES['payment_proof'];
        $maxBytes = 5 * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            echo json_encode(['ok' => false, 'msg' => 'File terlalu besar (max 5MB)']);
            exit;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($mime, $allowed)) {
            echo json_encode(['ok' => false, 'msg' => 'Tipe file tidak didukung']);
            exit;
        }

        $uploadsDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
        $tmp = $file['tmp_name'];
        $orig = basename($file['name']);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $nameFile = 'pay_' . time() . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . $ext : '');
        $dest = $uploadsDir . '/' . $nameFile;
        if (move_uploaded_file($tmp, $dest)) {
            $path = 'uploads/' . $nameFile;
            $stmt = $pdo->prepare("UPDATE `orders` SET payment_proof = ?, payment_status = 'pending', payment_method = 'TRANSFER' WHERE id = ?");
            $stmt->execute([$path, $orderId]);
            echo json_encode(['ok' => true, 'payment_proof' => $path]);
            exit;
        }

        echo json_encode(['ok' => false, 'msg' => 'Failed to move uploaded file']);
        exit;
    }

    if ($action === 'confirm_payment') {
        // Admin confirms payment (mark paid)
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Admin only']);
            exit;
        }
        $orderId = intval($_POST['id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['ok' => false, 'msg' => 'Order ID required']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE `orders` SET payment_status = 'paid', status = 'completed' WHERE id = ?");
        $stmt->execute([$orderId]);
        echo json_encode(['ok' => true]);
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
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(final_amount),0) as total_revenue,
            COALESCE(AVG(final_amount),0) as avg_order_value
        FROM `orders`
        WHERE $dateFilter");
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get daily breakdown
        $stmt = $pdo->query("SELECT 
            DATE(created_at) as order_date,
            COUNT(*) as daily_orders,
            COALESCE(SUM(final_amount),0) as daily_revenue
        FROM `orders`
        WHERE $dateFilter
        GROUP BY DATE(created_at)
        ORDER BY order_date DESC
        LIMIT 30");
        $daily = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
