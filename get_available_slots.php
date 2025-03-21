<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = "localhost";
$username = "root";
$password = "";
$database = "appointment";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(["error" => "Database connection failed"]));
}

if (isset($_GET['lecturer_ID']) && isset($_GET['appointment_date'])) {
    $lecturer_id = $_GET['lecturer_ID'];
    $appointment_date = $_GET['appointment_date'];
    $day_of_week = date('l', strtotime($appointment_date));

    error_log("Received lecturer_ID: " . $lecturer_id . ", appointment_date: " . $appointment_date);

    $stmt_schedule = $conn->prepare("SELECT start_time, end_time, meeting_duration FROM lecturer_schedule WHERE lecturer_ID = ? AND days = ?");
    if (!$stmt_schedule) {
        error_log("Prepare statement failed: " . $conn->error);
        die(json_encode(['error' => 'Database error']));
    }
    $stmt_schedule->bind_param("ss", $lecturer_id, $day_of_week);
    $stmt_schedule->execute();
    $result_schedule = $stmt_schedule->get_result();
    $schedule_row = $result_schedule->fetch_assoc();
    $stmt_schedule->close();

    if ($schedule_row) {
        $start_time_str = $schedule_row['start_time'];
        $end_time_str = $schedule_row['end_time'];
        $meeting_duration = $schedule_row['meeting_duration'];

        $start_time = strtotime($start_time_str);
        $end_time = strtotime($end_time_str);

        $available_slots = [];
        $current_time = $start_time;

        while ($current_time <= ($end_time - ($meeting_duration * 60))) {
            $slot_time = date('H:i', $current_time);
            $stmt_check = $conn->prepare("SELECT 1 FROM appoint WHERE Lecturer_ID = ? AND Appointment_Date = ? AND time_of_appointment = ?");
            if (!$stmt_check) {
                error_log("Prepare statement failed: " . $conn->error);
                die(json_encode(['error' => 'Database error']));
            }
            $stmt_check->bind_param("sss", $lecturer_id, $appointment_date, $slot_time);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows === 0) {
                $available_slots[] = $slot_time;
            }
            $stmt_check->close();
            $current_time += $meeting_duration * 60;
        }
        echo json_encode(['time_slots' => $available_slots]);
    } else {
        error_log("Schedule not found for lecturer_ID: " . $lecturer_id . ", day: " . $day_of_week);
        echo json_encode(['time_slots' => []]);
    }
} else {
    error_log("Invalid request: lecturer_ID or appointment_date missing");
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>