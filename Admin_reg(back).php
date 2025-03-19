<?php
// Include database connection
include 'db_connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Read and decode JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if JSON decoding failed
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON format.", "raw_data" => $rawData]);
    exit();
}

// Debugging: Log received data
error_log("Received Data: " . print_r($data, true));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Required fields validation
    $requiredFields = ['name', 'admin_ID', 'email', 'contact_no', 'password'];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            echo json_encode(["status" => "error", "message" => "$field is required."]);
            exit();
        }
    }

    $name = trim($data['name']);
    $admin_id = trim($data['admin_ID']);
    $email = trim($data['email']);
    $contact_no = $data['contact_no']; //  keep +254
    $password = trim($data['password']);

    // Validate Name (Only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        echo json_encode(["status" => "error", "message" => "Name should contain only letters and spaces."]);
        exit();
    }

    // Validate Admin ID (Must be an integer)
    if (!filter_var($admin_id, FILTER_VALIDATE_INT)) {
        echo json_encode(["status" => "error", "message" => "Admin ID must be a valid integer."]);
        exit();
    }

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit();
    }

    // Validate Contact Number (Should be +254 followed by 9 digits)
    if (!preg_match("/^\+254\d{9}$/", $contact_no)) {
        echo json_encode(["status" => "error", "message" => "Contact number must be in the format +254 followed by 9 digits."]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicates (email and contact_no only)
    $checkStmt = $conn->prepare("SELECT id FROM admin WHERE email = ? OR contact_no = ?");
    $checkStmt->bind_param("ss", $email, $contact_no); 
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        echo json_encode(["status" => "error", "message" => "Email or contact number already exists."]);
        exit();
    }

    $checkStmt->close();

    // Insert into database 
    $stmt = $conn->prepare("INSERT INTO admin (name, admin_ID, email, contact_no, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $name, $admin_id, $email, $contact_no, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Admin registered successfully.",
            "redirect" => "Admin_log_in.php", // Redirect URL for login page
            "clear_form" => true // Flag to clear form in JS
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register admin."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>