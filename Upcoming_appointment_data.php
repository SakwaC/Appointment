<?php
// upcoming_appointments.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "appointment";

try {
    // Start session
    session_start();

    // Check if lecturer ID is provided via GET or SESSION
    if (isset($_GET['lecturer_id'])) {
        $lecturerId = $_GET['lecturer_id'];
    } elseif (isset($_SESSION['lecturer_id'])) {
        $lecturerId = $_SESSION['lecturer_id'];
    } else {
        throw new Exception("Lecturer ID not provided.");
    }

    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Fetch upcoming approved appointments
    $sqlAppointments = "SELECT 
                            students.Name AS student_name,
                            appoint.appointment_date,
                            appoint.time_of_appointment,
                            appoint.Description
                        FROM appoint
                        JOIN students ON appoint.student_id = students.student_id
                        WHERE appoint.lecturer_id = ? AND appoint.appointment_date >= CURDATE() AND appoint.status = 'approved'
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

    while ($row = $resultAppointments->fetch_assoc()) {
        $appointments[] = $row;
    }

    $stmtAppointments->close();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($appointments);

    // Close database connection
    $conn->close();

} catch (Exception $e) {
    // Handle errors and send JSON response
    $response = ['error' => $e->getMessage()];
    header('Content-Type: application/json');
    echo json_encode($response);

    // Log the error (optional, but recommended in production)
    error_log("Error in upcoming_appointments.php: " . $e->getMessage()); // log error
}
?>