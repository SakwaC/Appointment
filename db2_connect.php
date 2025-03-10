<?php
$servername = "localhost"; 
$username = "root";
$password = ""; 
$dbname = "appointment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get total booked appointments
function getBookedAppointments() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM appoint WHERE status = 'booked'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to get total canceled appointments
function getCanceledAppointments() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM appoint WHERE status = 'canceled'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to get total registered students
function getRegisteredStudents() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM students";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to get available lecturers
function getAvailableLecturers() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM lecturer WHERE availability = 'available'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}
?>
