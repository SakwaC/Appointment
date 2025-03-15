<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'db2_connect.php';

// Check database connection
if (!$conn) {
    echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
    exit();
}

$query = "SELECT days, start_time, end_time FROM lecturer_schedule";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$scheduleData = [];

while ($row = $result->fetch_assoc()) {
    $scheduleData[] = $row;
}

echo json_encode($scheduleData);
?>
