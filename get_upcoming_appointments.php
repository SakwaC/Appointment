<?php
session_start();
require 'db_connection.php';

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if the user is logged in (either student or lecturer)
if (!isset($_SESSION['lecturer_id']) && !isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

// Define query based on user type
if (isset($_SESSION['lecturer_id'])) {
    $userID = $_SESSION['lecturer_id'];
    $sql = "SELECT a.appointment_id, a.student_id, s.username AS student_name, 
                   l.username AS lecturer_name, l.phone_number AS lecturer_phone, 
                   a.department, a.appointment_date, 
                   a.time_of_appointment, a.appointment_description 
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id
            JOIN lecturers l ON a.lecturer_id = l.lecturer_id
            WHERE a.lecturer_id = ? 
            AND a.status = 'confirmed'  
            AND a.appointment_date >= CURDATE() 
            ORDER BY a.appointment_date ASC, a.time_of_appointment ASC";
} else {
    $userID = $_SESSION['student_id'];
    $sql = "SELECT a.appointment_id, a.student_id, s.username AS student_name, 
                   l.username AS lecturer_name, l.phone_number AS lecturer_phone, 
                   a.department, a.appointment_date, 
                   a.time_of_appointment, a.appointment_description 
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id
            JOIN lecturers l ON a.lecturer_id = l.lecturer_id
            WHERE a.student_id = ? 
            AND a.status = 'confirmed'  
            AND a.appointment_date >= CURDATE() 
            ORDER BY a.appointment_date ASC, a.time_of_appointment ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>
