<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Dashboard</title>
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
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 10px;
            background-color: lightblue;
            height: auto;
            min-height: 100%;
            margin-top: 20px;
        }
        .sidebar .nav-link {
            color: black;
            font-weight: bold;
        }
        .sidebar .nav-link:hover {
            color: #007bff;
        }
        .appointments {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .quick-stats .stat {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: lightblue;
            padding: 10px;
            margin-top: 210px;
        }
        .footer div {
            flex: 1;
            text-align: center;
            padding: 8px;
            font-weight: normal;
        }
        .footer div:not(:last-child) {
            border-right: 2px solid black;
        }
        h5 {
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
                    <li class="nav-item"><a class="nav-link" href="Create_appointment.php">Create Appointment</a></li>
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
        fetch('get_Dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                const upcomingContainer = document.getElementById('upcoming-appointments-container');
                upcomingContainer.innerHTML = `
                    <h2>Upcoming Appointments</h2>
                    <div class="text-center mt-3">
                        <button id="add-appointment" class="btn btn-primary" onclick="location.href='create_appointment.php';">Add Appointment</button>
                    </div>
                `;

                if (data.upcoming_appointments.length === 0) {
                    // If no upcoming appointments, show a message
                    upcomingContainer.innerHTML += `<p class="text-center text-muted mt-3">No upcoming appointments.</p>`;
                } else {
                    data.upcoming_appointments.forEach(appointment => {
                        const appointmentDiv = document.createElement('div');
                        appointmentDiv.classList.add('appointments', 'p-2', 'mb-2', 'border', 'rounded');
                        appointmentDiv.innerHTML = `
                            <div class="appointment-title font-weight-bold">${appointment.appointment_title}</div>
                            <div>Date & Time: ${appointment.appointment_date}</div>
                            <div>Lecturer Name: ${appointment.lecturer_name}</div>
                            <div>Status: ${appointment.status}</div>
                            <div>Lecturer Contact: ${appointment.lecturer_contact}</div>
                            <div>Lecturer Comments: ${appointment.lecturer_comments}</div>
                        `;
                        upcomingContainer.appendChild(appointmentDiv);
                    });
                }

                const statsContainer = document.getElementById('quick-stats-container');
                statsContainer.innerHTML = `<h5>Quick Stats</h5>`;
                if (data.quick_stats) {
                    statsContainer.innerHTML += `
                        <div class="stat my-2">Upcoming Appointments: <strong>${data.quick_stats.upcoming}</strong></div>
                        <div class="stat my-2">Completed Appointments: <strong>${data.quick_stats.completed}</strong></div>
                        <div class="stat my-2">Pending Appointments: <strong>${data.quick_stats.pending}</strong></div>
                        <div class="stat my-2">Cancelled Appointments: <strong>${data.quick_stats.cancelled}</strong></div>
                    `;
                } else {
                    statsContainer.innerHTML += `<p class="text-danger">Error fetching stats.</p>`;
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                document.getElementById('upcoming-appointments-container').innerHTML += `<p class="text-danger">Failed to load data.</p>`;
            });
    </script>
</body>
</html>