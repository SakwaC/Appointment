<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }
        .chart-container {
            padding: 20px;
        }
        .navbar .logout-btn {
            position: absolute;
         right: 20px;
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">
            <img src="Ku_logo.jpeg" alt="Logo">
            Admin Dashboard
        </a>
        <div class="ml-auto">
        <a href="Admin_land_in.php" class="btn btn-dark">Log out</a>
        </div>

    </nav>

    <div class="container-fluid dashboard-container">
        <h2 class="text-center text-primary">Admin Dashboard</h2>
        <div class="row">
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

        <div class="row mt-4">
            <div class="col-md-6 chart-container">
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <canvas id="lecturerAvailabilityChart"></canvas>
            </div>
        </div>

        <!-- Feedback Section -->
        <div class="mt-4">
            <h4 class="text-center text-primary">Student Feedback</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center p-3">
                        <h4>Total Feedback</h4>
                        <h2>
                            <?php 
                            $result = $conn->query("SELECT COUNT(*) as count FROM feedback");
                            $row = $result->fetch_assoc();
                            echo $row['count'];
                            ?>
                        </h2>
                    </div>
                </div>
            </div>

            <table class="table table-bordered mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody id="feedbackTable">
                    <?php
                    $feedbackQuery = "SELECT feedback_text FROM feedback LIMIT 3"; // Show only first 3 initially
                    $result = $conn->query($feedbackQuery);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>{$row['feedback_text']}</td></tr>";
                        }
                    } else {
                        echo "<tr><td class='text-center text-danger'>No feedback found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="text-center">
                <button class="btn btn-primary" id="viewAllBtn">View All</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; <?php echo date("Y"); ?> Lecturer-Student Appointment System | All Rights Reserved.
    </footer>

    <script>
    document.getElementById("viewAllBtn").addEventListener("click", function() {
        fetch("fetch_all_feedback.php") // Fetch all feedback messages via AJAX
            .then(response => response.text())
            .then(data => {
                document.getElementById("feedbackTable").innerHTML = data;
                document.getElementById("viewAllBtn").style.display = "none"; // Hide button after clicking
            })
            .catch(error => console.error("Error fetching feedback:", error));
    });

    // Lecturer Availability Chart
    fetch("fetch_lecturer_schedule.php")
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('lecturerAvailabilityChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.days,
                    datasets: [{
                        label: 'Available Hours',
                        data: data.hours,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error fetching lecturer schedule  data:", error));
    </script>
</body>
</html>
