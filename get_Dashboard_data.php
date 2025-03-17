<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "appointment";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Start session and get student ID
    session_start();

    if (isset($_SESSION['student_id'])) {
        $studentId = $_SESSION['student_id'];
    } elseif (isset($_GET['student_id'])) {
        // Sanitize input
        $studentId = filter_var($_GET['student_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } else {
        throw new Exception('Student ID not provided.');
    }

    // Ensure student ID is not empty
    if (empty($studentId)) {
        throw new Exception('Invalid Student ID.');
    }

    // Debugging log
    error_log("Debug: Student ID = " . $studentId);

    // Fetch upcoming appointments (only approved and today or future)
    $sqlAppointments = "
        SELECT 
            appoint.Appointment_ID,
            appoint.Description,
            appoint.appointment_date,
            appoint.time_of_appointment,
            appoint.status,
            lecturer.name AS lecturer_name,
            lecturer.Contact_No,
            approve.Comments  -- Fetch Comments from approve table
        FROM appoint
        JOIN lecturer ON appoint.lecturer_id = lecturer.lecturer_ID
        LEFT JOIN approve ON appoint.Appointment_ID = approve.Appointment_ID  -- Ensure we fetch comments
        WHERE appoint.student_id = ? 
        AND appoint.appointment_date >= CURDATE() 
        AND LOWER(appoint.status) = 'approved'
        ORDER BY appoint.appointment_date ASC, appoint.time_of_appointment ASC";

    $stmtAppointments = $conn->prepare($sqlAppointments);
    if (!$stmtAppointments) {
        throw new Exception("Prepare failed (Appointments): " . $conn->error);
    }

    $stmtAppointments->bind_param("s", $studentId);
    if (!$stmtAppointments->execute()) {
        throw new Exception("Appointments Execute failed: " . $stmtAppointments->error);
    }

    $resultAppointments = $stmtAppointments->get_result();
    $appointments = [];

    while ($row = $resultAppointments->fetch_assoc()) {
        $appointments[] = $row;
    }

    // Close first statement properly
    $stmtAppointments->close();

    // Debugging logs
    error_log("Debug: Appointments count = " . count($appointments));

    // Fetch quick stats (all appointments, including past)
    $sqlCounts = "
        SELECT 
            COUNT(CASE WHEN LOWER(status) = 'approved' THEN 1 END) AS upcoming, 
            COUNT(CASE WHEN LOWER(status) = 'pending' THEN 1 END) AS pending, 
            COUNT(CASE WHEN LOWER(status) = 'rejected' THEN 1 END) AS cancelled
        FROM appoint 
        WHERE student_id = ?";

    $stmtCounts = $conn->prepare($sqlCounts);
    if (!$stmtCounts) {
        throw new Exception("Prepare failed (Quick Stats): " . $conn->error);
    }

    $stmtCounts->bind_param("s", $studentId);
    if (!$stmtCounts->execute()) {
        throw new Exception("Counts Execute failed: " . $stmtCounts->error);
    }

    $resultCounts = $stmtCounts->get_result();
    $counts = $resultCounts->fetch_assoc() ?? ['upcoming' => 0, 'pending' => 0, 'cancelled' => 0];

    $stmtCounts->close();

    // Debugging logs
    error_log("Debug: Quick Stats -> Upcoming: {$counts['upcoming']} | Pending: {$counts['pending']} | Cancelled: {$counts['cancelled']}");

    // Prepare and send JSON response
    $response = [
        'appointments' => $appointments,
        'upcomingCount' => $counts['upcoming'] ?? 0,
        'pendingCount' => $counts['pending'] ?? 0,
        'cancelledCount' => $counts['cancelled'] ?? 0,
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

    // Close database connection
    $conn->close();

} catch (Exception $e) {
    // Log the error
    error_log("Error: " . $e->getMessage());

    // Send error response
    $response = ['error' => $e->getMessage()];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
