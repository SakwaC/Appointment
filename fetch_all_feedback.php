<?php
include 'db2_connect.php';

$feedbackQuery = "SELECT feedback_text FROM feedback"; // Fetch all feedback messages
$result = $conn->query($feedbackQuery);

$output = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr><td>{$row['feedback_text']}</td></tr>";
    }
} else {
    $output .= "<tr><td class='text-center text-danger'>No feedback found.</td></tr>";
}

echo $output;
?>
