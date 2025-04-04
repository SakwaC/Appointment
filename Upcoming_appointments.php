<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  

    <style>
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: black;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .back-button:hover {
            background-color: #333;
        }
         body{
            background-color: aliceblue;
         }

         .footer {
            margin-top: auto;
            width: 100%;
            background-color: skyblue;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            position: absolute;
            bottom: 0;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Dashboard.php'">Back</button>

    <div class="container mt-5">
        <h2 class="text-center text-primary">Upcoming Appointments</h2>
        <table class="table table-bordered mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>Appointment ID</th>
                    <th>Student Name</th>
                    <th>Lecturer Name</th>
                    <th>Lecturer Phone</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody id="appointmentsTable">
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script>
    $(document).ready(function() {
        const studentId = localStorage.getItem('student_id');
        const sessionId = localStorage.getItem('session_id');

        console.log("Student ID:", studentId);
        console.log("Session ID:", sessionId);

        if (!studentId || !sessionId) {
            console.warn("Missing credentials. Redirecting...");
            // window.location.href = 'student_login.html';
            return;
        }

        console.log("Fetching appointments for Student ID:", studentId);
        console.log("Fetching appointments for sessionId:", sessionId);

        $.ajax({
            url: "http://localhost/Appointments/get_upcoming_appointments.php",
            type: "GET",
            dataType: "json",
            xhrFields: { withCredentials: true },
            crossDomain: true,
            headers: {
                'X-Student-ID': studentId,
                'X-Session-ID': sessionId
            },
            success: function(response) {
                let tableBody = $("#appointmentsTable");
                tableBody.empty();

                console.log("Server Response:", response);

                if (response.error) {
                    tableBody.append(`<tr><td colspan='8' class='text-danger text-center'>${response.error}</td></tr>`);
                    return;
                }

                if (!Array.isArray(response) || response.length === 0) {
                    tableBody.append("<tr><td colspan='8' class='text-center'>No upcoming appointments found</td></tr>");
                    return;
                }

                response.forEach(appointment => {
                    let row = `
                        <tr>
                            <td>${appointment.Appointment_ID || 'N/A'}</td>
                            <td>${appointment.student_name || 'N/A'}</td>
                            <td>${appointment.lecturer_name || 'N/A'}</td>
                            <td>${appointment.lecturer_phone || 'N/A'}</td>
                            <td>${appointment.department || 'N/A'}</td>
                            <td>${appointment.appointment_date || 'N/A'}</td>
                            <td>${appointment.time_of_appointment || 'N/A'}</td>
                            <td>${appointment.appointment_description || 'N/A'}</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $("#appointmentsTable").append(`<tr><td colspan='8' class='text-danger text-center'>Error loading appointments</td></tr>`);
            }
        });
    });
    </script>

<footer class="footer">
    &copy; 2025 Kenyatta University. All rights reserved.
</footer>

</body>
</html>
