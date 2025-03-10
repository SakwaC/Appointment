<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db_connection.php';

// Set headers for JSON response and CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(["error" => "Lecturer not logged in"]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];

$sql = "SELECT a.appointment_id, a.student_id, s.username AS student_name, 
               a.department, a.appointment_date, a.time_of_appointment, 
               a.appointment_description 
        FROM appoint a
        JOIN students s ON a.student_id = s.student_id
        WHERE a.lecturer_id = ?
        ORDER BY a.appointment_date ASC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Database query failed"]);
    exit;
}

$stmt->bind_param("i", $lecturerID);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>
