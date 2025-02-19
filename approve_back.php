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
    // Validate input
    $appointment_id = isset($_POST["appointment_id"]) ? intval($_POST["appointment_id"]) : 0;
    $comments = htmlspecialchars(trim($_POST["comments"] ?? ''));
    $status = htmlspecialchars(trim($_POST["status"] ?? ''));
    $date_approved = date('Y-m-d H:i:s'); // Auto-generate timestamp

    if ($appointment_id === 0 || empty($comments) || empty($status)) {
        echo json_encode(["status" => "error", "message" => "All fields are required and Appointment ID must be valid."]);
        exit();
    }

    // Insert data into `approve` table
    $stmt = $conn->prepare("INSERT INTO approve (Appointment_ID, status, Comments, Date_Approved) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL prepare failed: " . $conn->error]);
        exit();
    }

    // Bind and execute the statement
    $stmt->bind_param("isss", $appointment_id, $status, $comments, $date_approved);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failure']);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

// Close database connection
$conn->close();
?>
