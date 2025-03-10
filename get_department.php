<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_connection.php';

// Check if database connection is successful
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    http_response_code(500);
    exit();
}

// Fetch distinct departments from the lecturer table
$sql = "SELECT DISTINCT department FROM lecturer";
$result = $conn->query($sql);

// Check if query execution was successful
if (!$result) {
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

// Check if any results exist
if ($result->num_rows === 0) {
    echo json_encode(["error" => "No departments found"]);
    exit();
}

// Fetch data and return as JSON
$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = ["department" => $row['department']];
}

// Return JSON response
echo json_encode($departments);
?>
