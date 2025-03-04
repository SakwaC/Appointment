<?php
// Include database connection
include 'db_connect.php';

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    if (!isset($data['lecturer_id']) || !isset($data['new_password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    $lecturer_id = trim($data['lecturer_id']);
    $new_password = trim($data['new_password']);

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Check if lecturer exists
    $stmt = $conn->prepare("SELECT * FROM lecturer WHERE Lecturer_ID = ?");
    $stmt->bind_param("s", $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update password
        $update_stmt = $conn->prepare("UPDATE lecturer SET password = ? WHERE Lecturer_ID = ?");
        $update_stmt->bind_param("ss", $hashed_password, $lecturer_id);
        if ($update_stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update password."]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Lecturer ID not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
