<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = include __DIR__ . '/../config.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
<header class="header">
  <div class="header-inner">
  <a href="index.php" class="logo">HIZFK</a>

    <nav class="nav">
      <a href="index.php">TRANG CHỦ</a>
      <a href="about.php">GIỚI THIỆU</a>
      <div class="dropdown">
        <a href="products.php" class="dropbtn">SẢN PHẨM ▾</a>

        <div class="dropdown-content">
          <div class="dropdown-column">
            <h4>THƯƠNG HIỆU</h4>
            <?php
            $brandResult = $conn->query("SELECT * FROM brand ORDER BY brand_name ASC");
            while ($b = $brandResult->fetch_assoc()):
            ?>
              <a href="products.php?brand=<?php echo $b['brand_id']; ?>">
                <?php echo htmlspecialchars($b['brand_name']); ?>
              </a>
            <?php endwhile; ?>
          </div>

          <div class="dropdown-column">
            <h4>MÀU SẮC</h4>
            <?php
            $colorResult = $conn->query("SELECT * FROM color ORDER BY color_name ASC");
            while ($c = $colorResult->fetch_assoc()):
            ?>
              <a href="products.php?color=<?php echo $c['color_id']; ?>">
                <?php echo htmlspecialchars($c['color_name']); ?>
              </a>
            <?php endwhile; ?>
          </div>

          <div class="dropdown-column">
            <h4>DỊP</h4>
            <?php
            $occasionResult = $conn->query("SELECT * FROM occasion ORDER BY occasion_name ASC");
            while ($o = $occasionResult->fetch_assoc()):
            ?>
              <a href="products.php?occasion=<?php echo $o['occasion_id']; ?>">
                <?php echo htmlspecialchars($o['occasion_name']); ?>
              </a>
            <?php endwhile; ?>
          </div>

          <div class="dropdown-column">
            <h4>SIZE</h4>
            <?php
            $sizeResult = $conn->query("SELECT DISTINCT size FROM product WHERE size IS NOT NULL AND size != '' ORDER BY size");
            while ($s = $sizeResult->fetch_assoc()):
            ?>
              <a href="products.php?size=<?php echo urlencode($s['size']); ?>">
                <?php echo htmlspecialchars($s['size']); ?>
              </a>
            <?php endwhile; ?>
          </div>
        </div>
      </div>

  <a href="contact.php">LIÊN HỆ</a>
    </nav>

    <div class="header-icons">
  <a href="account.php" title="Tài khoản">👤</a>
  <a href="cart.php" title="Giỏ hàng">🛒</a>
    </div>
  </div>
</header>
