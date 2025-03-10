<?php
header("Access-Control-Allow-Origin: *"); // Allows requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific request methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow certain headers
header("Content-Type: application/json");

include 'db_connection.php'; // Ensure database connection is included

// Check if the connection is successful
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    http_response_code(500);
    exit();
}

// Fetch the maximum appointment ID from the table
$sql = "SELECT MAX(id) AS max_id FROM appoint";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    http_response_code(500);
    exit();
}

$row = $result->fetch_assoc();
$newId = "APT-" . str_pad(($row['max_id'] ?? 0) + 1, 6, "0", STR_PAD_LEFT);

// Return the new appointment ID in JSON format
echo json_encode(["id" => $newId]);
?>
