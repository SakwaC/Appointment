<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_connection.php';

$department_name = isset($_GET['department']) ? $_GET['department'] : '';

if (!empty($department_name)) {
    // Select unique lecturer names and pick the first ID for each
    $sql = "SELECT MIN(id) as id, lecturer_ID, name FROM lecturer WHERE department = ? GROUP BY name, lecturer_ID";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $department_name);
        $stmt->execute();
        $result = $stmt->get_result();

        $lecturers = [];

        while ($row = $result->fetch_assoc()) {
            $lecturers[] = [
                "id" => $row['id'], 
                "lecturer_ID" => $row['lecturer_ID'],
                "name" => $row['name']
            ];
        }

        echo json_encode($lecturers);
    } else {
        echo json_encode(["error" => "Database query failed: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Department name missing"]);
}
?>
