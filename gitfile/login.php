<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 사용자 확인
    $query = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $_SESSION['user_id'] = $username; // 로그인 성공
        header("Location: main.php");
        exit();
    } else {
        $error = "아이디 또는 비밀번호가 잘못되었습니다.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link rel="stylesheet" href="login_formstyle.css"> <!-- CSS 연결 -->
</head>
<body>
    <div class="login-container">
        <h2>로그인</h2>
        <form class="login-form" method="POST" action="">
            <!-- 사용자 이름 입력 -->
            <div class="form-group">
                <label for="username">사용자 이름</label>
                <input type="text" id="username" name="username" placeholder="사용자 이름" required>
            </div>
            <!-- 비밀번호 입력 -->
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" placeholder="비밀번호" required>
            </div>
            <!-- 로그인 버튼 -->
            <button type="submit">로그인</button>
        </form>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    </div>
</body>
</html>
