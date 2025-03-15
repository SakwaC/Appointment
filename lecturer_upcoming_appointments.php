<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: azure;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: lightblue;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: black !important;
            color: white !important;
        }
        .back-button:hover {
            background-color: darkgray !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5 position-relative">
        <a href="lecturer_dashboard.php" class="btn back-button">Back</a>
        <h2 class="mb-4 text-center">Upcoming Appointments</h2>
        <table class="table table-striped table-bordered" id="appointmentsTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
    <footer class="footer">
        &copy; 2025 Lecturer Appointment System. All Rights Reserved.
    </footer>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Retrieve lecturer_id from localStorage
        const lecturerId = localStorage.getItem('lecturer_id');

        if (!lecturerId) {
            console.error('Lecturer ID not found in localStorage. Redirecting to login.');
            window.location.href = 'login.php'; // Redirect if no ID
            return; // Stop further execution
        }

        // Fetch appointments with lecturer_id as a query parameter
        fetch(`Upcoming_appointment_data.php?lecturer_id=${lecturerId}`)
            .then(response => {
                if (!response.ok) {
                    // Handle HTTP errors (e.g., 404, 500)
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const tableBody = document.querySelector('#appointmentsTable tbody');
                tableBody.innerHTML = ''; // Clear existing table rows

                if (data && Array.isArray(data)) {
                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="4">No appointments found.</td></tr>';
                    } else {
                        data.forEach(appointment => {
                            const row = tableBody.insertRow();
                            row.insertCell().textContent = appointment.student_name;
                            row.insertCell().textContent = appointment.appointment_date;
                            row.insertCell().textContent = appointment.time_of_appointment;
                            row.insertCell().textContent = appointment.Description;
                        });
                    }
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4">No appointments found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching appointments:', error);
                const tableBody = document.querySelector('#appointmentsTable tbody');
                tableBody.innerHTML = '<tr><td colspan="4">Error loading appointments.</td></tr>';
            });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>