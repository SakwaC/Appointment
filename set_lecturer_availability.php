<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Debugging session
if (!isset($_SESSION['lecturer_id'])) {
    echo json_encode(["message" => "Unauthorized", "error" => "Session expired or lecturer not logged in"]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];
$days = implode(", ", $_POST['days'] ?? []);
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$meeting_duration = intval($_POST['meeting_duration'] ?? 0);

// Ensure lecturer_id is unique in table
$sql = "INSERT INTO lecturer_schedule (lecturer_id, days, start_time, end_time, meeting_duration) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        days = VALUES(days), start_time = VALUES(start_time), 
        end_time = VALUES(end_time), meeting_duration = VALUES(meeting_duration)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["message" => "SQL Error", "error" => $conn->error]);
    exit;
}

$stmt->bind_param("isssi", $lecturerID, $days, $start_time, $end_time, $meeting_duration);

if ($stmt->execute()) {
    echo json_encode(["message" => "Availability updated successfully"]);
} else {
    echo json_encode(["message" => "Failed to update", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
