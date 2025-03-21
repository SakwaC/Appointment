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
    $query = "SELECT COUNT(*) as total FROM appoint";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;  // Ensure it returns 0 if empty
}

// Function to get total rejected (canceled) appointments
function getCanceledAppointments() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM appoint WHERE status = 'Rejected'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Function to get total registered students
function getRegisteredStudents() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM students";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Function to get available lecturers
function getAvailableLecturers() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM lecturer WHERE availability = 'available'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Function to fetch lecturer name by ID
function getLecturerNameById($lecturerId) {
    global $conn;
    $stmt = $conn->prepare("SELECT name FROM lecturer WHERE lecturer_id = ?");
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $lecturerName = $row['name'];
    } else {
        $lecturerName = null; // Lecturer not found
    }

    $stmt->close();
    return $lecturerName;
}

// Handle the fetch_lecturer_name.php request (if needed)

if (isset($_GET['lecturerId'])) {
    $lecturerId = $_GET['lecturerId'];

    $lecturerName = getLecturerNameById($lecturerId);

    if ($lecturerName !== null) {
        $lecturer = array('name' => $lecturerName);
        echo json_encode($lecturer);
    } else {
        echo json_encode(array('name' => null));
    }
}

// Do NOT close the connection here!
?>