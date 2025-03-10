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
            background-color: #f8f9fa;
        }
        .dashboard-container {
            padding: 20px;
        }
        .card {
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .chart-container {
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include 'db2_connect.php'; ?>
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
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('fetch_chart_data.php')
            .then(response => response.json())
            .then(data => {
                var ctx1 = document.getElementById('departmentChart').getContext('2d');
                var departmentChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: ['CS', 'IT', 'Math', 'Physics', 'Engineering'],
                        datasets: [{
                            label: 'Registered Students',
                            data: data.departmentData,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)'
                        }]
                    }
                });

                var ctx2 = document.getElementById('lecturerAvailabilityChart').getContext('2d');
                var lecturerAvailabilityChart = new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: ['Available', 'Busy'],
                        datasets: [{
                            data: [data.lecturerAvailability.available, data.lecturerAvailability.busy],
                            backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(255, 99, 132, 0.5)']
                        }]
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });
</script>

</body>
</html>
