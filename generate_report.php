<?php
require_once 'TCPDF-main/tcpdf.php';
require_once 'db_connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Check if POST data is received
if (!isset($_POST['studentId'], $_POST['startDate'], $_POST['endDate'])) {
    error_log("Error: Missing POST data for studentId, startDate, or endDate.");
    die("Invalid request. Please provide a student ID and a date range.");
}

$student_id = $_POST['studentId'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

// Validate date format (YYYY-MM-DD)
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $startDate) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $endDate)) {
    error_log("Error: Invalid date format - StartDate: $startDate, EndDate: $endDate");
    die("Invalid date format. Please select valid dates.");
}

// Fetch student details
$sql_student = "SELECT Student_ID, Name, School, department FROM students WHERE Student_ID = ?";
$stmt_student = $conn->prepare($sql_student);

if (!$stmt_student) {
    error_log("Error preparing student SQL statement: " . $conn->error);
    die("Error fetching student details.");
}

$stmt_student->bind_param("s", $student_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();

if ($result_student->num_rows === 0) {
    error_log("Error: No student found with ID: $student_id");
    die("No student found with the provided ID.");
}

$student_info = $result_student->fetch_assoc();

// Fetch appointments with lecturer name
$sql = "SELECT a.appointment_id, l.name AS lecturer_name, a.appointment_date, a.Description 
        FROM appoint a
        JOIN lecturer l ON a.lecturer_id = l.Lecturer_ID
        WHERE a.Student_ID = ? AND a.appointment_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparing appointments SQL statement: " . $conn->error);
    die("Error fetching appointments.");
}

$stmt->bind_param("sss", $student_id, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("No appointments found for Student ID: $student_id between $startDate and $endDate");
    die("No appointments found for the selected date range.");
}

// Create a new PDF document
try {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', '', 12);
    $pdf->Cell(0, 10, "Student Report from $startDate to $endDate", 0, 1, 'C');
    $pdf->Ln(10);

    // Display student details
    $pdf->Cell(40, 10, 'Student Name: ', 0);
    $pdf->Cell(100, 10, $student_info['Name'], 0, 1);
    $pdf->Cell(40, 10, 'Student ID: ', 0);
    $pdf->Cell(100, 10, $student_info['Student_ID'], 0, 1);
    $pdf->Cell(40, 10, 'School: ', 0);
    $pdf->Cell(100, 10, $student_info['School'], 0, 1);
    $pdf->Cell(40, 10, 'Department: ', 0);
    $pdf->Cell(100, 10, $student_info['department'], 0, 1);
    $pdf->Ln(10);

     // Table Headers (Adjusted column widths)
     $pdf->Cell(45, 10, 'Appt. ID', 1); // Increased Appointment ID width
     $pdf->Cell(40, 10, 'Lecturer', 1); // Reduced Lecturer width
     $pdf->Cell(30, 10, 'Date', 1); // Reduced Date width
     $pdf->Cell(75, 10, 'Description', 1); // Increased Description width
     $pdf->Ln(10);
 
     // Table Data (Adjusted column widths)
     while ($row = $result->fetch_assoc()) {
         $pdf->Cell(45, 10, $row['appointment_id'], 1); // Increased Appointment ID width
         $pdf->Cell(40, 10, $row['lecturer_name'], 1); // Reduced Lecturer width
         $pdf->Cell(30, 10, $row['appointment_date'], 1); // Reduced Date width
         $pdf->Cell(75, 10, $row['Description'], 1); // Increased Description width
         $pdf->Ln(10);
     }
 


    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="student_report.pdf"');

    // Output the PDF
    $pdf->Output('student_report.pdf', 'I');

} catch (Exception $e) {
    error_log("Error generating PDF: " . $e->getMessage());
    die("Failed to generate the PDF. Please check the error log.");
}
?>