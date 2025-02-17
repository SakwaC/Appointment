<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "appointment"; 

// Create a connection
$conn = new mysqli('localhost', 'root', '', 'appointment');

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback_text'];

    // Use prepared statements for security
    $stmt = $conn->prepare("INSERT INTO feedback (student_id, email, feedback_text) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $student_id, $email, $feedback);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='feedback.html';</script>";
    } else {
        echo "<script>alert('Error submitting feedback. Try again later.'); window.location.href='feedback.html';</script>";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
