<?php
// lecturer_dashboard_data.php

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

    // Get lecturer ID from session or request
    session_start();

    if (isset($_SESSION['lecturer_id'])) {
        $lecturerId = $_SESSION['lecturer_id'];
    } elseif (isset($_GET['lecturer_id'])) {
        // Sanitize lecturer_id
        $lecturerId = filter_var($_GET['lecturer_id'], FILTER_SANITIZE_NUMBER_INT);
        if ($lecturerId === false) {
            throw new Exception('Invalid Lecturer ID.');
        }
    } else {
        throw new Exception('Lecturer ID not provided.');
    }

  // Fetch upcoming appointments (only approved and today or future)
$sqlAppointments = "
SELECT 
    appoint.Appointment_ID,
    appoint.Description,
    DATE(approve.Date_Approved) AS appointment_date,  -- Format to show only the date
    appoint.time_of_appointment,
    appoint.status,
    students.Name,
    students.Contact_No,
    appoint.Description
FROM appoint
JOIN students ON appoint.student_id = students.student_id
JOIN approve ON appoint.Appointment_ID = approve.Appointment_ID
WHERE appoint.lecturer_id = ? 
AND approve.Date_Approved >= CURDATE()
AND appoint.status = 'approved'
ORDER BY approve.Date_Approved ASC, appoint.time_of_appointment ASC";


    $stmtAppointments = $conn->prepare($sqlAppointments);
    if (!$stmtAppointments) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmtAppointments->bind_param("i", $lecturerId);
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

    // Fetch quick stats (all appointments, including past)
    $sqlCounts = "SELECT 
                        COUNT(CASE WHEN status = 'approved' THEN 1 END) AS upcoming, 
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending, 
                        COUNT(CASE WHEN status = 'rejected' THEN 1 END) AS cancelled
                    FROM appoint 
                    WHERE lecturer_id = ?";

    $stmtCounts = $conn->prepare($sqlCounts);
    if (!$stmtCounts) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmtCounts->bind_param("i", $lecturerId);
    if (!$stmtCounts->execute()) {
        throw new Exception("Counts Execute failed: " . $stmtCounts->error);
    }

    $resultCounts = $stmtCounts->get_result();
    $counts = $resultCounts->fetch_assoc();

    $stmtCounts->close();

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
    // Handle exceptions
    $response = ['error' => $e->getMessage()];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>