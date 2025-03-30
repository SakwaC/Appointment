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
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Invalid Lecturer ID. Please check and try again.', 0, 1, 'C');
        $pdf->Output('lecturer_appointments_report.pdf', 'I');
        exit();
    }

    if (!$startDate) {
        $stmtFirstDate = $conn->prepare("SELECT MIN(appointment_date) AS first_date FROM appoint WHERE lecturer_id = ?");
        $stmtFirstDate->bind_param("i", $lecturerId);
        $stmtFirstDate->execute();
        $resultFirstDate = $stmtFirstDate->get_result();
        $rowFirstDate = $resultFirstDate->fetch_assoc();
        $startDate = $rowFirstDate['first_date'] ? $rowFirstDate['first_date'] : $endDate;
    }

    $stmtAppointments = $conn->prepare("SELECT s.Name, s.Student_ID, a.appointment_date, a.time_of_appointment, a.Description, a.status 
                                        FROM appoint a 
                                        JOIN students s ON a.student_id = s.Student_ID 
                                        WHERE a.lecturer_id = ? 
                                        AND a.appointment_date BETWEEN ? AND ?");
    $stmtAppointments->bind_param("iss", $lecturerId, $startDate, $endDate);
    $stmtAppointments->execute();
    $resultAppointments = $stmtAppointments->get_result();

    if (ob_get_length()) {
        ob_end_clean();
    }

    class CustomPDF extends TCPDF {
        public function Footer() {
            $this->SetY(-20);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
            $this->Ln(5);
            $this->Cell(0, 10, 'Kenyatta University Student Lecturer Appointment System', 0, 0, 'C');
        }
    }

    $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 20);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Lecturer Appointments Report');
    $pdf->SetSubject('Appointments');

    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'R');

    $pdf->Image('C:/Users/pc/Downloads/downloads/htdocs/Appointments/Ku_logo.jpeg', 15, 10, 25, 25, 'JPEG');
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Lecturer Appointments Report', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, "From: $startDate To: $endDate", 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 10, "Lecturer Name: $lecturerName", 0, 1, 'L');
    $pdf->Cell(0, 10, "Lecturer ID: $lecturerID", 0, 1, 'L');
    $pdf->Cell(0, 10, "Department: $lecturerDepartment", 0, 1, 'L');
    $pdf->Ln(5);

    if ($resultAppointments->num_rows > 0) {
        $html = '<table border="1" cellpadding="6">
                    <thead>
                        <tr style="background-color:#f2f2f2; font-weight: bold; text-align: center;">
                            <th style="width: 20%;">Student Name</th>
                            <th style="width: 20%;">Student ID</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 15%;">Time</th>
                            <th style="width: 20%;">Reason</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>';

        while ($row = $resultAppointments->fetch_assoc()) {
            $html .= '<tr style="text-align: center;">';
            $html .= '<td style="width: 20%;">' . htmlspecialchars($row['Name']) . '</td>';
            $html .= '<td style="width: 20%;">' . htmlspecialchars($row['Student_ID']) . '</td>';
            $html .= '<td style="width: 15%;">' . htmlspecialchars($row['appointment_date']) . '</td>';
            $html .= '<td style="width: 15%;">' . htmlspecialchars($row['time_of_appointment']) . '</td>';
            $html .= '<td style="width: 20%;">' . htmlspecialchars($row['Description']) . '</td>';
            $html .= '<td style="width: 15%;">' . htmlspecialchars($row['status']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    } else {
        $pdf->Cell(0, 10, 'No appointments found within the selected date range.', 0, 1, 'C');
    }

    $pdf->Output('lecturer_appointments_report.pdf', 'I');
    exit();
}

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'No Lecturer ID Provided. Please enter a valid ID.', 0, 1, 'C');
$pdf->Output('lecturer_appointments_report.pdf', 'I');
exit();
?>
