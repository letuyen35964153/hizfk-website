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
              echo '<a href="products.php?brand=' . $b['brand_id'] . '">' . htmlspecialchars($b['brand_name']) . '</a>';
            endwhile;
            ?>
          </div>
          <div class="dropdown-column">
            <h4>MÀU SẮC</h4>
            <?php
            $colorResult = $conn->query("SELECT * FROM color ORDER BY color_name ASC");
            while ($c = $colorResult->fetch_assoc()):
              echo '<a href="products.php?color=' . $c['color_id'] . '">' . htmlspecialchars($c['color_name']) . '</a>';
            endwhile;
            ?>
          </div>
          <div class="dropdown-column">
            <h4>DỊP</h4>
            <?php
            $occResult = $conn->query("SELECT * FROM occasion ORDER BY occasion_name ASC");
            while ($o = $occResult->fetch_assoc()):
              echo '<a href="products.php?occasion=' . $o['occasion_id'] . '">' . htmlspecialchars($o['occasion_name']) . '</a>';
            endwhile;
            ?>
          </div>
          <div class="dropdown-column">
            <h4>SIZE</h4>
            <?php
            $sizeResult = $conn->query("SELECT DISTINCT size FROM product WHERE size IS NOT NULL AND size!='' ORDER BY size ASC");
            while ($s = $sizeResult->fetch_assoc()):
              echo '<a href="products.php?size=' . urlencode($s['size']) . '">' . htmlspecialchars($s['size']) . '</a>';
            endwhile;
            ?>
          </div>
        </div>
      </div>

      <a href="contact.php">LIÊN HỆ</a>
    </nav>

    <div class="header-icons">
      <div class="user-dropdown">
        <span class="user-icon">👤</span>
        <div class="user-panel">
          <?php if (!isset($_SESSION['user'])): ?>
            <form method="POST" action="actions/login_process.php" class="login-form">
              <h4>Đăng nhập</h4>
              <input type="email" name="email" placeholder="Email" required>
              <input type="password" name="password" placeholder="Mật khẩu" required>
              <button type="submit">Đăng nhập</button>
              <p class="new-user">Khách hàng mới? <a href="register.php">Đăng ký</a></p>
            </form>
          <?php else: ?>
            <div class="user-info">
              <h4>Xin chào, <?php echo htmlspecialchars($_SESSION['user']['name']); ?> 👋</h4>
              <p>Email: <?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
              <a href="logout.php" class="logout-btn">Đăng xuất</a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <a href="cart.php" class="cart-icon" title="Giỏ hàng">🛒</a>
    </div>
  </div>
</header>
