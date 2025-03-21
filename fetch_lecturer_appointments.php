<?php
// fetch_lecturer_appointments.php

include 'db2_connect.php';
require_once 'TCPDF-main/tcpdf.php';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="report.pdf"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

if (isset($_GET['lecturerId']) && !empty($_GET['lecturerId'])) {
    $lecturerId = $_GET['lecturerId'];

    // Get dynamic date range from URL, fallback to database values
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

    // Fetch lecturer details
    $stmtLecturer = $conn->prepare("SELECT name, lecturer_ID, Department FROM lecturer WHERE lecturer_ID = ?");
    $stmtLecturer->bind_param("i", $lecturerId);
    $stmtLecturer->execute();
    $resultLecturer = $stmtLecturer->get_result();

    if ($resultLecturer->num_rows > 0) {
        $lecturer = $resultLecturer->fetch_assoc();
        $lecturerName = htmlspecialchars($lecturer['name']);
        $lecturerID = htmlspecialchars($lecturer['lecturer_ID']);
        $lecturerDepartment = htmlspecialchars($lecturer['Department']);
    } else {
        // Invalid lecturer ID
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Invalid Lecturer ID.', 0, 1, 'C');
        $pdf->Output('lecturer_appointments_report.pdf', 'I');
        exit();
    }

    // Fetch the first available appointment date if start_date is not provided
    if (!$startDate) {
        $stmtFirstDate = $conn->prepare("SELECT MIN(appointment_date) AS first_date FROM appoint WHERE lecturer_id = ?");
        $stmtFirstDate->bind_param("i", $lecturerId);
        $stmtFirstDate->execute();
        $resultFirstDate = $stmtFirstDate->get_result();
        $rowFirstDate = $resultFirstDate->fetch_assoc();
        $startDate = $rowFirstDate['first_date'] ? $rowFirstDate['first_date'] : $endDate;
    }

    // Fetch appointments within the date range
    $stmtAppointments = $conn->prepare("SELECT s.Name, s.Student_ID, a.appointment_date, a.time_of_appointment, a.Description, a.status 
                                        FROM appoint a 
                                        JOIN students s ON a.student_id = s.Student_ID 
                                        WHERE a.lecturer_id = ? 
                                        AND a.appointment_date BETWEEN ? AND ?");
    $stmtAppointments->bind_param("iss", $lecturerId, $startDate, $endDate);
    $stmtAppointments->execute();
    $resultAppointments = $stmtAppointments->get_result();

    // Ensure no output before PDF generation
    if (ob_get_length()) {
        ob_end_clean();
    }

    // Create PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Lecturer Appointments Report');
    $pdf->SetSubject('Appointments');

    // Add a page
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14);

    // Report Title
    $pdf->Cell(0, 10, 'Lecturer Appointments Report', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, "From: $startDate To: $endDate", 0, 1, 'C');
    $pdf->Ln(5); // Add space

    // Lecturer Details
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 10, "Lecturer Name: $lecturerName", 0, 1, 'L');
    $pdf->Cell(0, 10, "Lecturer ID: $lecturerID", 0, 1, 'L');
    $pdf->Cell(0, 10, "Department: $lecturerDepartment", 0, 1, 'L');
    $pdf->Ln(5); // Space before table

    if ($resultAppointments->num_rows > 0) {
        // Table header
        $html = '<table border="1" cellpadding="5">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>Student Name</th>
                            <th>Student ID No</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Table rows
        while ($row = $resultAppointments->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['Name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['Student_ID']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['appointment_date']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['time_of_appointment']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['Description']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        // No appointments found
        $pdf->Cell(0, 10, 'No appointments found within the selected date range.', 0, 1, 'C');
    }

    // Output the PDF
    $pdf->Output('lecturer_appointments_report.pdf', 'I');

    exit();
}

// If no lecturer ID is provided
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'No Lecturer ID Provided.', 0, 1, 'C');
$pdf->Output('lecturer_appointments_report.pdf', 'I');
exit();
?>
