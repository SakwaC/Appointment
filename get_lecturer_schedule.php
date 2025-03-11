<?php
session_start();
require 'db_connection.php';

// Allow CORS for requests from your frontend
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Content-Type: application/json');

// Handle OPTIONS request (Preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Check if lecturer_id is set
if (!isset($_GET['lecturer_ID'])) {
    echo json_encode(["status" => "error", "message" => "Missing lecturer_ID"]);
    exit;
}

$lecturerID = intval($_GET['lecturer_ID']);
$sessionID = session_id();

// Query lecturer's schedule
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
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode([
    "status" => count($schedules) > 0 ? "success" : "error",
    "data" => $schedules,
    "message" => count($schedules) > 0 ? "Schedules found" : "No schedule found",
    "lecturer_ID" => $lecturerID,
    "session_id" => $sessionID
]);

$stmt->close();
$conn->close();
?>
