<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure the response is in JSON format
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Allow specific HTTP methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow specific headers

// Handle Preflight OPTIONS Request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'appointment');

    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Database connection failed."]);
        exit();
    }

    if (empty($_POST)) {
        echo json_encode(["status" => "error", "message" => "No data received."]);
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

    if (empty($lecturer_ID) || empty($name) || empty($email) || empty($password) || empty($contact_No) || empty($department)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
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
}
?>
