<?php
// Allow requests from any origin (for development only)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_connection.php'; 

$dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : '';

if (!empty($dept_id)) {
    // Fetch lecturers along with department name
    $sql = "SELECT lecturer.id, lecturer.name AS lecturer_name, department.name AS department_name
            FROM lecturer
            JOIN department ON lecturer.department_id = department.id
            WHERE lecturer.department_id = ?"; 

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lecturer = [];
    while ($row = $result->fetch_assoc()) {
        $lecturer[] = $row;
    }

    // Return JSON response
    echo json_encode($lecturer);
} else {
    echo json_encode(["error" => "Department ID missing"]);
}
?>
