<?php
session_start();
$config = include __DIR__ . '/../config.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin!";
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email không hợp lệ!";
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Mật khẩu xác nhận không trùng khớp!";
        header("Location: register.php");
        exit;
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Mật khẩu phải có ít nhất 6 ký tự!";
        header("Location: register.php");
        exit;
    }

    $check = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email đã tồn tại!";
        header("Location: register.php");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO user (full_name, email, password_hash, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $password_hash, $phone, $address);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Lỗi hệ thống: " . $conn->error;
        header("Location: register.php");
    }
}
