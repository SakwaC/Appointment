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
        die("Connection failed: " . $conn->connect_error);
    }

    // Debugging - Check if POST data is received
    if (empty($_POST)) {
        die("No data received. Check if the form method is POST.");
    }
    
    // Get input values
    $Student_ID = trim($_POST['Student_ID']);
    $Name = trim($_POST['Name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $contact_No = trim($_POST['contact_No']);
    $course = trim($_POST['course']);
    $registration_date = trim($_POST['registration_date']);

    // Validate required fields
    if (empty($Student_ID) || empty($Name) || empty($email) || empty($password) || empty($contact_No) || empty($course) || empty($registration_date)) {
        die("All fields are required.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert new record
    $sql = "INSERT INTO students (Student_ID, Name, Email, Password, Contact_No, Course, Registration_Date) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssss", $Student_ID, $Name, $email, $hashed_password, $contact_No, $course, $registration_date);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'failure']);
        }
        

        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>