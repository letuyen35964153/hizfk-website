<?php
session_start();
$config = include __DIR__ . '/../config.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
        header("Location: login.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['user_id'],
            'name' => $user['full_name'],
            'email' => $user['email']
        ];
        $_SESSION['success'] = "Đăng nhập thành công!";
        header("Location: index.php");
    } else {
        $_SESSION['error'] = "Email hoặc mật khẩu không chính xác!";
        header("Location: login.php");
    }
}
