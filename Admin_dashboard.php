<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e3f2fd;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand img {
            height: 50px;
        }
        .dashboard-container {
            padding: 20px;
        }
        .card {
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: white;
            margin-bottom: 20px;
        }
        .report-section {
         display: grid;
         grid-template-columns: repeat(3, 1fr); /* Creates three equal columns */
         gap: 20px;
         justify-content: center;
         }

     .report-container {
    background-color: #ffffff;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    border: 1px solid #007bff;
    min-width: 300px;
    text-align: center;
}

        .navbar .logout-btn {
            position: absolute;
            right: 20px;
        }

        .feedback-container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    background: #ffffff; /* White background */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
    text-align: center;
}

.feedback-header {
    font-size: 1.5rem;
    color: #007bff; /* Bootstrap primary blue */
    font-weight: bold;
}

.feedback-card {
    padding: 20px;
    border-radius: 10px;
    background: #f8f9fa; /* Light gray background */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

.feedback-list {
    padding: 0;
    list-style: none;
    text-align: left;
    max-height: 200px;
    overflow-y: auto; /* Scrollable feedback */
}

.feedback-list li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    font-size: 1rem;
    color: #333;
}

.feedback-list li:last-child {
    border-bottom: none;
}

.view-feedback-btn {
    margin-top: 10px;
    text-decoration: none;
    font-weight: bold;
}


        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'db2_connect.php'; ?>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
            <img src="Ku_logo.jpeg" alt="Logo">
            Admin Dashboard
        </a>
        <div class="ml-auto">
            <a href="land_in.php" class="btn btn-dark">Log out</a>
        </div>
    </nav>

    <div class="container-fluid dashboard-container">
        <h2 class="text-center text-primary">Admin Dashboard</h2>
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Booked Appointments</h4>
                    <h2><?php echo getBookedAppointments(); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Canceled Appointments</h4>
                    <h2><?php echo getCanceledAppointments(); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Registered Students</h4>
                    <h2><?php echo getRegisteredStudents(); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Available Lecturers</h4>
                    <h2><?php echo getAvailableLecturers(); ?></h2>
                </div>
            </div>
        </div>
        <h2 class="text-center text-uppercase fw-bold text-primary border-bottom pb-2">Reports</h2>
        <div class="container mt-4">
    <div class="row">
        <!-- First Column: General Report -->
        <div class="col-md-4">
            <form action="generate_admin_report.php" method="POST" class="text-center border p-3">
                <h4 class="text-primary">General Report</h4>
                <select name="report_type" class="form-control mb-3" required>
                    <option value="">Select Report Type</option>
                    <option value="appointments">Booked Appointments Report</option>
                    <option value="feedback">Feedback Report</option>
                    <option value="lecturers">Lecturers and Departments Report</option>
                    <option value="students">Registered Students Report</option>
                </select>

                <select name="department" id="department" class="form-control mb-3">
                    <option value="">Select Department</option>
                    <option value="Computing and Information Science">Computing and Information Science</option>
                    <option value="Biology and Chemistry">Biology and Chemistry</option>
                    <option value="Biotechnology">Biotechnology</option>
                    <option value="Accounting">Accounting</option>
                    <option value="Business Administration">Business Administration</option>
                    <option value="Mechanical">Mechanical</option>
                    <option value="Electrical">Electrical</option>
                    <option value="Geography">Geography</option>
                    <option value="English Literature">English Literature</option>
                </select>

                <input type="date" name="start_date" class="form-control mb-3" required>
                <input type="date" name="end_date" class="form-control mb-3" required>
                <button type="submit" class="btn btn-primary w-100">Download Report</button>
            </form>
        </div>

        <!-- Second Column: Appointments by Lecturer -->
        <div class="col-md-4">
            <div id="lecturerAppointment" class="border p-3">
                <h4 class="text-center text-primary">Appointments by Lecturer</h4>
                <form id="lecturerAppointmentForm" class="text-center">
                    <input type="text" id="lecturerIdInput" class="form-control mb-3" placeholder="Enter Lecturer ID" required>
                    <input type="date" id="lecturerStartDate" class="form-control mb-3" required>
                    <input type="date" id="lecturerEndDate" class="form-control mb-3" required>
                    <button type="button" onclick="generateLecturerAppointmentsReport()" class="btn btn-primary w-100">Generate Report</button>
                </form>
                <div id="lecturerAppointmentReportArea" class="mt-3"></div>
            </div>
        </div>

        <!-- Third Column: Students by School/Department -->
        <div class="col-md-4">
            <div id="schoolDepartmentDate" class="border p-3">
                <h4 class="text-center text-primary">Students by School/Department</h4>
                <form id="schoolDepartmentForm" class="text-center">
                    <select id="school" class="form-control mb-3" required>
                        <option value="">Select School</option>
                        <option value="SPAS">SPAS</option>
                        <option value="School of Business">School of Business</option>
                        <option value="SEA">SEA</option>
                        <option value="School of Education">School of Education</option>
                    </select>
                    <select id="schoolDepartment" class="form-control mb-3" required>
                        <option value="">Select Department</option>
                    </select>
                    <input type="date" id="schoolStartDate" class="form-control mb-3" required>
                    <input type="date" id="schoolEndDate" class="form-control mb-3" required>
                    <button type="button" onclick="generateStudentsByDepartmentReport()" class="btn btn-primary w-100">Generate Report</button>
                </form>
                <div id="schoolDepartmentReportArea" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

   

