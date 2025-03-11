<?php
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Content-Type: application/json');
include 'db_connection.php'; // Include database connection file

// Authenticate lecturer session
$lecturer_id = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : null;
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;

if (!$lecturer_id || !$session_id) {
    echo json_encode(["error" => "Unauthorized request"]);
    exit();
}

$sql = "SELECT days, start_time, end_time, meeting_duration FROM lecturer_schedule WHERE lecturer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [];
while ($row = $result->fetch_assoc()) {
    $schedule[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["data" => $schedule]);
?>
