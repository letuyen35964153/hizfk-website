<?php
session_start();
include __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Giỏ hàng | HIZFK</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include './layout/header.php'; ?>

<?php
// handle remove or update actions
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) die('DB error');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
  $rid = (int)$_GET['remove'];
  if (isset($_SESSION['cart'][$rid])) unset($_SESSION['cart'][$rid]);
  header('Location: cart.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
  foreach ($_POST['quant'] as $pid => $q) {
    $pid = (int)$pid; $q = max(0, (int)$q);
    if ($q === 0) {
      unset($_SESSION['cart'][$pid]);
    } else {
      $_SESSION['cart'][$pid] = $q;
    }
  }
  header('Location: cart.php');
  exit;
}

?>

<div class="container">
  <h1 class="page-title">Giỏ hàng</h1>

  <?php if (empty($_SESSION['cart'])): ?>
    <p>Giỏ hàng hiện đang trống.</p>
  <?php else: ?>
    <form method="post">
      <table style="width:100%; border-collapse: collapse;">
        <thead>
          <tr style="text-align:left; border-bottom:1px solid #ddd;"><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Tổng</th><th></th></tr>
        </thead>
        <tbody>
        <?php
          $total = 0;
          $ids = array_map('intval', array_keys($_SESSION['cart']));
          if (!empty($ids)) {
            $in = implode(',', $ids);
            $res = $conn->query("SELECT * FROM product WHERE product_id IN ($in)");
            $rows = [];
            while ($r = $res->fetch_assoc()) $rows[$r['product_id']] = $r;
            foreach ($_SESSION['cart'] as $pid => $qty) {
              $pid = (int)$pid; $qty = (int)$qty;
              if (!isset($rows[$pid])) continue;
              $p = $rows[$pid];
              $line = $qty * $p['rental_price'];
              $total += $line;
        ?>
          <tr style="border-bottom:1px solid #eee;">
            <td>
              <a href="product_detail.php?id=<?php echo $p['product_id']; ?>"><?php echo htmlspecialchars($p['product_name']); ?></a>
            </td>
            <td><?php echo number_format($p['rental_price']); ?> ₫</td>
            <td><input type="number" name="quant[<?php echo $p['product_id']; ?>]" value="<?php echo $qty; ?>" min="0" style="width:80px"></td>
            <td><?php echo number_format($line); ?> ₫</td>
            <td><a href="cart.php?remove=<?php echo $p['product_id']; ?>">Xóa</a></td>
          </tr>
        <?php }
          }
        ?>
        </tbody>
      </table>

      <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <button type="submit" name="update_cart" style="padding:8px 12px;">Cập nhật giỏ</button>
        </div>
        <div style="font-weight:bold;">Tổng: <?php echo number_format($total); ?> ₫</div>
      </div>
    </form>
  <?php endif; ?>

</div>

<?php include './layout/footer.php'; ?>
</body>
</html>