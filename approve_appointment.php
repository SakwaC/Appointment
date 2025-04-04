<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<style>
    h2 { text-align: center; color: blue; }
    label { font-weight: bold; }
    body { background-color: lightblue; }

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
    .footer {
        margin-top: auto;
        width: 100%;
        background-color: azure;
        padding: 10px;
        text-align: center;
        font-size: 14px;
        position: absolute;
        bottom: 0;
    }
</style>

<body>
    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='lecturer_dashboard.php'">Back</button>

    <div class="container mt-5">
        <h2>Approve Appointment</h2>
        <form id="appointmentForm">
            <div class="form-group">
                <label for="appointmentId">Appointment ID</label>
                <input type="text" class="form-control" id="appointmentId" name="appointment_id" placeholder="Enter Appointment ID" required readonly>
            </div>
            <div class="form-group">
                <label for="lecturerComments">Comments</label>
                <textarea class="form-control" id="lecturerComments" name="comments" rows="3" placeholder="Enter your comments here" required></textarea>
            </div>
            <div class="form-group">
                <label for="dateApproved">Approval Date</label>
                <input type="date" class="form-control" id="dateApproved" name="date_approved" required>
            </div>
            <input type="hidden" id="status" name="status">
            <div class="text-center">
                <button type="button" class="btn btn-success" onclick="setStatus('approved')">Approve</button>
                <button type="button" class="btn btn-danger" onclick="setStatus('rejected')">Reject</button>
            </div>
        </form>
    </div>

    <script>
        // Extract appointment ID from the URL and set it in the input field
        document.addEventListener("DOMContentLoaded", function() {
            let urlParams = new URLSearchParams(window.location.search);
            let appointmentId = urlParams.get("id");

            if (appointmentId) {
                document.getElementById("appointmentId").value = appointmentId;
            }

            // Allow only today or future dates in the date picker
            let today = new Date().toISOString().split("T")[0];
            document.getElementById("dateApproved").setAttribute("min", today);
        });

        function setStatus(status) {
            $("#status").val(status);
            submitForm();
        }

        function submitForm() {
            var formData = $("#appointmentForm").serialize();

            $.ajax({
                type: "POST",
                url: "http://localhost/Appointments/approve_back.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        alert('Appointment successfully processed!');
                        window.location.href = 'Lecturer_dashboard.php';
                    } else {
                        alert('Failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    alert("An error occurred while processing the request.");
                }
            });
        }
    </script>
    <div class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </div>
</body>
</html>
