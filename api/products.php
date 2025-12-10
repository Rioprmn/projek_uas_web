<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    $pdo = getPDO(true);

    if ($action === 'list') {
        // Get all products from database
        $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` ASC");
        $products = $stmt->fetchAll();

        echo json_encode(['ok' => true, 'products' => $products]);
        exit;
    }

    if ($action === 'create') {
        // Add new product
        $id = intval($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $code = $_POST['code'] ?? '';
        $price = intval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $image = $_POST['image'] ?? null;
        $category = $_POST['category'] ?? '';
        $unit = $_POST['unit'] ?? 'pcs';

        if (!$id || !$name || !$code) {
            echo json_encode(['ok' => false, 'msg' => 'Data tidak lengkap']);
            exit;
        }

        // Handle uploaded image file if present (field name: image_file)
        if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            $tmp = $_FILES['image_file']['tmp_name'];
            $orig = basename($_FILES['image_file']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $nameFile = 'p_' . time() . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . $ext : '');
            $dest = $uploadsDir . '/' . $nameFile;
            if (move_uploaded_file($tmp, $dest)) {
                // Save a web-relative path
                $image = 'uploads/' . $nameFile;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO `products` (id, name, code, price, stock, image, category, unit)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name=?, code=?, price=?, stock=?, image=?, category=?, unit=?
        ");
        $stmt->execute([$id, $name, $code, $price, $stock, $image, $category, $unit,
                       $name, $code, $price, $stock, $image, $category, $unit]);

        echo json_encode(['ok' => true, 'msg' => 'Produk berhasil disimpan', 'image' => $image]);
        exit;
    }

    if ($action === 'delete') {
        // Delete product
        $id = intval($_POST['id'] ?? 0);

        if (!$id) {
            echo json_encode(['ok' => false, 'msg' => 'ID tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM `products` WHERE `id` = ?");
        $stmt->execute([$id]);

        echo json_encode(['ok' => true, 'msg' => 'Produk berhasil dihapus']);
        exit;
    }

    if ($action === 'update') {
        // Update product
        $id = intval($_POST['id'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);

        if (!$id) {
            echo json_encode(['ok' => false, 'msg' => 'ID tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE `products` SET `stock` = ? WHERE `id` = ?");
        $stmt->execute([$stock, $id]);

        echo json_encode(['ok' => true, 'msg' => 'Produk berhasil diperbarui']);
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Action tidak ditemukan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
?>
