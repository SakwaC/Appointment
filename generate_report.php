<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
require 'db_connection.php';
require('fpdf.php'); // Ensure FPDF is in the correct directory

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

// Validate inputs
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';

if (empty($startDate) || empty($endDate)) {
    die("Please select a valid date range.");
}

// Fetch student appointment details
$sql = "SELECT s.student_name, s.registration_number, a.appointment_date, a.status
        FROM students s
        JOIN appointments a ON s.student_id = a.student_id
        WHERE a.appointment_date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);

if (!$stmt->execute()) {
    die("SQL Error: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No appointments found for the selected date range.");
}

// Create PDF document
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, "Student Appointments Report", 0, 1, 'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Student Name', 1);
$pdf->Cell(40, 10, 'Reg No', 1);
$pdf->Cell(50, 10, 'Appointment Date', 1);
$pdf->Cell(50, 10, 'Status', 1);
$pdf->Ln();

// Table content
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, $row['student_name'], 1);
    $pdf->Cell(40, 10, $row['registration_number'], 1);
    $pdf->Cell(50, 10, $row['appointment_date'], 1);
    $pdf->Cell(50, 10, ucfirst($row['status']), 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output("D", "student_report.pdf");
?>
