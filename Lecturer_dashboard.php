<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
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
        <h1 class="m-0">Lecturer Dashboard</h1>
        <div class="d-flex align-items-center">
            <img src="notification.png" alt="Notification Bell" width="30" height="30">
            <span class="ml-2">Notification</span>
        </div>
        <div><a href="land_in.php" class="text-white">Log out</a></div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="lecturer_upcoming_appointments.php">Upcoming Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="Booked_appointment.php">booked_appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="approve_appointment.php">Approve Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="appointments" id="upcoming-appointments-container">
                    <h2>Upcoming Appointments</h2>
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
    const lecturerId = localStorage.getItem('lecturer_id');
    console.log("Lecturer ID from local storage:", lecturerId);

    if (lecturerId) {
        console.log("Fetch URL:", 'lecturer_dashboard_data.php?lecturer_id=' + lecturerId);
        fetch('lecturer_dashboard_data.php?lecturer_id=' + lecturerId)
            .then(response => response.json())
            .then(data => {
                const upcomingContainer = document.getElementById('upcoming-appointments-container');
                upcomingContainer.innerHTML = `
                    <h2>Upcoming Appointments</h2>
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" onclick="location.href='approve_appointment.php';">
                            Approve Appointments
                        </button>
                    </div>
                `;

                if (data && data.appointments && Array.isArray(data.appointments)) {
                    if (data.appointments.length === 0) {
                        upcomingContainer.innerHTML += `<p class="text-center text-muted mt-3">No upcoming appointments.</p>`;
                    } else {
                        data.appointments.forEach(appointment => {
                            const appointmentDiv = document.createElement('div');
                            appointmentDiv.classList.add('appointments', 'p-2', 'mb-2', 'border', 'rounded');
                            appointmentDiv.innerHTML = `
                                <div class="appointment-title font-weight-bold">${appointment.Description}</div>
                                <div>Date: ${appointment.appointment_date}</div>
                                <div>Time: ${appointment.time_of_appointment}</div>
                                <div>Student Name: ${appointment.Name}</div>
                                <div>Status: ${appointment.status}</div>
                                <div>Student Contact: ${appointment.Contact_No}</div>
                                
                            `;

                            upcomingContainer.appendChild(appointmentDiv);
                        });
                    }
                } else {
                    upcomingContainer.innerHTML += `<p class="text-danger">Failed to load appointments data.</p>`;
                    if (data && data.error) {
                        console.error("PHP Error:", data.error);
                    }
                }

                const statsContainer = document.getElementById('quick-stats-container');
                statsContainer.innerHTML = `<h5>Quick Stats</h5>`;

                if (data) {
                    statsContainer.innerHTML += `
                        <div class="stat my-2">Upcoming Appointments: <strong>${data.upcomingCount !== undefined ? data.upcomingCount : 'N/A'}</strong></div>
                        <div class="stat my-2">Pending Appointments: <strong>${data.pendingCount !== undefined ? data.pendingCount : 'N/A'}</strong></div>
                        <div class="stat my-2">Cancelled Appointments: <strong>${data.cancelledCount !== undefined ? data.cancelledCount : 'N/A'}</strong></div>
                    `;
                } else {
                    statsContainer.innerHTML += `<p class="text-danger">Failed to load stats data.</p>`;
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                document.getElementById('upcoming-appointments-container').innerHTML += `<p class="text-danger">Failed to load data.</p>`;
            });
    } else {
        console.error("Lecturer ID not found in local storage.");
        document.getElementById('upcoming-appointments-container').innerHTML += `<p class="text-danger">Lecturer ID missing.</p>`;
    }
</script>
   
</body>
</html>