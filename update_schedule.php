<?php
// Get the origin of the request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// List of allowed origins
$allowed_origins = [
    'http://127.0.0.1:5500',
    'http://localhost:3000', // Add your desired origins here
    // Add any other origins you want to allow
];

// Check if the origin is in the allowed list
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $origin);
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit;
    }
} else {
    // Optionally handle requests from disallowed origins
    http_response_code(403);
    echo json_encode(['error' => 'Origin not allowed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

if (!isset($_SERVER['HTTP_X_LECTURER_ID']) || !isset($_SERVER['HTTP_X_SESSION_ID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$lecturerId = $_SERVER['HTTP_X_LECTURER_ID'];
$sessionId = $_SERVER['HTTP_X_SESSION_ID'];

if (isset($_POST['schedule_id']) && isset($_POST['field']) && isset($_POST['value'])) {
    $scheduleId = $_POST['schedule_id'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    $validFields = ['start_time', 'end_time', 'meeting_duration'];
    if (!in_array($field, $validFields)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid field']);
        exit;
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "appointment";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        http_response_code(500);
        error_log("Database connection failed: " . $conn->connect_error);
        echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $sql = "UPDATE lecturer_schedule SET $field = ? WHERE id = ? AND lecturer_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sii", $value, $scheduleId, $lecturerId);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Schedule updated successfully']);
        } else {
            http_response_code(500);
            error_log("SQL Error: " . $stmt->error);
            echo json_encode(['error' => 'Error updating schedule: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(500);
        error_log("Prepare Error: " . $conn->error);
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }

    $conn->close();
} else {
    http_response_code(400);
    error_log("Missing Parameters: " . print_r($_POST, true));
    echo json_encode(['error' => 'Missing parameters']);
}

error_log("POST Data: " . print_r($_POST, true));
error_log("Lecturer ID: " . $lecturerId);
error_log("Schedule ID: " . $scheduleId);
error_log("Field: " . $field);
error_log("Value: " . $value);
error_log("SQL Query: " . $sql);
?>