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
            background-color: #ffffff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 40px;
            margin-bottom: 40px;
            border: 1px solid #007bff;
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

        <div class="report-section">
            <h4 class="text-center text-primary">Generate Reports</h4>
            <form action="generate_admin_report.php" method="POST" class="text-center">
                <select name="report_type" class="form-control mb-3" required>
                    <option value="">Select Report Type</option>
                    <option value="appointments">Appointments Report</option>
                    <option value="feedback">Feedback Report</option>
                    <option value="lecturers">Lecturers and Departments Report</option>
                    <option value="students">Registered Students Report</option>
                </select>
                <input type="date" name="start_date" class="form-control mb-3" required>
                <input type="date" name="end_date" class="form-control mb-3" required>
                <button type="submit" class="btn btn-primary">Download Report</button>
            </form>
        </div>

        <div class="mt-4">
            <h4 class="text-center text-primary">Student Feedback</h4>
            <div class="card text-center p-3">
                <h4>Total Feedback</h4>
                <h2>
                    <?php 
                    $result = $conn->query("SELECT COUNT(*) as count FROM feedback");
                    $row = $result->fetch_assoc();
                    echo $row['count'];
                    ?>
                </h2>
                <h5>Recent Feedback</h5>
                <ul class="list-group">
                    <?php
                    $result = $conn->query("SELECT feedback_text FROM feedback ORDER BY feedback_text DESC LIMIT 3");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='list-group-item'>" . $row['feedback_text'] . "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No feedback available</li>";
                    }
                    ?>
                </ul>
                <a href="#" onclick="showAllFeedback()" class="btn btn-link">View All Feedback</a>
            </div>
        </div>
    </div>

    <footer>&copy; <?php echo date("Y"); ?> Lecturer-Student Appointment System | All Rights Reserved.</footer>

    <script>
        function showAllFeedback() {
            const feedbackList = document.querySelector('.list-group');
            feedbackList.innerHTML = '';

            fetch('fetch_all_feedback.php')
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === '') {
                        feedbackList.innerHTML = "<li class='list-group-item'>No feedback available</li>";
                    } else {
                        const feedbackArray = data.split('\n');
                        feedbackArray.forEach(feedback => {
                            if (feedback.trim()) {
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item';
                                listItem.textContent = feedback;
                                feedbackList.appendChild(listItem);
                            }
                        });
                    }
                })
                .catch(error => console.error('Error fetching feedback:', error));
        }
    </script>
</body>
</html>
