<?php
session_start();
$config = include __DIR__ . '/config.php';

$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$limit = 5; 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$totalQuery = $conn->query("SELECT COUNT(*) as total FROM product");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM product LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HIZFK | Trang chủ</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include './layout/header.php'; ?>

<div class="container">
  <h1 class="page-title">Bộ sưu tập trang phục</h1>

  <div class="product-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
          <a href="product_detail.php?id=<?php echo (int)$row['product_id']; ?>">
            <img src="<?php echo htmlspecialchars($row['main_image'] ?: 'assets/img/noimage.jpg'); ?>" alt="">
            <h2><?php echo htmlspecialchars($row['product_name']); ?></h2>
            <p class="price"><?php echo number_format($row['rental_price']); ?> ₫ / <?php echo $row['rental_duration']; ?></p>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Hiện chưa có sản phẩm nào.</p>
    <?php endif; ?>
  </div>

  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?php echo $page - 1; ?>">&lt;</a>
    <?php endif; ?>
    <span>Trang <?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?php echo $page + 1; ?>">&gt;</a>
    <?php endif; ?>
  </div>
</div>

<?php include './layout/footer.php'; ?>
</body>
</html>
