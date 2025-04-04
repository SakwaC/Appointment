<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .back-button {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: black;
        color: white;
        padding: 5px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
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

        
        .confirm-button {
    position: absolute;
    top: 50%;
    right: -90px; /* Adjusted right value to increase space */
    transform: translateY(-50%);
    background-color: #4CAF50;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.confirm-button:hover {
    background-color: #3e8e41;
}

/* Add a margin to the description text */
td[style="position: relative;"] {
    padding-right: 90px; /* Matches the right value of the button */
}
</style>

    
</head>
<body>
     <!-- Back Button -->
     <button class="back-button" onclick="window.location.href='lecturer_dashboard.php'">Back</button>

    <div class="container mt-4">
        <h2 class="text-center text-primary">Booked Appointments</h2>
        <table class="table table-bordered mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>Appointment ID</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody id="appointTable">
                <tr><td colspan="7" class="text-center">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <script>
    function goToApprovePage(appointmentId) {
        let url = "approve_appointment.php?id=" + encodeURIComponent(appointmentId);
        console.log("Redirecting to:", url);
        window.location.href = url;
    }

    $(document).ready(function () {
        const sessionId = localStorage.getItem('session_id');
        const lecturerId = localStorage.getItem('lecturer_id');
        console.log("Current Lecturer ID:", lecturerId);

        if (!sessionId || !lecturerId) {
            console.warn("Session or Lecturer ID is missing. Redirecting to login...");
            window.location.href = 'lecturer_login.html';
            return;
        }

        function refreshBookedAppointments() {
            console.log("Fetching booked appointments...");
            $.ajax({
                url: "http://localhost/Appointments/get_booked_appointment.php",
                type: "GET",
                dataType: "json",
                xhrFields: {
                    withCredentials: true
                },
                headers: {
                    'X-Session-ID': sessionId,
                    'X-Lecturer-ID': lecturerId
                },
                success: function(response) {
                    console.log("API Response:", response);

                    let appointTable = $("#appointTable");
                    appointTable.empty();

                    if (!Array.isArray(response)) {
                        let message = response.message || "Error fetching data";
                        console.warn("API Error Message:", message);
                        appointTable.append(`<tr><td colspan='8' class='text-center text-danger'>${message}</td></tr>`);
                        return;
                    }

                    if (response.length === 0) {
                        appointTable.append("<tr><td colspan='8' class='text-center text-danger'>No pending appointments</td></tr>");
                        return;
                    }

                    response.forEach(appointment => {
                        console.log("Processing appointment:", appointment);

                        let appointmentId = appointment.Appointment_ID || 'N/A';
                        let studentId = appointment.student_id || 'N/A';
                        let studentName = appointment.student_name || 'N/A';
                        let department = appointment.department || 'N/A';
                        let appointmentDate = appointment.appointment_date || 'N/A';
                        let appointmentTime = appointment.time_of_appointment || 'N/A';
                        let description = appointment.Description || 'N/A';

                        let row = `<tr>
                            <td>${appointmentId}</td>
                            <td>${studentId}</td>
                            <td>${studentName}</td>
                            <td>${department}</td>
                            <td>${appointmentDate}</td>
                            <td>${appointmentTime}</td>
                            <td style="position: relative;">
                                ${description}
                                <button class="confirm-button" onclick="goToApprovePage('${encodeURIComponent(appointmentId)}')">Confirm</button>
                            </td>
                        </tr>`;

                        appointTable.append(row);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText || error);
                    $("#appointTable").html(`<tr><td colspan='8' class='text-center text-danger'>Error fetching data. Please try again.</td></tr>`);
                }
            });
        }

        refreshBookedAppointments();
    });
</script>


    <div class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>
</body>
</html>
