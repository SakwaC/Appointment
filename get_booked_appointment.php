<?php
session_start();

$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? null;
$lecturerId = $_SERVER['HTTP_X_LECTURER_ID'] ?? null;

if ($sessionId && $lecturerId) {
    $_SESSION['lecturer_id'] = $lecturerId;
    $_SESSION['is_authenticated'] = true;
}

require 'db_connection.php';

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Session-ID, X-Lecturer-ID");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if lecturer is logged in
error_log('Current session data: ' . print_r($_SESSION, true));

if (!isset($_SESSION['lecturer_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Lecturer not logged in", "session_data" => $_SESSION]);
    exit;
}

$lecturerID = $_SESSION['lecturer_id'];

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get lecturer ID from lecturer table
$lecturer_query = "SELECT id FROM lecturer WHERE lecturer_id = '1010'";
$lecturer_result = $conn->query($lecturer_query);

if ($lecturer_result && $lecturer_result->num_rows > 0) {
    $lecturer_row = $lecturer_result->fetch_assoc();
    $lecturerID = $lecturer_row['id'];

    // Query the booked appointments
    $sql = "SELECT 
        a.Appointment_ID,
        a.student_id,
        s.name as student_name,  
        a.department,
        a.appointment_date,
        a.time_of_appointment,
        a.Description
        FROM appoint a
        LEFT JOIN students s ON a.student_id = s.Student_ID
        WHERE a.lecturer_id = ?
        ORDER BY a.appointment_date ASC";

    error_log("Lecturer ID being queried: " . $lecturerID);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare Error: " . $conn->error);
        echo json_encode([
            "error" => "Failed to prepare SQL statement",
            "details" => $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("i", $lecturerID);
    $stmt->execute();
    $result = $stmt->get_result();

    $appoint = [];
    while ($row = $result->fetch_assoc()) {
        $appoint[] = $row;
    }

    $stmt->close();
    $conn->close();

    if (empty($appoint)) {
        echo json_encode(["message" => "No booked appointments"]);
    } else {
        echo json_encode($appoint);
    }
} else {
    echo json_encode(["error" => "Lecturer not found"]);
    exit;
}
?>
