<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'appointment');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // Get input values
    $Lecturer_Id = $_POST['Lecturer_Id'];
    $Name = $_POST['Name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact_No = $_POST['contact_No'];
    $Department = $_POST['Department'];
    $registration_date = $_POST['registration_date'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert new record
    $sql = "INSERT INTO lecturer (Lecturer_ID, Name , Email , Password , Contact_No, Department, Registration_Date) VALUES (?, ?, ? ,?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $Lecturer_ID, $Name, $email, $hashed_password, $contact_No, $Department, $registration_date);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href = 'lecturer_login.html'; 
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
