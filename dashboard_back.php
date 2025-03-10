<?php
session_start(); // Start the session

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "appointment";

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Fetch all records from the appoint table
$query = "SELECT a.*, l.Name AS lecturer_name, l.Contact_No AS lecturer_contact 
          FROM appoint a 
          LEFT JOIN lecturer l ON a.Lecturer_ID = l.id";

$result = $conn->query($query);

if ($result) {
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    echo json_encode(["status" => "success", "appointments" => $appointments]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to fetch records"]);
}

$conn->close();
?>
