<?php
session_start(); // 세션 시작
if (!isset($_SESSION['user_id'])) {
    $username = '게스트';
} else {
    $username = htmlspecialchars($_SESSION['user_id']); // htmlspecialchars로 보안 처리
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanseo Airline</title>
    <link rel="stylesheet" href="style.css"> <!-- 외부 CSS 연결 -->
</head>
<body>
    <header class="header">
        <h1>Hanseo Airline</h1>
        <!-- 로그인/로그아웃 버튼 -->
        <?php if ($username === '게스트'): ?>
            <button class="login-btn" onclick="window.location.href='login.php'">로그인</button>
        <?php else: ?>
            <p>안녕하세요, <?php echo $username; ?>님!</p>
            <button class="login-btn" onclick="window.location.href='logout.php'">로그아웃</button>
        <?php endif; ?>
    </header>

    <main class="main">
        <!-- 비행기 검색 섹션 -->
        <section class="reservation">
            <h2>비행기 검색</h2>
            <form method="POST" action="search_flight.php">
                <!-- 출발 도시 -->
                <div class="form-group">
                    <label for="departure">From</label>
                    <input type="text" id="departure" name="origin" placeholder="출발지" required>
                </div>
                <!-- 도착 도시 -->
                <div class="form-group">
                    <label for="arrival">To</label>
                    <input type="text" id="arrival" name="destination" placeholder="도착지" required>
                </div>
                <!-- 출발일 -->
                <div class="form-group">
                    <label for="departure-date">출발일</label>
                    <input type="date" id="departure-date" name="departure_date" required>
                </div>
                <button type="submit">비행기 검색</button>
                <a href="cancel_reservation.php">예약 목록</a>
            </form>
        </section>

        <!-- 유명한 관광지 섹션 -->
        <section class="popular-destinations">
            <h2>유명한 관광지</h2>
            <div class="destinations">
                <!-- 도쿄 -->
                <div class="destination">
                    <img src="tokyo.jpg" alt="도쿄">
                    <p>도쿄</p>
                    <p>20만원부터</p>
                </div>
                <!-- 파리 -->
                <div class="destination">
                    <img src="paris.jpg" alt="Paris">
                    <p>Paris</p>
                    <p>80만원부터</p>
                </div>
                <!-- 뉴욕 -->
                <div class="destination">
                    <img src="newyork.jpg" alt="New York">
                    <p>New York</p>
                    <p>100만원부터</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