<div class="feedback-container">
    <h4 class="feedback-header">Student Feedback</h4>
    
    <div class="feedback-card">
        <h4>Total Feedback</h4>
        <h2>
            <?php
            $result = $conn->query("SELECT COUNT(*) as count FROM feedback");
            $row = $result->fetch_assoc();
            echo $row['count'];
            ?>
        </h2>
        
        <h5>Recent Feedback</h5>
        <ul class="feedback-list">
            <?php
            $result = $conn->query("SELECT feedback_text FROM feedback ORDER BY feedback_text DESC LIMIT 3");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . $row['feedback_text'] . "</li>";
                }
            } else {
                echo "<li>No feedback available</li>";
            }
            ?>
        </ul>

        <a href="#" onclick="showAllFeedback()" class="btn btn-link view-feedback-btn">View All Feedback</a>
    </div>
</div>


    <footer>&copy; <?php echo date("Y"); ?> Lecturer-Student Appointment System | All Rights Reserved.</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const schoolSelect = document.getElementById('school');
    const schoolDepartmentSelect = document.getElementById('schoolDepartment'); // Updated ID

    const schoolDepartments = {
        'SPAS': ['Computing and Information Science', 'Biology and Chemistry', 'Biotechnology'],
        'School of Business': ['Accounting', 'Business Administration'],
        'SEA': ['Mechanical', 'Electrical'],
        'School of Education': ['Geography', 'English Literature']
    };

    schoolSelect.addEventListener('change', function () {
        console.log("School selected:", schoolSelect.value); // Debugging

        schoolDepartmentSelect.innerHTML = '<option value="">Select Department</option>';

        if (schoolDepartments[schoolSelect.value]) {
            schoolDepartments[schoolSelect.value].forEach(department => {
                let option = document.createElement('option');
                option.value = department;
                option.innerText = department;
                schoolDepartmentSelect.appendChild(option);
            });
        }

        console.log("Updated department options:", schoolDepartmentSelect.innerHTML); // Debugging
    });
});


function showAllFeedback() {
    const feedbackList = document.querySelector('.list-group');
    if (!feedbackList) return console.error("Feedback list element not found.");

    feedbackList.innerHTML = 'Loading feedback...';

    fetch('fetch_all_feedback.php')
        .then(response => response.text())
        .then(data => {
            feedbackList.innerHTML = data.trim() ?
                data.split('\n').map(fb => `<li class='list-group-item'>${fb}</li>`).join('')
                : "<li class='list-group-item'>No feedback available</li>";
        })
        .catch(error => {
            console.error('Error fetching feedback:', error);
            feedbackList.innerHTML = "<li class='list-group-item'>Error loading feedback. Please try again.</li>";
        });
}

    function generateLecturerAppointmentsReport() {
    const lecturerInput = document.getElementById("lecturerIdInput");
    const startDateInput = document.getElementById("lecturerStartDate");
    const endDateInput = document.getElementById("lecturerEndDate");
    const reportArea = document.getElementById("lecturerAppointmentReportArea");

    if (!reportArea) {
        console.error("Error: lecturerAppointmentReportArea element is missing in the HTML!");
        alert("Report area is missing in the HTML. Please check your code.");
        return;
    }

    reportArea.innerHTML = "Generating report...";

    const lecturerId = lecturerInput.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    console.log("Lecturer ID:", lecturerId);
    console.log("Start Date:", startDate);
    console.log("End Date:", endDate);

    if (!lecturerId || !startDate || !endDate) {
        alert("Please select all fields before generating the report.");
        return;
    }

    reportArea.innerHTML = "Loading report...";
    fetch(`fetch_lecturer_appointments.php?lecturerId=${encodeURIComponent(lecturerId)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`)
        .then(response => response.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            window.open(url, "_blank");
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error generating report.");
        });
}

function generateStudentsByDepartmentReport() {
    const school = document.getElementById('school').value;
    const department = document.getElementById('department').value;
    const startDate = document.getElementById('schoolStartDate').value;
    const endDate = document.getElementById('schoolEndDate').value;
    const reportArea = document.getElementById('schoolDepartmentReportArea');

    if (!reportArea) {
        console.error("Error: schoolDepartmentReportArea element is missing in the HTML!");
        alert("Report area is missing in the HTML. Please check your code.");
        return;
    }

    reportArea.innerHTML = "Generating report...";

    if (!school || !department || !startDate || !endDate) {
        alert("Please select all fields before generating the report.");
        return;
    }

    fetch(`fetch_students_dept.php?school=${encodeURIComponent(school)}&department=${encodeURIComponent(department)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`)
        .then(response => response.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            window.open(url, "_blank");
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error generating report.");
        });
}
</script>
</body>
</html>