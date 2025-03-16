<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
session_regenerate_id(true);

// Log session ID for debugging
error_log("Session ID: " . session_id());

if (!isset($_SESSION['student_id']) || empty($_SESSION['student_id'])) {
    error_log("Session missing or student_id not set: " . print_r($_SESSION, true));
    http_response_code(401);
    echo json_encode(['error' => 'Student ID not found in session. Please log in again.']);
    exit;
}

$studentId = $_SESSION['student_id'];
error_log("Student ID: " . $studentId);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "appointment";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Fetch upcoming appointments
$sql_appointments = "
    SELECT 
        a.appointment_date, 
        a.time_of_appointment, 
        a.Description,
        l.name AS lecturer_name, 
        l.Contact_No, 
        COALESCE(ap.Comments, 'No Comments') AS Comments, 
        COALESCE(ap.status, 'Pending') AS status
    FROM appoint a
    JOIN lecturer l ON a.lecturer_id = l.lecturer_ID
    LEFT JOIN approve ap ON a.Appointment_ID = ap.Appointment_ID
    WHERE a.appointment_date >= CURDATE() 
    AND a.student_id = ?
    ORDER BY a.appointment_date, a.time_of_appointment
    LIMIT 3
";

$stmt_appointments = $conn->prepare($sql_appointments);
if (!$stmt_appointments) {
    error_log("SQL Error: " . $conn->error . " - Query: " . $sql_appointments); // Log query
    http_response_code(500);
    echo json_encode(['error' => 'SQL preparation failed']);
    exit;
}

$stmt_appointments->bind_param("s", $studentId);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();

$upcoming_appointments = [];
while ($row = $result_appointments->fetch_assoc()) {
    $upcoming_appointments[] = $row;
}
$stmt_appointments->close();

function getCount($conn, $sql, $studentId) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("SQL Error: " . $conn->error . " - Query: " . $sql); // Log query
        return 0;
    }
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_row()[0] ?? 0;
    $stmt->close();
    return $count;
}

// Quick Stats
$quick_stats = [
    'upcoming' => getCount($conn, "SELECT COUNT(*) FROM appoint WHERE appointment_date >= CURDATE() AND student_id = ?", $studentId),
    'completed' => getCount($conn, "SELECT COUNT(*) FROM approve ap JOIN appoint a ON ap.Appointment_ID = a.Appointment_ID WHERE ap.status = 'approved' AND a.student_id = ?", $studentId),
    'pending' => getCount($conn, "SELECT COUNT(*) FROM approve ap JOIN appoint a ON ap.Appointment_ID = a.Appointment_ID WHERE ap.status = 'pending' AND a.student_id = ?", $studentId),
    'cancelled' => getCount($conn, "SELECT COUNT(*) FROM approve ap JOIN appoint a ON ap.Appointment_ID = a.Appointment_ID WHERE ap.status = 'rejected' AND a.student_id = ?", $studentId),
];

$conn->close();

header('Content-Type: application/json');

$response = [
    'upcoming_appointments' => $upcoming_appointments,
    'quick_stats' => $quick_stats
];

// Log the JSON response just before sending it
error_log("JSON Response: " . json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>