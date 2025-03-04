<?php
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
    $department = $_POST["department"] ?? '';
    $lecturer = $_POST["lecturer"] ?? '';
    $appointment_date = $_POST["appointment_date"] ?? '';
    $appointment_time = $_POST["appointment_time"] ?? '';
    $appointment_description = $_POST["appointment_description"] ?? '';

    // Validate inputs
    if (empty($department) || empty($lecturer) || empty($appointment_date) || empty($appointment_time) || empty($appointment_description)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // Ensure date is not in the past
    if (strtotime($appointment_date) < strtotime(date("Y-m-d"))) {
        echo json_encode(["status" => "error", "message" => "Appointment date cannot be in the past."]);
        exit();
    }

    // Insert appointment data
    $stmt = $conn->prepare("INSERT INTO appoint (Department, Lecturer_ID, Appointment_Date, Appointment_Time, Description) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]));
    }

    $stmt->bind_param("sisss", $department, $lecturer, $appointment_date, $appointment_time, $appointment_description);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create appointment"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
