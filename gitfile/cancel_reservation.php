<?php
session_start();
require 'config.php';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 사용자가 예약한 좌석 목록 가져오기
$query = $conn->prepare("SELECT seat_number FROM seats WHERE user_id = ?");
$query->bind_param("s", $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();

// 예약 취소 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_number = $_POST['seat_number'];

    $stmt = $conn->prepare("
        UPDATE seats 
        SET status = 'available', 
            user_id = NULL
        WHERE seat_number = ? 
        AND user_id = ?
    ");
    $stmt->bind_param("ss", $seat_number, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $message = "좌석이 성공적으로 취소되었습니다.";
    } else {
        $message = "좌석 취소에 실패했습니다.";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약 좌석 목록</title>
    <style>
        /* 공통 스타일 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            padding: 1rem;
        }

        .container {
            max-width: 42rem;
            margin: 0 auto;
            padding: 1rem;
        }

        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .message {
            margin: 1rem 0;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background-color: #e0f7fa;
            color: #00796b;
        }

        .seats {
            margin-top: 1.5rem;
        }

        .seat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .seat:last-child {
            border-bottom: none;
        }

        .seat-number {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: #dc2626;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #b91c1c;
        }

        .btn-return {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: white;
            border: 1px solid #d1d5db;
            text-align: center;
            text-decoration: none;
            border-radius: 0.5rem;
            color: #374151;
        }

        .btn-return:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>예약 좌석 목록</h1>
            </div>

            <?php if (isset($message)): ?>
                <div class="message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="seats">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <form method="POST" class="seat">
                        <span class="seat-number">좌석 번호: <?php echo $row['seat_number']; ?></span>
                        <button type="submit" name="seat_number" value="<?php echo $row['seat_number']; ?>" class="btn btn-cancel">취소</button>
                    </form>
                <?php endwhile; ?>
            </div>

            <a href="main.php" class="btn-return">메인으로 돌아가기</a>
        </div>
    </div>
</body>
</html>
