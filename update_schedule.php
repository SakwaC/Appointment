<?php
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Lecturer-ID, X-Session-ID');
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
        error_log("Database connection failed: " . $conn->connect_error); // Log connection error
        echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $sql = "UPDATE lecturer_schedule SET $field = ? WHERE id = ? AND lecturer_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($field == "meeting_duration") {
            $stmt->bind_param("sii", $value, $scheduleId, $lecturerId);
        } else {
            $stmt->bind_param("sii", $value, $scheduleId, $lecturerId);
        }

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Schedule updated successfully']);
        } else {
            http_response_code(500);
            error_log("SQL Error: " . $stmt->error); // Log SQL error
            echo json_encode(['error' => 'Error updating schedule: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(500);
        error_log("Prepare Error: " . $conn->error); // Log prepare error
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }

    $conn->close();
} else {
    http_response_code(400);
    error_log("Missing Parameters: " . print_r($_POST, true)); //log post variables.
    echo json_encode(['error' => 'Missing parameters']);
}

error_log("POST Data: " . print_r($_POST, true)); // Log POST data
error_log("Lecturer ID: " . $lecturerId); //log lecturer ID
error_log("Schedule ID: " . $scheduleId); //log schedule ID
error_log("Field: " . $field); //log field
error_log("Value: " . $value); //log Value
error_log("SQL Query: " . $sql); //log sql Query

?>