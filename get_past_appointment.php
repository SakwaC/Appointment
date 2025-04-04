<?php
session_start();
require 'db_connection.php';

// Define allowed origins
$allowedOrigins = [
    'http://localhost:3000',
    'http://127.0.0.1:5500',
    
];

// Get the origin from the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;

// Check if the origin is allowed
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Student-ID, X-Session-ID");
    header("Access-Control-Allow-Credentials: true");
} else {
    // Optionally, handle invalid origin (e.g., log, send error)
    // header("HTTP/1.1 403 Forbidden");
    // exit;
}

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
            l.Lecturer_ID,
            l.Name AS lecturer_name,
            l.Contact_No AS lecturer_contact,
            a.Department,
            a.appointment_date,
            a.time_of_appointment,
            a.Description
        FROM appoint a
        INNER JOIN lecturer l ON a.lecturer_id = l.Lecturer_ID
        WHERE a.student_id = ?
        ORDER BY a.appointment_date DESC";

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

if (empty($appoint)) {
    echo json_encode(["data" =>[], "message" => "No past appointments found"]);
    exit;
} else {
    echo json_encode(["data" => $appoint, "message" => "Success"]);
}

$stmt->close();
$conn->close();
?>