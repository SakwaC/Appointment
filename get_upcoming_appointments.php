<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Set CORS headers
header('Access-Control-Allow-Origin: http://localhost:3000'); 
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Session-ID, X-Student-ID, PHPSESSID');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Content-Type: application/json');


// Debugging logs
error_log("Session ID: " . session_id());
error_log("SESSION DATA: " . print_r($_SESSION, true));

// Validate session ID from request
$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? null;


// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Validate Student ID
$studentId = $_SERVER['HTTP_X_STUDENT_ID'] ?? $_GET['student_id'] ?? null;

// Database connection
require 'db_connection.php';

// Fetch upcoming appointments
$sql = "SELECT
            a.Appointment_ID,
            a.student_id,
            s.Name AS student_name,
            l.name AS lecturer_name,
            l.contact_No AS lecturer_phone,
            a.department,
            a.appointment_date,
            a.time_of_appointment,
            a.Description AS appointment_description
        FROM appoint a
        JOIN students s ON a.student_id = s.Student_ID
        JOIN lecturer l ON a.lecturer_id = l.lecturer_ID
        WHERE a.student_id = ?
        AND a.status = 'approved'
        AND a.appointment_date >= CURDATE()
        ORDER BY a.appointment_date ASC, a.time_of_appointment ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentId);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Return JSON response
echo json_encode($appointments ?: ["message" => "No upcoming appointments found"]);

$stmt->close();
$conn->close();
