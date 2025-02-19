
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Allow specific origin or * for development 
header('Access-Control-Allow-Origin: *');  // Or a specific domain
header('Access-Control-Allow-Methods: POST'); // Only POST is needed
header('Access-Control-Allow-Headers: Content-Type'); // Only Content-Type

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'appointment');

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['status' => 'failure', 'message' => "Database connection failed: " . $conn->connect_error]);
        exit;
    }

    // Use $_POST directly since you're sending form data
    $Student_ID = trim($_POST['Student_ID'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $feedback = trim($_POST['feedback_text'] ?? '');

    // Validation 
    if (empty($Student_ID) || empty($email) || empty($feedback)) {
        http_response_code(400);
        echo json_encode(['status' => 'failure', 'message' => "All fields are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'failure', 'message' => "Invalid email format."]);
        exit;
    }

    // Prepared Statement 
    $sql = "INSERT INTO feedback (Student_ID, Email, feedback_text) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $Student_ID, $email, $feedback);

        if ($stmt->execute()) {
            http_response_code(201); // 201 Created is more appropriate for successful inserts
            echo json_encode(['status' => 'success', 'message' => 'Feedback submitted successfully.']);
        } else {
            http_response_code(500);
            // Include more detailed error info for debugging (remove in production)
            echo json_encode(['status' => 'failure', 'message' => "Error inserting record: " . $stmt->error . " (errno: " . $stmt->errno . ")"]);
        }

        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'failure', 'message' => "Error preparing statement: " . $conn->error]);
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'failure', 'message' => "Invalid request. Please use POST."]);
}
?>