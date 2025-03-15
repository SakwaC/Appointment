<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'appointment');

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['status' => 'failure', 'message' => "Connection failed: " . $conn->connect_error]));
    }

    // Debugging - Check if POST data is received
    if (empty($_POST)) {
        die(json_encode(['status' => 'failure', 'message' => "No data received. Check if the form method is POST."]));
    }

    // Get input values
    $Student_ID = trim($_POST['Student_ID']);
    $Name = trim($_POST['Name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $Contact_No = trim($_POST['contactNo']); 
    $school = trim($_POST['school']); 
    $department = trim($_POST['department']); 
    $course = trim($_POST['course']);
    $Registration_Date = trim($_POST['registration_date']); 

    // Validate required fields
    if (empty($Student_ID) || empty($Name) || empty($email) || empty($password) || empty($Contact_No) || empty($school) || empty($department) || empty($course) || empty($Registration_Date)) {
        die(json_encode(['status' => 'failure', 'message' => "All fields are required."]));
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert new record
    $sql = "INSERT INTO students (Student_ID, Name, Email, Password, Contact_No, school, department, Course, Registration_Date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssss", $Student_ID, $Name, $email, $hashed_password, $Contact_No, $school, $department, $course, $Registration_Date);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
        }
    
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    }
       

    // Close connection
    $conn->close();
}
?>