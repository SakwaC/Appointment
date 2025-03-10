<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

// Validate input fields
if (!isset($_POST['Student_ID']) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing Student_ID or Password."]);
    exit;
}

// Database connection
$host = "localhost"; 
$username = "root";  
$password = "";      
$database = "appointment"; 

$conn = new mysqli($host, $username, $password, $database);

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Get user input & sanitize
$studentID = trim($_POST['Student_ID']);
$password = trim($_POST['password']);

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT password FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $hashedPassword = $user['password']; // Retrieve stored hashed password

    // Check if the password is hashed; if not, hash it and update DB
    if (!password_get_info($hashedPassword)['algo']) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the database with the hashed password
        $update_sql = "UPDATE students SET password = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashedPassword, $studentID);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Verify hashed password
    if (password_verify($password, $hashedPassword)) {
        $_SESSION['student_id'] = $studentID;
        echo json_encode([
            "status" => "success",
            "redirect" => "Dashboard.html",
            "student_id" => $studentID  // Include student_id in response
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid login credentials."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "User not found."]);
}

// Close connection
$stmt->close();
$conn->close();
?>
