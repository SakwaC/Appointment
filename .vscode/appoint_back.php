<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "appointment"; 

// Create a database connection
$conn = new mysqli('localhost', 'root', '', 'appointment');


// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_description = $_POST['appointment_description'];

    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO appoint (id, appointment_date, description, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("sss", $appointment_id, $appointment_date, $appointment_description);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment created successfully!'); window.location.href='create_appointment.html';</script>";
    } else {
        echo "<script>alert('Error creating appointment. Please try again.'); window.location.href='create_appointment.html';</script>";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
