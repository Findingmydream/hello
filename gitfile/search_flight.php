<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];

    // 비행기 검색 쿼리
    $stmt = $conn->prepare("
        SELECT * FROM flights 
        WHERE origin = ? AND destination = ? AND departure_date = ?
    ");
    $stmt->bind_param("sss", $origin, $destination, $departure_date);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>항공편 검색 결과</title>
    <style>
        /* 스타일은 기존 CSS 유지 */
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
        .card > * + * {
            margin-top: 1.5rem;
        }
        .header, .flight-details, .info, .buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .buttons {
            flex-direction: row;
            justify-content: space-between;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        .btn-secondary {
            background-color: white;
            border: 1px solid #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <?php while ($flight = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="header">
                        <span>항공편 ID: <?php echo $flight['id']; ?></span>
                    </div>
                    <div class="flight-details">
                        <div>
                            <p>출발</p>
                            <p><?php echo $flight['origin']; ?>
                            <p><?php echo $flight['departure_date']; ?></p>
                        </div>
                        <div>
                            <p>도착</p>
                            <p><?php echo $flight['destination']; ?>
                            <p><?php echo $flight['arrival_date']; ?></p>
                        </div>
                    </div>
                    <div class="info">
                        <p class="price">₩<?php echo number_format($flight['price']); ?></p>
                    </div>
                    <div class="buttons">
                        <a href="reserve_seat.php?flight_id=<?php echo $flight['id']; ?>" class="btn btn-primary">좌석 예약하기</a>
                        <a href="main.php" class="btn btn-secondary">메인으로 돌아가기</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>검색 결과가 없습니다. <a href="main.php">메인 화면</a>으로 돌아가세요.</p>
        <?php endif; ?>
    </div>
</body>
</html>