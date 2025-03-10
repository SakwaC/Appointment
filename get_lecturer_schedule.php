<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID");
header("Access-Control-Allow-Credentials: true");

$lecturerID = $_GET['lecturer_id'];
$sessionID = session_id();

if ($lecturerID) {
    $_SESSION['lecturer_id'] = $lecturerID;
    $_SESSION['is_authenticated'] = true;
}

if (!isset($_SESSION['lecturer_id']) || !$_SESSION['is_authenticated']) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access",
        "session_status" => [
            "active" => session_status() === PHP_SESSION_ACTIVE,
            "lecturer_id_set" => isset($_SESSION['lecturer_id']),
            "is_authenticated" => isset($_SESSION['is_authenticated']) ? $_SESSION['is_authenticated'] : false
        ]
    ]);
    exit;
}

$sql = "SELECT days, start_time, end_time, meeting_duration 
        FROM lecturer_schedule 
        WHERE lecturer_id = ? 
        ORDER BY FIELD(days, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL Error", "details" => $conn->error]);
    exit;
}

$stmt->bind_param("i", $lecturerID);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

if (count($schedules) > 0) {
    echo json_encode([
        "status" => "success",
        "data" => $schedules,
        "session_id" => $sessionID
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No schedule found",
        "lecturer_id" => $lecturerID,
        "session_id" => $sessionID
    ]);
}

$stmt->close();
$conn->close();
?>
