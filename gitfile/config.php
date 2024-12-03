<?php
$host = "localhost"; // MySQL 서버
$username = "root"; // 사용자 이름
$password = ""; // 비밀번호
$dbname = "airline_booking"; // 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($host, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
