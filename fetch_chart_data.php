<?php
include 'db2_connect.php';

// Fetch registered students per department
$departmentData = [];
$departments = ['CS', 'IT', 'Math', 'Physics', 'Engineering'];

foreach ($departments as $dept) {
    $query = "SELECT COUNT(*) as total FROM students WHERE department = '$dept'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $departmentData[] = $row['total'] ?? 0;
}

// Fetch lecturer availability
$query = "SELECT 
            SUM(CASE WHEN availability = 'available' THEN 1 ELSE 0 END) AS available,
            SUM(CASE WHEN availability = 'busy' THEN 1 ELSE 0 END) AS busy
          FROM lecturers";
$result = $conn->query($query);
$lecturerAvailability = $result->fetch_assoc();

$response = [
    'departmentData' => $departmentData,
    'lecturerAvailability' => [
        'available' => $lecturerAvailability['available'] ?? 0,
        'busy' => $lecturerAvailability['busy'] ?? 0
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>
