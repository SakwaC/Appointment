<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit();
}

// Validate input fields
if (!isset($_POST['Lecturer_ID']) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing Lecturer_ID or Password."]);
    exit();
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
    exit();
}

// Get user input & sanitize
$lecturerID = trim($_POST['Lecturer_ID']);
$password = trim($_POST['password']);

// Prepare SQL statement
$sql = "SELECT Password FROM lecturer WHERE lecturer_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $lecturerID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    // Verify hashed password
    if (password_verify($password, $hashedPassword)) {
        echo json_encode(["status" => "success", "redirect" => "lecturer_dashboard.html"]);
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
