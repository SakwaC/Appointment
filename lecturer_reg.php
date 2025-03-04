<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization'); 

// Handle Preflight OPTIONS Request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'appointment');
    
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }
    
    // Check if data is received
    if (empty($_POST)) {
        echo json_encode(["status" => "error", "message" => "No data received.", "debug" => file_get_contents("php://input")]);
        exit();
    }

    // Get and sanitize input values
    $lecturer_ID = htmlspecialchars(trim($_POST['lecturerId']));
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $contact_No = htmlspecialchars(trim($_POST['contactNo']));
    $department = htmlspecialchars(trim($_POST['department']));
    $registration_date = date('Y-m-d'); // Auto-generate today's date

    // Validate required fields
    if (empty($lecturer_ID) || empty($name) || empty($email) || empty($password) || empty($contact_No) || empty($department)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit();
    }

    // Check if lecturer_ID already exists
    $checkQuery = "SELECT lecturer_ID FROM lecturer WHERE lecturer_ID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $lecturer_ID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Lecturer ID already registered."]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new lecturer record
    $sql = "INSERT INTO lecturer (lecturer_ID, Name, Email, Password, Contact_No, Department, Registration_Date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssss", $lecturer_ID, $name, $email, $hashed_password, $contact_No, $department, $registration_date);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
