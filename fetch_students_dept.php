<?php
// fetch_students_by_department.php

// Include your database connection file
include 'db2_connect.php';
require_once 'TCPDF-main/tcpdf.php';

// Retrieve parameters from the GET request
$school = $_GET['school'] ?? '';
$department = $_GET['department'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Basic input validation
if (empty($school) || empty($department) || empty($startDate) || empty($endDate)) {
    die("Missing parameters. Please provide school, department, start date, and end date.");
}

// SQL query to fetch students based on school, department, and registration date
$sql = "SELECT Student_ID, Name, Email, Contact_No, school, department, Course, Registration_Date
        FROM students
        WHERE school = ? AND department = ? AND Registration_Date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("ssss", $school, $department, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No students found for the given criteria.");
}

// Create a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 14);

// Report Title
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(0, 10, "Registered Students Report", 0, 1, 'C');
$pdf->Ln(3);

// Date Range (Centered)
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, "From: $startDate To: $endDate", 0, 1, 'C');
$pdf->Ln(5);

// School and Department (Left-aligned)
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 8, "School: $school", 0, 1, 'L');
$pdf->Cell(0, 8, "Department: $department", 0, 1, 'L');
$pdf->Ln(10);

// Table Headers
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(35, 12, 'STUDENT ID', 1, 0, 'C');  // Increased width for Student ID
$pdf->Cell(30, 12, 'Name', 1, 0, 'C');        // Reduced Name width
$pdf->Cell(40, 12, 'Email', 1, 0, 'C');       // Reduced Email width
$pdf->Cell(30, 12, 'Contact No.', 1, 0, 'C');
$pdf->Cell(35, 12, 'Course', 1, 1, 'C');      // Increased width for Course

// Table Data
$pdf->SetFont('Helvetica', '', 11);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(35, 10, $row['Student_ID'], 1, 0, 'C');  // Increased font size for ID
    $pdf->Cell(30, 10, $row['Name'], 1, 0, 'L');
    $pdf->Cell(40, 10, $row['Email'], 1, 0, 'L');
    $pdf->Cell(30, 10, $row['Contact_No'], 1, 0, 'C');
    $pdf->Cell(35, 10, $row['Course'], 1, 1, 'C');  // Increased width for Course
}

// Output the PDF
$pdf->Output('students_report.pdf', 'I');

$stmt->close();
$conn->close();
?>
