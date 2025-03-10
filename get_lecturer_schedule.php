<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Debugging session data
if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode([
        "error" => "Unauthorized",
        "session_data" => $_SESSION // Debugging
    ]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];

$sql = "SELECT days, start_time, end_time, meeting_duration FROM lecturer_schedule WHERE lecturer_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL Error", "details" => $conn->error]);
    exit;
}

$stmt->bind_param("i", $lecturerID);
$stmt->execute();
$result = $stmt->get_result();

$schedule = $result->fetch_assoc();

// Check if no schedule is found
if (!$schedule) {
    echo json_encode(["error" => "No schedule found for lecturer", "lecturer_id" => $lecturerID]);
    exit;
}

echo json_encode($schedule);
?>

