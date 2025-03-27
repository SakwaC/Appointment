<?php
session_start();
require_once 'TCPDF-main/tcpdf.php';
require_once 'db2_connect.php';

// Get report parameters
$reportType = $_POST['report_type'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$department = $_POST['department'] ?? ''; // Added department filter

if (empty($reportType) || empty($startDate) || empty($endDate)) {
    die("Please fill in all required fields.");
}

// Report title mapping
$reportTitles = [
    'appointments' => 'Appointments Report',
    'feedback' => 'Feedback Report',
    'lecturers' => 'Lecturers Report',
    'students' => 'Registered Students Report',
];

$title = $reportTitles[$reportType] ?? 'Unknown Report';

// Create a new TCPDF object
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 12);

// Title of the Report
$pdf->Cell(0, 10, $title, 0, 1, 'C');

// Date Range Below Title
$pdf->Cell(0, 10, "From: $startDate  To: $endDate", 0, 1, 'C');

// Add department filter (for Appointments and Students reports)
if (!empty($department) && ($reportType === 'appointments' || $reportType === 'students')) {
    $pdf->Cell(0, 10, "Department: $department", 0, 1, 'C');
}

$pdf->Ln(10);

// SQL query based on selected report type
switch ($reportType) {
    case 'appointments':
        $sql = "SELECT a.Appointment_ID, s.name AS student_name, l.name AS lecturer_name, l.department, 
                       a.appointment_date, a.time_of_appointment, a.status 
                FROM appoint a 
                JOIN students s ON a.student_id = s.Student_ID 
                JOIN lecturer l ON a.lecturer_id = l.lecturer_id 
                WHERE a.appointment_date BETWEEN '$startDate' AND '$endDate'";

        // Apply department filter if selected
        if (!empty($department)) {
            $sql .= " AND l.department = '$department'";
        }
        break;

    case 'feedback':
        $sql = "SELECT student_id, Email, feedback_text 
                FROM feedback 
                WHERE feedback_date BETWEEN '$startDate' AND '$endDate'";
        break;

    case 'lecturers':
        $sql = "SELECT lecturer_id, name AS lecturer_name, department FROM lecturer";
        break;

    case 'students':
        $sql = "SELECT Student_ID, Name, Email, Contact_No, school, department, Course, Registration_Date 
                FROM students";

        // Apply department filter if selected
        if (!empty($department)) {
            $sql .= " WHERE department = '$department'";
        }
        break;

    default:
        $pdf->Cell(0, 10, 'Invalid report type selected.', 1, 1, 'C');
        $pdf->Output('report.pdf', 'I');
        exit();
}

// Execute the query
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            $pdf->Cell(40, 10, ucfirst(str_replace('_', ' ', $key)), 1);
            $pdf->Cell(80, 10, $value, 1);
            $pdf->Ln();
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No data found for the selected report type.', 1, 1, 'C');
}

// Output the generated PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="report.pdf"');
$pdf->Output('report.pdf', 'I');
?>
