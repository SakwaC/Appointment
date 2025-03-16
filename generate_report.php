<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
require 'db_connection.php';
require_once('tcpdf/tcpdf.php'); // Include TCPDF library

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

// Create PDF document using TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your System');
$pdf->SetTitle('Student Appointments Report');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Student Appointments Report', '');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 12, '', true);
$pdf->AddPage();

$html = '<h1>Student Appointments Report</h1>';
$html .= '<table border="1"><thead><tr><th>Student Name</th><th>Reg No</th><th>Appointment Date</th><th>Status</th></tr></thead><tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr><td>' . $row['student_name'] . '</td><td>' . $row['registration_number'] . '</td><td>' . $row['appointment_date'] . '</td><td>' . ucfirst($row['status']) . '</td></tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("student_report.pdf", "D"); // "D" for download
?>