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
    if (isset($_GET['debug'])) { // Conditional debug output
        var_dump($_SESSION);
    }
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

    // Fetch upcoming appointments
    $sqlAppointments = "SELECT 
                            appoint.Appointment_ID,
                            appoint.Description,
                            appoint.appointment_date,
                            appoint.time_of_appointment,
                            appoint.status,
                            students.Name,
                            students.Contact_No,
                            appoint.Description
                        FROM appoint
                        JOIN students ON appoint.student_id = students.student_id
                        WHERE appoint.lecturer_id = ? AND appoint.appointment_date >= CURDATE()
                        ORDER BY appoint.appointment_date ASC, appoint.time_of_appointment ASC";

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

    if ($resultAppointments->num_rows > 0) {
        while ($row = $resultAppointments->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
    $stmtAppointments->close();

    // Fetch quick stats
    $sqlUpcomingCount = "SELECT COUNT(*) as upcoming FROM appoint WHERE lecturer_id = ? AND appointment_date >= CURDATE() AND status = 'approved'";
    $sqlPendingCount = "SELECT COUNT(*) as pending FROM appoint WHERE lecturer_id = ? AND appointment_date >= CURDATE() AND status = 'pending'";
    $sqlCancelledCount = "SELECT COUNT(*) as cancelled FROM appoint WHERE lecturer_id = ? AND appointment_date >= CURDATE() AND status = 'rejected'";

    $stmtUpcomingCount = $conn->prepare($sqlUpcomingCount);
    $stmtPendingCount = $conn->prepare($sqlPendingCount);
    $stmtCancelledCount = $conn->prepare($sqlCancelledCount);

    if (!$stmtUpcomingCount || !$stmtPendingCount || !$stmtCancelledCount) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmtUpcomingCount->bind_param("i", $lecturerId);
    $stmtPendingCount->bind_param("i", $lecturerId);
    $stmtCancelledCount->bind_param("i", $lecturerId);

    if (!$stmtUpcomingCount->execute()) {
        throw new Exception("Upcoming Count Execute failed: " . $stmtUpcomingCount->error);
    }
    if (!$stmtPendingCount->execute()) {
        throw new Exception("Pending Count Execute failed: " . $stmtPendingCount->error);
    }
    if (!$stmtCancelledCount->execute()) {
        throw new Exception("Cancelled Count Execute failed: " . $stmtCancelledCount->error);
    }

    $resultUpcomingCount = $stmtUpcomingCount->get_result();
    $resultPendingCount = $stmtPendingCount->get_result();
    $resultCancelledCount = $stmtCancelledCount->get_result();

    $upcomingCount = $resultUpcomingCount->fetch_assoc()['upcoming'];
    $pendingCount = $resultPendingCount->fetch_assoc()['pending'];
    $cancelledCount = $resultCancelledCount->fetch_assoc()['cancelled'];

    $stmtUpcomingCount->close();
    $stmtPendingCount->close();
    $stmtCancelledCount->close();

    // Prepare and send JSON response
    $response = [
        'appointments' => $appointments,
        'upcomingCount' => $upcomingCount,
        'pendingCount' => $pendingCount,
        'cancelledCount' => $cancelledCount,
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

    $conn->close();

} catch (Exception $e) {
    // Handle exceptions
    $response = ['error' => $e->getMessage()];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>