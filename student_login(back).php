<?php
session_start();
header("Content-Type: application/json");
error_log("Session ID (at top of " . basename(__FILE__) . "): " . session_id());

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit();
}

// Get form data directly
$studentID = isset($_POST['student_id']) ? trim($_POST['student_id']) : "";
$password = isset($_POST['password']) ? trim($_POST['password']) : "";

if (empty($studentID) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing student_id or Password."]);
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$db_password = "";
$database = "appointment";

$conn = new mysqli($host, $username, $db_password, $database);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Fetch user data
$sql = "SELECT student_id, password FROM students WHERE LOWER(student_id) = LOWER(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $hashedPassword = $user['password'];

    // Rehash password only if it's in plain text (legacy case)
    if (!password_get_info($hashedPassword)['algo']) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE students SET password = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashedPassword, $studentID);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Verify password
    if (password_verify($password, $hashedPassword)) {
        $_SESSION['student_id'] = $studentID;
        $sessionID = session_id();
        error_log("student_login(back).php: Login successful. student_id set to: " . $_SESSION['student_id']); // Log successful login

        echo json_encode([
            "status" => "success",
            "redirect" => "Dashboard.php",
            "student_id" => $studentID, 
            "session_id" => $sessionID
        ]);
    } else {
        http_response_code(401);
        error_log("student_login(back).php: Invalid login credentials for student ID: " . $studentID); // Log failed login
        echo json_encode(["status" => "error", "message" => "Invalid login credentials."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "User not found."]);
}

$stmt->close();
$conn->close();
?>