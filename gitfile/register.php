<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // 입력값 유효성 검사
    if (!empty($username) && !empty($password) && !empty($email)) {
        // 사용자 중복 확인
        $query = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {
            $error = "이미 존재하는 사용자 이름입니다.";
        } else {
            // 새로운 사용자 추가
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $email);
            if ($stmt->execute()) {
                $success = "회원가입이 완료되었습니다. 로그인 해주세요!";
            } else {
                $error = "회원가입 중 오류가 발생했습니다.";
            }
        }
    } else {
        $error = "모든 필드를 입력해야 합니다.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>회원가입</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .form-container {
      background: #ffffff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    .title {
      text-align: center;
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 1.5rem;
      color: #333;
    }
    .title::after {
      content: '';
      display: block;
      width: 50px;
      height: 3px;
      background: #007bff;
      margin: 0.5rem auto 0;
      border-radius: 5px;
    }
    .input-group {
      margin-bottom: 1.5rem;
    }
    .input-group label {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 0.5rem;
      display: block;
    }
    .input-group input {
      width: 100%;
      padding: 0.8rem;
      font-size: 1rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      outline: none;
      transition: 0.3s ease-in-out;
    }
    .input-group input:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    .submit-btn {
      width: 100%;
      background: #007bff;
      color: #fff;
      padding: 0.8rem;
      font-size: 1rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .submit-btn:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1 class="title">회원가입</h1>
    <form method="POST" action="">
      <div class="input-group">
        <label for="username">아이디</label>
        <input type="text" id="username" name="username" placeholder="아이디를 입력하세요" required>
      </div>
      <div class="input-group">
        <label for="password">비밀번호</label>
        <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required>
      </div>
      <div class="input-group">
        <label for="email">이메일</label>
        <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required>
      </div>
      <button type="submit" class="submit-btn">회원가입</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">이미 계정이 있으신가요? <a href="login.php">로그인</a></p>
    <?php
    if (isset($error)) {
        echo "<p style='color:red; text-align:center;'>$error</p>";
    }
    if (isset($success)) {
        echo "<p style='color:green; text-align:center;'>$success</p>";
    }
    ?>
  </div>
</body>
</html>