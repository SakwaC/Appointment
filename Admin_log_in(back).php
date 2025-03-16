<?php
// Cors fixation
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allowed_origins = [
    'http://localhost:3000',
    'http://localhost:5500',
];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $origin);
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit();
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => "Origin not allowed."]);
    exit();
}

session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = intval(trim($_POST['admin_ID'])); // Convert to integer
    $password = trim($_POST['password']);

    if (empty($admin_id) || empty($password)) {
        echo json_encode(['error' => "All fields are required."]);
        exit();
    }

    try {
        $sql = "SELECT admin_ID, name, password FROM admin WHERE admin_ID = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("i", $admin_id); // Bind as integer

        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                echo json_encode(['success' => true, 'admin_ID' => $admin['admin_ID'], 'admin_name' => $admin['name']]);
                exit();
            } else {
                $error_message = "Invalid password for admin_ID: " . $admin_id;
                error_log($error_message);
                echo json_encode(['error' => "Invalid login credentials."]); // Improved error message
                exit();
            }
        } else {
            $error_message = "Admin ID not found: " . $admin_id;
            error_log($error_message);
            echo json_encode(['error' => "Invalid login credentials."]); // Improved error message
            exit();
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['error' => "Database error."]);
        exit();
    }
} else {
    echo json_encode(['error' => "Invalid request method."]);
    exit();
}
?>