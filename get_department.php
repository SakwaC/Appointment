<?php
// Allow requests from any origin (for development only)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include the database connection
include 'db_connection.php';

// Fetch departments
$sql = "SELECT id, name FROM department";
$result = $conn->query($sql);

// Check if query was successful
if (!$result) {
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

// Fetch data and return as JSON
$department = [];
while ($row = $result->fetch_assoc()) {
    $department[] = $row;
}

echo json_encode($department);
?>

