<?php session_start(); ?>
<form action="actions/register_process.php" method="POST">
  <h2>Đăng ký tài khoản</h2>
  <?php if(isset($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>
  <input type="text" name="full_name" placeholder="Họ và tên" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Mật khẩu" required>
  <input type="password" name="confirm" placeholder="Nhập lại mật khẩu" required>
  <input type="text" name="phone" placeholder="Số điện thoại">
  <input type="text" name="address" placeholder="Địa chỉ">
  <button type="submit">Đăng ký</button>
  <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</form>
