<?php
session_start();

$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? null;
$lecturerId = $_SERVER['HTTP_X_LECTURER_ID'] ?? null;

if ($sessionId && $lecturerId) {
    $_SESSION['lecturer_id'] = $lecturerId;
    $_SESSION['is_authenticated'] = true;
}

require 'db_connection.php';

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
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Session-ID, X-Lecturer-ID");
} else {
    // Optionally, handle invalid origin (e.g., log, send error)
    // header("HTTP/1.1 403 Forbidden");
    // exit;
}

header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Lecturer not logged in"]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get the unique `id` from the lecturer table
$lecturer_query = "SELECT id FROM lecturer WHERE lecturer_id = ?";
$stmt = $conn->prepare($lecturer_query);
$stmt->bind_param("s", $lecturerID);
$stmt->execute();
$lecturer_result = $stmt->get_result();

if ($lecturer_result && $lecturer_result->num_rows > 0) {
    $lecturer_row = $lecturer_result->fetch_assoc();
    $uniqueLecturerID = $lecturer_row['id'];

    // Fetch appointments including status
    $sql = "SELECT a.*, s.Student_ID, s.Name as student_name 
            FROM appoint a
            LEFT JOIN students s ON a.student_id = s.Student_ID
            WHERE a.lecturer_id = ? AND a.status = 'Pending'
            ORDER BY a.appointment_date ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "Failed to prepare SQL", "details" => $conn->error]);
        exit;
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lecturerID);
    $stmt->execute();
    $result = $stmt->get_result();

    $appoint = [];
    while ($row = $result->fetch_assoc()) {
        $appoint[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(empty($appoint) ? ["message" => "No Pending appointments"] : $appoint);
} else {
    echo json_encode(["error" => "Lecturer not found"]);
    exit;
}
?>