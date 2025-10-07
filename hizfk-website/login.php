<?php session_start(); ?>
<form action="actions/login_process.php" method="POST">
  <h2>Đăng nhập</h2>
  <?php if(isset($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Mật khẩu" required>
  <button type="submit">Đăng nhập</button>
  <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
</form>