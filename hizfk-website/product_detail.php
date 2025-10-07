<?php
$config = include __DIR__ . '/config.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

// Start session to manage cart
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $qty = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  if (!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = $qty;
  } else {
    $_SESSION['cart'][$id] += $qty;
  }
  header('Location: cart.php');
  exit;
}

$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    $notFound = true;
    $product = null;
} else {
    $notFound = false;
    $product = $res->fetch_assoc();
}

// Fetch related metadata: brand, color, occasions
$brandName = '';
$colorName = '';
$occasions = [];
if (!$notFound) {
  if (!empty($product['brand_id'])) {
    $bRes = $conn->query("SELECT brand_name FROM brand WHERE brand_id=" . (int)$product['brand_id'] . " LIMIT 1");
    if ($bRes && $bRes->num_rows) {
      $brandName = $bRes->fetch_assoc()['brand_name'];
    }
  }
  if (!empty($product['color_id'])) {
    $cRes = $conn->query("SELECT color_name FROM color WHERE color_id=" . (int)$product['color_id'] . " LIMIT 1");
    if ($cRes && $cRes->num_rows) {
      $colorName = $cRes->fetch_assoc()['color_name'];
    }
  }
  $oRes = $conn->query("SELECT o.occasion_name FROM occasion o JOIN product_occasion po ON o.occasion_id = po.occasion_id WHERE po.product_id = " . (int)$id);
  if ($oRes) {
    while ($or = $oRes->fetch_assoc()) {
      $occasions[] = $or['occasion_name'];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $notFound ? 'Sản phẩm không tìm thấy' : htmlspecialchars($product['product_name'] . ' | HIZFK'); ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .product-detail { max-width: 900px; margin: 30px auto; display: flex; gap: 30px; align-items: flex-start; }
    .product-detail img { width: 420px; height: auto; object-fit: cover; border:1px solid #ddd; }
    .product-detail .meta { flex: 1; }
    .back-link { display:inline-block; margin-bottom: 12px; }
  </style>
</head>
<body>
<?php include './layout/header.php'; ?>

<div class="container">
  <?php if ($notFound): ?>
    <h1 class="page-title">Sản phẩm không tìm thấy</h1>
    <p>Không tìm thấy sản phẩm bạn yêu cầu.</p>
    <p><a href="products.php">Quay lại danh sách sản phẩm</a></p>
  <?php else: ?>
    <a class="back-link" href="products.php" style="text-decoration: none;">&larr; Quay lại</a>
    <div class="product-detail">
      <div class="gallery">
        <img src="<?php echo htmlspecialchars($product['main_image'] ?: 'assets/img/noimage.jpg'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
      </div>
      <div class="meta">
        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
        <p class="price"><?php echo number_format($product['rental_price']); ?> ₫ / <?php echo htmlspecialchars($product['rental_duration']); ?></p>

        <ul class="detail-meta">
          <?php if (!empty($product['sku'])): ?><li>SKU: <?php echo htmlspecialchars($product['sku']); ?></li><?php endif; ?>
          <?php if (!empty($brandName)): ?><li>Thương hiệu: <?php echo htmlspecialchars($brandName); ?></li><?php endif; ?>
          <?php if (!empty($colorName)): ?><li>Màu: <?php echo htmlspecialchars($colorName); ?></li><?php endif; ?>
          <?php if (!empty($product['size'])): ?><li>Size: <?php echo htmlspecialchars($product['size']); ?></li><?php endif; ?>
          <?php if (!empty($product['material'])): ?><li>Chất liệu: <?php echo htmlspecialchars($product['material']); ?></li><?php endif; ?>
          <?php if (isset($product['stock'])): ?><li>Tồn kho: <?php echo (int)$product['stock']; ?></li><?php endif; ?>
        </ul>

        <?php if (!empty($product['description'])): ?>
          <h3>Mô tả</h3>
          <p class="detail-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <?php endif; ?>

        <form method="post" style="margin-top:16px; display:flex; gap:8px; align-items:center;">
          <label for="qty">Số lượng</label>
          <input id="qty" type="number" name="quantity" value="1" min="1" style="width:80px; padding:6px;">
          <button class="btn-rent" type="submit" name="add_to_cart">Thêm vào giỏ hàng</button>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include './layout/footer.php'; ?>
</body>
</html>