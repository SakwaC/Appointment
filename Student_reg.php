<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'appointment');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create students table if it doesn't exist
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Student_ID VARCHAR(50) NOT NULL,
        Name VARCHAR(100) NOT NULL,
        Email VARCHAR(100) NOT NULL,
        Password VARCHAR(255) NOT NULL,
        Contact_No VARCHAR(50) NOT NULL,
        Course VARCHAR(100) NOT NULL,
        Registration_Date DATE NOT NULL
    )";

    if (!$conn->query($createTableQuery)) {
        die("Error creating table: " . $conn->error);
    }

    // Get input values
    $Student_ID = $_POST['Student_ID'];
    $Name = $_POST['Name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact_No = $_POST['contact_No'];
    $course = $_POST['course'];
    $registration_date = $_POST['registration_date'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert new record
    $sql = "INSERT INTO students (Student_ID, Name , Email , Password , Contact_No, Course, Registration_Date) VALUES (?, ?, ? ,?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $Student_ID, $Name, $email, $hashed_password, $contact_No, $course, $registration_date);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href = 'student_login.html'; 
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
