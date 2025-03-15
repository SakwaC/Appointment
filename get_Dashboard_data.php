<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "appointment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Upcoming Appointments (Using JOINs) - Modified to filter past appointments
$sql_appointments = "
    SELECT 
        a.appointment_date, 
        a.time_of_appointment, 
        a.Description,
        l.name AS lecturer_name,
        l.Contact_No,
        ap.Comments,
        ap.status
    FROM appoint a
    JOIN lecturer l ON a.lecturer_id = l.lecturer_ID
    JOIN approve ap ON a.Appointment_ID = ap.Appointment_ID
    WHERE a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date, a.time_of_appointment
    LIMIT 5
";

$result_appointments = $conn->query($sql_appointments);

// Check if query execution was successful
if (!$result_appointments) {
    echo json_encode(['error' => 'SQL Error: ' . $conn->error]);
    exit;
}

// Fetch results into an array
$upcoming_appointments = [];
while ($row = $result_appointments->fetch_assoc()) {
    $upcoming_appointments[] = $row;
}

// Quick Stats (from 'approved' table) - Modified to reflect current date
$sql_upcoming_count = "SELECT COUNT(*) FROM appoint WHERE appointment_date >= CURDATE()";
$sql_completed_count = "SELECT COUNT(*) FROM approve WHERE status = 'approved'";
$sql_pending_count = "SELECT COUNT(*) FROM approve WHERE status = 'pending'";
$sql_cancelled_count = "SELECT COUNT(*) FROM approve WHERE status = 'rejected'";

$upcoming_count = $conn->query($sql_upcoming_count);
$completed_count = $conn->query($sql_completed_count);
$pending_count = $conn->query($sql_pending_count);
$cancelled_count = $conn->query($sql_cancelled_count);

// Ensure valid counts and handle query errors
$upcoming_count = ($upcoming_count && $upcoming_count->num_rows > 0) ? $upcoming_count->fetch_row()[0] : 0;
$completed_count = ($completed_count && $completed_count->num_rows > 0) ? $completed_count->fetch_row()[0] : 0;
$pending_count = ($pending_count && $pending_count->num_rows > 0) ? $pending_count->fetch_row()[0] : 0;
$cancelled_count = ($cancelled_count && $cancelled_count->num_rows > 0) ? $cancelled_count->fetch_row()[0] : 0;

// Prepare JSON output
$data = [
    'upcoming_appointments' => $upcoming_appointments,
    'quick_stats' => [
        'upcoming' => $upcoming_count,
        'completed' => $completed_count,
        'pending' => $pending_count,
        'cancelled' => $cancelled_count,
    ]
];

// Set correct headers for JSON output
header('Content-Type: application/json');
echo json_encode($data);

// Close database connection
$conn->close();
?>