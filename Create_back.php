<?php
session_start(); // Start the session to store student_id

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization'); 

// Database connection
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "appointment"; 

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve student_id from session if available
    $student_id = $_SESSION["student_id"] ?? $_POST["student_id"] ?? ''; 
    $department = $_POST["department"] ?? '';
    $lecturer = $_POST["lecturer"] ?? '';
    $appointment_date = $_POST["appointment_date"] ?? '';
    $time_of_appointment = $_POST["time_of_appointment"] ?? '';
    $appointment_description = $_POST["appointment_description"] ?? '';

    // Log the student_id for debugging
    error_log("Student ID: " . $student_id);

    // Validate inputs
    if (empty($student_id) || empty($department) || empty($lecturer) || empty($appointment_date) || empty($time_of_appointment) || empty($appointment_description)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // Ensure date is not in the past
    if (strtotime($appointment_date) < strtotime(date("Y-m-d"))) {
        echo json_encode(["status" => "error", "message" => "Appointment date cannot be in the past."]);
        exit();
    }

    // Generate a unique Appointment_ID in format APPT-YYYYMMDD-XXXX
    $date_part = date("Ymd"); // Get current date as YYYYMMDD
    $result = $conn->query("SELECT COUNT(*) as total FROM appoint WHERE Appointment_Date = '$appointment_date'");
    $row = $result->fetch_assoc();
    $count = $row['total'] + 1; // Increment count for uniqueness

    $appointment_id = "APPT-$date_part-" . str_pad($count, 4, "0", STR_PAD_LEFT); // Format APPT-YYYYMMDD-XXXX

    // Insert appointment data with the generated Appointment_ID
    $stmt = $conn->prepare("INSERT INTO appoint (Appointment_ID, student_id, Department, Lecturer_ID, Appointment_Date, time_of_appointment, Description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]));
    }

    $stmt->bind_param("sisisss", $appointment_id, $student_id, $department, $lecturer, $appointment_date, $time_of_appointment, $appointment_description);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment created successfully", "appointment_id" => $appointment_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create appointment"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
