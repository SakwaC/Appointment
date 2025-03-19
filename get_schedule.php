<?php

// Define allowed origins
$allowedOrigins = [
    'http://localhost:3000',
    'http://127.0.0.1:5500',
    
];

// Get the origin from the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;

// Check if the origin is allowed
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID');
    header('Access-Control-Allow-Methods: GET, OPTIONS');

} else {
    // Optionally, handle invalid origin (e.g., log, send error)
    // header("HTTP/1.1 403 Forbidden");
    // exit;
}

header('Content-Type: application/json');

// Include database connection file 
include 'db_connection.php';

// Authenticate lecturer session
$lecturer_id = isset($_GET['lecturer_id']) ? $_GET['lecturer_id'] : null;
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;

if (!$lecturer_id || !$session_id) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Unauthorized request"]);
    exit();
}

try {
    
    $sql = "SELECT id, days, start_time, end_time, meeting_duration FROM lecturer_schedule WHERE lecturer_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $stmt->bind_param("i", $lecturer_id);

    if (!$stmt->execute()) {
        throw new Exception("Database execute error: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Database result error: " . $stmt->error);
    }

    $schedule = [];
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(["data" => $schedule]);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("get_schedule.php error: " . $e->getMessage()); // Log error
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}

?>