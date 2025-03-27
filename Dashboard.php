<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Dashboard</title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: aliceblue;
        }
        .sidebar {
            background-color: lightblue;
            padding: 10px;
            height: 100vh;
        }
        .sidebar .nav-link {
            color: black;
            font-weight: bold;
        }
        .sidebar .nav-link:hover {
            color: #007bff;
        }
        .appointments, .quick-stats {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: lightblue;
            padding: 10px;
            margin-top: 20px;
        }
        .footer div {
            flex: 1;
            text-align: center;
        }
        h5{
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header d-flex justify-content-between align-items-center bg-info text-white p-3">
        <img src="Ku_logo.jpeg" alt="logo" width="50" height="40">
        <h1>Appointments Dashboard</h1>

        
        <div><a href="land_in.php" class="text-white">Log out</a></div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="Dashboard.php">Dashboard Overview</a></li>
                    <li class="nav-item"><a class="nav-link" href="Upcoming_appointments.php">Upcoming Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="past_appointment.php">Past Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="Create_appointment.php">Book Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="Feedback.php">Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="appointments" id="upcoming-appointments-container">
                    <h2>Upcoming Appointments</h2>
                    <div class="text-center mt-3">
                        <button id="add-appointment" class="btn btn-primary" onclick="location.href='create_appointment.php';">Add Appointment</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-lg-4">
                <div class="quick-stats" id="quick-stats-container">
                    <h5>Quick Stats</h5>
                </div>
            </div>
        </div>

        <div class="footer">
            <div><strong>Contact:</strong> appointment.ku.ac.ke</div>
            <div><strong>Privacy Policy:</strong> <a href="#">View Here</a></div>
            <div><strong>© 2025 Appointment System. All rights reserved.</strong></div>
        </div>
    </div>

    <script>
        // Retrieve student ID from local storage
        const studentId = localStorage.getItem('student_id'); 
        console.log("Retrieved Student ID:", studentId);

        if (studentId) {
            // Fetch appointment data from the server
            fetch(`get_Dashboard_data.php?student_id=${studentId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("API Response:", data);

                    const upcomingContainer = document.getElementById('upcoming-appointments-container');
                    upcomingContainer.innerHTML = `
                        <h2>Upcoming Appointments</h2>
                        <div class="text-center mt-3">
                            <button id="add-appointment" class="btn btn-primary" onclick="location.href='create_appointment.php';">Add Appointment</button>
                        </div>
                    `;

                    if (!data.appointments || data.appointments.length === 0) {
                        upcomingContainer.innerHTML += `<p class="text-center text-muted mt-3">No upcoming appointments.</p>`;
                    } else {
                        data.appointments.forEach(appointment => {
                            upcomingContainer.appendChild(createAppointmentDiv(appointment));
                        });
                    }

                    // Update Quick Stats
                    const statsContainer = document.getElementById('quick-stats-container');
                    statsContainer.innerHTML = `<h5>Quick Stats</h5>`;

                    if (data.upcomingCount !== undefined) {
                        statsContainer.innerHTML += `
                            <div class="stat my-2">Upcoming Appointments: <strong>${data.upcomingCount || 0}</strong></div>
                            <div class="stat my-2">Completed Appointments: <strong>${data.completedCount || 0}</strong></div>
                            <div class="stat my-2">Pending Appointments: <strong>${data.pendingCount || 0}</strong></div>
                            <div class="stat my-2">Cancelled Appointments: <strong>${data.cancelledCount || 0}</strong></div>
                        `;
                    } else {
                        statsContainer.innerHTML += `<p class="text-danger">Error fetching stats.</p>`;
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    document.getElementById('upcoming-appointments-container').innerHTML += `<p class="text-danger">Failed to load appointments.</p>`;
                    document.getElementById('quick-stats-container').innerHTML += `<p class="text-danger">Failed to load stats.</p>`;
                });
        } else {
            document.getElementById('upcoming-appointments-container').innerHTML += `<p class="text-danger">Student ID missing.</p>`;
        }

        // Function to create an appointment card
        function createAppointmentDiv(appointment) {
         const appointmentDiv = document.createElement('div');
          appointmentDiv.classList.add('appointments', 'p-2', 'mb-2', 'border', 'rounded');
          appointmentDiv.innerHTML = `
        <div class="appointment-title font-weight-bold">${sanitizeHTML(appointment?.Description || 'No Description')}</div>
        
        <div><strong>Date:</strong> ${appointment?.appointment_date || 'N/A'}</div>
        <div><strong>Time:</strong> ${appointment?.time_of_appointment || 'N/A'}</div>
        <div><strong>Lecturer Name:</strong> ${sanitizeHTML(appointment?.lecturer_name || 'N/A')}</div>
        <div><strong>Status:</strong> ${appointment?.status || 'N/A'}</div>
        <div><strong>Lecturer Contact:</strong> ${appointment?.Contact_No || 'N/A'}</div>
        <div><strong>Lecturer Comments:</strong> ${sanitizeHTML(appointment?.Comments || 'No Comments')}</div>
        `;
         return appointmentDiv;
        }


        // Function to sanitize input
        function sanitizeHTML(str) {
            const temp = document.createElement('div');
            temp.textContent = str;
            return temp.innerHTML;
        }
    </script>

</body>
</html>
