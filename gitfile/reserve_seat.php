<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 예시로 flight_id가 URL 파라미터로 전달된다고 가정
$flight_id = $_GET['flight_id'] ?? 1; // 기본값으로 1번 비행기를 사용

// 선택된 비행기의 좌석만 가져오기
$query = $conn->prepare("SELECT * FROM seats WHERE flight_id = ?");
$query->bind_param("i", $flight_id);
$query->execute();
$result = $query->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_number = $_POST['seat_number'];

    // 좌석 예약 처리
    $stmt = $conn->prepare("UPDATE seats SET status = 'reserved', user_id = ? WHERE seat_number = ? AND flight_id = ? AND status = 'available'");
    $stmt->bind_param("ssi", $_SESSION['user_id'], $seat_number, $flight_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => '예약이 완료되었습니다!']);
    } else {
        echo json_encode(['success' => false, 'message' => '예약에 실패했습니다. 다른 좌석을 선택해 주세요.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>항공기 좌석 선택</title>
    <style>
        /* styles.css 내용 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.5;
        }

        .container {
            max-width: 42rem;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .title {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .seat-container {
            margin-bottom: 2rem;
        }

        .legend {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-box {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 0.25rem;
        }

        .legend-box.available {
            background-color: #DBEAFE;
        }

        .legend-box.selected {
            background-color: #2563EB;
        }

        .legend-box.unavailable {
            background-color: #D1D5DB;
        }

        .seat-grid-container {
            background-color: #F9FAFB;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            justify-content: center;
            max-width: fit-content;
            margin: 0 auto;
        }

        .seat {
            width: 3rem;
            height: 3rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .seat.available {
            background-color: #DBEAFE;
        }

        .seat.available:hover {
            background-color: #BFDBFE;
        }

        .seat.selected {
            background-color: #2563EB;
            color: white;
        }

        .seat.selected:hover {
            background-color: #1D4ED8;
        }

        .seat.unavailable {
            background-color: #D1D5DB;
            cursor: not-allowed;
        }

        .selection-message {
            text-align: center;
        }

        .message {
            font-size: 1.125rem;
            color: #6B7280;
        }

        .selected-text {
            font-size: 1.125rem;
        }

        .selected-text .seat-number {
            font-weight: bold;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .reserve-button {
            background-color: #2563EB;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }
        .reserve-button:hover {
            background-color: #1D4ED8;
        }
    </style>
</head>
<body>
    <main class="container">
        <h1 class="title">좌석 선택 / Seat Selection</h1>

        <?php if (isset($message)) echo "<p>$message</p>"; ?>

        <div class="seat-container">
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box available"></div>
                    <span>이용 가능 / Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box selected"></div>
                    <span>선택됨 / Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box unavailable"></div>
                    <span>이용 불가 / Unavailable</span>
                </div>
            </div>

            <div class="seat-grid-container">
                <div class="seat-grid" id="seatGrid">
                    <!-- Seats will be generated by JavaScript -->
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <button type="submit" name="seat_number" value="<?php echo $row['seat_number']; ?>"
                                class="seat <?php echo $row['status'] === 'available' ? 'available' : 'reserved'; ?>"
                                <?php echo $row['status'] === 'reserved' ? 'disabled' : ''; ?>>
                            <?php echo $row['seat_number']; ?>
                        </button>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="selection-message" id="selectionMessage">
            <p class="message">좌석을 선택해주세요<br>Please select a seat</p>
        </div>

        <div class="button-container">
            <button class="reserve-button" id="reserveButton">예약하기 / Reserve</button>
        </div>
        <div id="reservationMessage" style="display:none; text-align:center; margin-top:1rem; font-size:1.25rem;"></div>
        <a href="main.php">메인 화면</a>
    </main>

    <script>
        // script.js 내용
        document.addEventListener('DOMContentLoaded', function () {
            let selectedSeat = null;
            const reserveButton = document.getElementById('reserveButton');
            const reservationMessage = document.getElementById('reservationMessage');

            document.querySelectorAll('.seat').forEach(seat => {
                seat.addEventListener('click', function () {
                  if (seat.classList.contains('available')) {
                     document.querySelectorAll('.selected').forEach(s => s.classList.remove('selected'));
                      seat.classList.add('selected');
                      selectedSeat = seat.value;
            }
        });
    });

    reserveButton.addEventListener('click', function () {
        if (selectedSeat) {
            const formData = new FormData();
            formData.append('seat_number', selectedSeat);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                reservationMessage.style.display = "block";
                reservationMessage.textContent = data.message;
                reservationMessage.style.color = data.success ? 'green' : 'red';
                if (data.success) {
                    setTimeout(() => location.reload(), 2000); // 2초 후 페이지 새로고침
                }
            })
            .catch(error => {
                reservationMessage.style.display = "block";
                reservationMessage.textContent = "서버 에러가 발생했습니다.";
                reservationMessage.style.color = 'red';
            });
            } else {
                alert("좌석을 먼저 선택해주세요.");
            }
        });
    });
    </script>
</body>
</html>