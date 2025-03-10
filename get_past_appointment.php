<?php
session_start();
require 'db_connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$studentID = $_SESSION['student_id'];

$sql = "SELECT a.appointment_id, a.lecturer_id, l.name AS lecturer_name, 
               l.contact AS lecturer_contact, a.department, 
               a.appointment_date, a.time_of_appointment, a.appointment_description 
        FROM appointments a
        JOIN lecturers l ON a.lecturer_id = l.lecturer_id
        WHERE a.student_id = ? AND a.appointment_date < CURDATE()
        ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>
