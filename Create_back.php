<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure the response is in JSON format
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow specific HTTP methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow specific headers


// Database connection
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "appointment"; 

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form inputs
    $appointment_id = $_POST["appointment_id"] ?? '';
    $appointment_date = $_POST["appointment_date"] ?? '';
    $appointment_description = $_POST["appointment_description"] ?? '';

    // Validate input
    if (empty($appointment_id) || empty($appointment_date) || empty($appointment_description)) {
        die("Error: All fields are required.");
    }

    // Debugging: Check database connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO appoint (Appointment_ID, appointment_date, Description) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        die("Error in SQL statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("iss", $appointment_id, $appointment_date, $appointment_description);

    // Execute query and check for errors
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failure']);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

// Close connection
$conn->close();
?>
