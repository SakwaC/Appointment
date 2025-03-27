<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = "localhost";
$username = "root";
$password = "";
$database = "appointment";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST["student_id"] ?? '';
    $department = $_POST["department"] ?? '';
    $lecturer = $_POST["lecturer"] ?? '';
    $appointment_date = $_POST["appointment_date"] ?? '';
    $time_of_appointment = $_POST["time_of_appointment"] ?? '';
    $appointment_description = $_POST["appointment_description"] ?? '';

    error_log("Received POST data: " . print_r($_POST, true));
    error_log("Student ID received: " . $student_id);

    if (empty($student_id) || empty($department) || empty($lecturer) || empty($appointment_date) || empty($time_of_appointment) || empty($appointment_description)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    if (strtotime($appointment_date) < strtotime(date("Y-m-d"))) {
        echo json_encode(["status" => "error", "message" => "Appointment date cannot be in the past."]);
        exit();
    }

    $date_part = date("Ymd");
    $result = $conn->query("SELECT MAX(SUBSTRING(Appointment_ID, -4)) as max_num FROM appoint WHERE Appointment_ID LIKE 'APPT-$date_part-%'");
    $row = $result->fetch_assoc();
    $next_num = ($row['max_num'] ? intval($row['max_num']) + 1 : 1);
    $appointment_id = "APPT-$date_part-" . str_pad($next_num, 4, "0", STR_PAD_LEFT);
    while ($conn->query("SELECT 1 FROM appoint WHERE Appointment_ID = '$appointment_id'")->num_rows > 0) {
        $next_num++;
        $appointment_id = "APPT-$date_part-" . str_pad($next_num, 4, "0", STR_PAD_LEFT);
    }

    // Check for duplicate appointments (same student, lecturer, date, and time)
    $stmt_check_duplicate = $conn->prepare("SELECT 1 FROM appoint WHERE Lecturer_ID = ? AND Appointment_Date = ? AND time_of_appointment = ? AND student_id = ?");
    $stmt_check_duplicate->bind_param("ssss", $lecturer, $appointment_date, $time_of_appointment, $student_id);
    $stmt_check_duplicate->execute();
    $stmt_check_duplicate->store_result();

    if ($stmt_check_duplicate->num_rows > 0) {
        $stmt_check_duplicate->close();
        echo json_encode(["status" => "error", "message" => "You already have an appointment with this lecturer at this time on this date."]);
        exit();
    }
    $stmt_check_duplicate->close();

    $stmt = $conn->prepare("INSERT INTO appoint (Appointment_ID, student_id, Department, Lecturer_ID, Appointment_Date, time_of_appointment, Description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]));
    }

    $stmt->bind_param("sssssss", $appointment_id, $student_id, $department, $lecturer, $appointment_date, $time_of_appointment, $appointment_description);

    if ($stmt->execute()) {
        error_log("Appointment Created Successfully. Appointment ID: " . $appointment_id);
    
        // Get the day of the week for the appointment
        $day_of_week = date("l", strtotime($appointment_date)); // e.g., "Monday"
    
        // Update the lecturer's schedule start_time by adding 20 minutes
        $update_schedule_query = "
            UPDATE lecturer_schedule 
            SET start_time = ADDTIME(start_time, '00:30') 
            WHERE lecturer_id = ? AND days = ?";
        
        $stmt_update_schedule = $conn->prepare($update_schedule_query);
        if ($stmt_update_schedule) {
            $stmt_update_schedule->bind_param("ss", $lecturer, $day_of_week);
            if ($stmt_update_schedule->execute()) {
                error_log("Lecturer schedule updated successfully for Lecturer_ID: " . $lecturer . " on " . $day_of_week);
            } else {
                error_log("Error updating lecturer schedule: " . $stmt_update_schedule->error);
            }
            $stmt_update_schedule->close();
        } else {
            error_log("Error preparing lecturer schedule update query: " . $conn->error);
        }
    
        echo json_encode([
            "status" => "success",
            "message" => "Appointment created successfully",
            "appointment_id" => $appointment_id
        ]);
    } else {
        error_log("Database Error (Insert Execute): " . $stmt->error);
        echo json_encode([
            "status" => "error",
            "message" => "Failed to create appointment",
            "error_details" => $stmt->error,
            "sql_error" => $conn->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>