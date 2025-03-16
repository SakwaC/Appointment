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
    // Enhanced Validation and Logging
    if (!$data) {
        error_log("JSON decoding failed.");
        echo json_encode(["status" => "error", "message" => "Invalid JSON data."]);
        exit();
    }

    if (!isset($data['admin_ID']) || !isset($data['new_password']) || empty($data['admin_ID']) || empty($data['new_password'])) {
        error_log("Missing or empty fields: admin_ID=" . (isset($data['admin_ID']) ? $data['admin_ID'] : 'null') . ", new_password=" . (isset($data['new_password']) ? $data['new_password'] : 'null'));
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    $admin_id = trim($data['admin_ID']);
    $new_password = trim($data['new_password']);

    // Debugging: Log received values
    error_log("admin_ID received: " . $admin_id);

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    try {
        // Check if admin exists
        $sql_select = "SELECT * FROM admin WHERE admin_ID = ?";
        error_log("Executing SELECT query: " . $sql_select . " with admin_ID: " . $admin_id);
        $stmt = $conn->prepare($sql_select);
        if (!$stmt) {
            throw new Exception("Prepare failed (SELECT): " . $conn->error);
        }
        $stmt->bind_param("s", $admin_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed (SELECT): " . $stmt->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password
            $sql_update = "UPDATE admin SET password = ? WHERE admin_ID = ?";
            error_log("Executing UPDATE query: " . $sql_update . " with admin_ID: " . $admin_id);
            $update_stmt = $conn->prepare($sql_update);
            if (!$update_stmt) {
                throw new Exception("Prepare failed (UPDATE): " . $conn->error);
            }
            $update_stmt->bind_param("ss", $hashed_password, $admin_id);
            if (!$update_stmt->execute()) {
                throw new Exception("Execute failed (UPDATE): " . $update_stmt->error);
            }
            echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
            $update_stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Admin ID not found."]);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>