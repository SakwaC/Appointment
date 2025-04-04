<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Appointments</title>
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
        <h2 class="text-center text-primary">Past Appointments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Lecturer Name</th>
                    <th>Lecturer Contact</th>
                    <th>Department</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody id="appointTable">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            function loadPastAppointments() {
                $.ajax({
                    url: "http://localhost/Appointments/get_past_appointment.php",
                    method: "GET",
                    dataType: "json",
                    headers: {
                        'X-Student-ID': localStorage.getItem('student_id'),
                        'X-Session-ID': localStorage.getItem('session_id')
                    },
                    success: function(response) {
                        let tableBody = $("#appointTable");
                        tableBody.empty();
                        
                        const appointments = response.data || [];
                        
                        if (appointments.length === 0) {
                            console.log("No appointments found");
                            tableBody.append("<tr><td colspan='6' class='text-center'>No past appointments</td></tr>");
                            return;
                        }
                        appointments.forEach((appointment) => {
                            let row = `<tr>
                                <td>${appointment.lecturer_name || 'N/A'}</td>
                                <td>${appointment.lecturer_contact || 'N/A'}</td>
                                <td>${appointment.Department || 'N/A'}</td>
                                <td>${appointment.appointment_date || 'N/A'}</td>
                                <td>${appointment.time_of_appointment || 'N/A'}</td>
                                <td>${appointment.Description || 'N/A'}</td>
                            </tr>`;
                            tableBody.append(row);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log("Response:", xhr.responseText);
                        console.error("AJAX error:", status, error);
                        $("#appointTable").append("<tr><td colspan='6' class='text-center text-danger'>Failed to load appointments</td></tr>");
                    }
                });
            }

            loadPastAppointments();
        });
    </script>
    <div class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>
</body>
</html>
