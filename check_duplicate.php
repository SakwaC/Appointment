<?php
header("Content-Type: application/json"); // Ensure JSON response

// Allow CORS for specific origins
$allowedOrigins = ["http://localhost:3000", "http://localhost:5500"];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

require_once 'db_connection.php'; // Ensure DB connection is working

// Fetch parameters safely
$student_id = $_GET['student_id'] ?? '';
$appointment_date = $_GET['appointment_date'] ?? '';
$lecturer_ID = $_GET['lecturer_ID'] ?? '';
$appointment_time = $_GET['appointment_time'] ?? ''; // This should match `time_of_appointment`

// Validate input
if (!$student_id || !$appointment_date || !$lecturer_ID || !$appointment_time) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit();
}

// Prepare SQL statement with the correct column name
$sql = "SELECT COUNT(*) as count FROM appoint WHERE student_id = ? AND appointment_date = ? AND lecturer_ID = ? AND time_of_appointment = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit();
}

// Bind parameters with correct types
$stmt->bind_param("sssi", $student_id, $appointment_date, $lecturer_ID, $appointment_time);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Ensure valid JSON response
if ($row) {
    echo json_encode(["exists" => $row['count'] > 0]);
} else {
    echo json_encode(["error" => "Query failed"]);
}

// Clean up
$stmt->close();
$conn->close();
?>
