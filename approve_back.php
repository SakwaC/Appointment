<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response headers for JSON format
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "appointment";

$conn = new mysqli($host, $username, $password, $database);

// Check for database connection errors
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

// Check if form data is received via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Debugging: Log received POST data
    file_put_contents("debug_log.txt", print_r($_POST, true));

    // Validate input
    $appointment_id = $_POST["appointment_id"] ?? ''; // Ensure it's a string
    $comments = trim($_POST["comments"] ?? '');
    $status = trim($_POST["status"] ?? '');
    $date_approved = date('Y-m-d'); // Store only today or future dates

    // Ensure valid input
    if (empty($appointment_id) || empty($comments) || empty($status)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // ** Fix: Ensure Appointment_ID is stored as a string **
    $appointment_id = strval($appointment_id);

    // ** Update the `appoint` table to set the status **
    $update_stmt = $conn->prepare("UPDATE appoint SET status = ? WHERE Appointment_ID = ?");
    if (!$update_stmt) {
        echo json_encode(["status" => "error", "message" => "SQL prepare failed: " . $conn->error]);
        exit();
    }
    $update_stmt->bind_param("ss", $status, $appointment_id);
    if (!$update_stmt->execute()) {
        echo json_encode(["status" => "failure", "message" => "Failed to update appointment."]);
        exit();
    }
    $update_stmt->close();

    // ** Insert into `approve` table **
    $insert_stmt = $conn->prepare("INSERT INTO approve (Appointment_ID, status, Comments, Date_Approved) VALUES (?, ?, ?, ?)");
    if (!$insert_stmt) {
        echo json_encode(["status" => "error", "message" => "SQL prepare failed: " . $conn->error]);
        exit();
    }

    // Bind as a string
    $insert_stmt->bind_param("ssss", $appointment_id, $status, $comments, $date_approved);

    if ($insert_stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Database error: ' . $conn->error]);
    }

    $insert_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

// Close database connection
$conn->close();
?>
