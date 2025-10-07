<?php
$config = include __DIR__ . '/config.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$where = "1=1";
$title = "Tất cả sản phẩm";

if (isset($_GET['brand'])) {
    $id = (int)$_GET['brand'];
    $where .= " AND brand_id=$id";
    $getName = $conn->query("SELECT brand_name FROM brand WHERE brand_id=$id")->fetch_assoc();
    $title = "Thương hiệu: " . $getName['brand_name'];
}

if (isset($_GET['color'])) {
    $id = (int)$_GET['color'];
    $where .= " AND color_id=$id";
    $getName = $conn->query("SELECT color_name FROM color WHERE color_id=$id")->fetch_assoc();
    $title = "Màu sắc: " . $getName['color_name'];
}

if (isset($_GET['size'])) {
    $size = $conn->real_escape_string($_GET['size']);
    $where .= " AND size='$size'";
    $title = "Size: " . htmlspecialchars($size);
}

if (isset($_GET['occasion'])) {
    $id = (int)$_GET['occasion'];
    $where .= " AND product_id IN (SELECT product_id FROM product_occasion WHERE occasion_id=$id)";
    $getName = $conn->query("SELECT occasion_name FROM occasion WHERE occasion_id=$id")->fetch_assoc();
    $title = "Dịp: " . $getName['occasion_name'];
}

$sql = "SELECT * FROM product WHERE $where ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($title); ?> | HIZFK</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include './layout/header.php'; ?>

<div class="container">
  <h1 class="page-title"><?php echo htmlspecialchars($title); ?></h1>

  <div class="product-grid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($p = $result->fetch_assoc()): ?>
        <div class="product-card">
            <a href="product_detail.php?id=<?php echo $p['product_id']; ?>">
                <img src="<?php echo htmlspecialchars($p['main_image'] ?: 'assets/img/noimage.jpg'); ?>" alt="">
            </a>
            <h2>
                <a href="product_detail.php?id=<?php echo $p['product_id']; ?>">
                <?php echo htmlspecialchars($p['product_name']); ?>
                </a>
            </h2>
            <p class="price"><?php echo number_format($p['rental_price']); ?> ₫ / <?php echo $p['rental_duration']; ?></p>
        </div>

      <?php endwhile; ?>
    <?php else: ?>
      <p>Không tìm thấy sản phẩm phù hợp.</p>
    <?php endif; ?>
  </div>
</div>

<?php include './layout/footer.php'; ?>
</body>
</html>
