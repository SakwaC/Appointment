<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID");
header("Access-Control-Allow-Credentials: true");

$lecturerID = $_POST['lecturer_id'] ?? $_SERVER['HTTP_X_LECTURER_ID'] ?? null;
$sessionID = session_id();

if ($lecturerID) {
    $_SESSION['lecturer_id'] = $lecturerID;
    $_SESSION['is_authenticated'] = true;
}

if (!isset($_SESSION['lecturer_id']) || !$_SESSION['is_authenticated']) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access",
        "session_info" => [
            "active" => session_status() === PHP_SESSION_ACTIVE,
            "lecturer_id" => isset($_SESSION['lecturer_id']),
            "session_id" => $sessionID
        ]
    ]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];
$selectedDays = $_POST['days'] ?? [];
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$meeting_duration = intval($_POST['meeting_duration'] ?? 0);

if (empty($selectedDays) || empty($start_time) || empty($end_time) || $meeting_duration <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input data"
    ]);
    exit;
}

// First check for existing days
$checkSQL = "SELECT days FROM lecturer_schedule WHERE lecturer_id = ? AND days = ?";
$checkStmt = $conn->prepare($checkSQL);

// Update existing or insert new records
$updateSQL = "UPDATE lecturer_schedule SET start_time = ?, end_time = ?, meeting_duration = ? WHERE lecturer_id = ? AND days = ?";
$insertSQL = "INSERT INTO lecturer_schedule (lecturer_id, days, start_time, end_time, meeting_duration) VALUES (?, ?, ?, ?, ?)";

$updateStmt = $conn->prepare($updateSQL);
$insertStmt = $conn->prepare($insertSQL);

$successCount = 0;

foreach ($selectedDays as $day) {
    // Check if day exists
    $checkStmt->bind_param("is", $lecturerID, $day);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $updateStmt->bind_param("sssis", $start_time, $end_time, $meeting_duration, $lecturerID, $day);
        if ($updateStmt->execute()) {
            $successCount++;
        }
    } else {
        // Insert new record
        $insertStmt->bind_param("isssi", $lecturerID, $day, $start_time, $end_time, $meeting_duration);
        if ($insertStmt->execute()) {
            $successCount++;
        }
    }
}

if ($successCount === count($selectedDays)) {
    echo json_encode([
        "status" => "success",
        "message" => "Schedule updated successfully for all selected days"
    ]);
} else {
    echo json_encode([
        "status" => "partial_success",
        "message" => "Schedule updated for {$successCount} out of " . count($selectedDays) . " days"
    ]);
}

$checkStmt->close();
$updateStmt->close();
$insertStmt->close();
$conn->close();
?>
