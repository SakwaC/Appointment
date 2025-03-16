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

// Debugging logs
error_log("Raw POST Data: " . $rawData);
error_log("Decoded JSON Data: " . json_encode($data));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    if (!$data || empty($data['Student_ID']) || empty($data['new_password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    $student_id = trim($data['Student_ID']);
    $new_password = trim($data['new_password']);

    // Debugging: Log received values
    error_log("Student_ID received: " . $student_id);

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Check if student exists
    $stmt = $conn->prepare("SELECT * FROM students WHERE Student_ID = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update password
        $update_stmt = $conn->prepare("UPDATE students SET password = ? WHERE Student_ID = ?");
        $update_stmt->bind_param("ss", $hashed_password, $student_id);
        
        if ($update_stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update password."]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Student ID not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
