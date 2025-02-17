<?php
$servername = "localhost"; 
$username = "root"; // Database username
$password = ""; // Database password
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
    $comments = $_POST['comments'];
    $status = $_POST['status'];

    // Use prepared statements for security
    $stmt = $conn->prepare("UPDATE appointments SET status = ?, lecturer_comments = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $comments, $appointment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment status updated successfully!'); window.location.href='approve_appointment.html';</script>";
    } else {
        echo "<script>alert('Error updating appointment status. Try again later.'); window.location.href='approve_appointment.html';</script>";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
