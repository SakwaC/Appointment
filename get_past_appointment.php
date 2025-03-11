<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Student-ID, X-Session-ID");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$studentID = $_SERVER['HTTP_X_STUDENT_ID'] ?? null;

if (!$studentID) {
    echo json_encode(["error" => "Student ID not provided"]);
    exit;
}
error_log("Using Student ID for query: " . $studentID);

if (!$conn) {
    echo json_encode(["error" => "Database connection failed", "debug" => "Check db_connection.php"]);
    exit;
}

$sql = "SELECT 
        a.appointment_id,
        a.lecturer_id,
        a.Department,
        a.appointment_date,
        a.time_of_appointment,
        a.Description,
        l.Name AS lecturer_name,
        l.Contact_No AS lecturer_contact
    FROM appoint a
    LEFT JOIN lecturer l ON a.lecturer_id = l.id
    WHERE a.student_id = ?
    ORDER BY a.appointment_date DESC
    ";



$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare SQL statement", "debug" => $conn->error]);
    exit;
}

$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
error_log("SQL Query executed: " . $sql);

$appoint = [];
while ($row = $result->fetch_assoc()) {
    error_log("Appointment data found: " . json_encode($row));
    $appoint[] = $row;
}

error_log("Total appointments found: " . count($appoint));

// Debugging: Check what is being returned
if (empty($appoint)) {
    echo json_encode(["data" =>[], "message" => "No past appointments found"]);
    exit;
} else {
    echo json_encode(["data" => $appoint, "message" => "Success"]);
}

// Close database resources
$stmt->close();
$conn->close();
?>
