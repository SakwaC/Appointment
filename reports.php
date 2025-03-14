<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Generation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-top: 20px;
            color: #007bff; 
        }
        label {
            font-weight: bold;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }
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
        body {
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

    <div class="container">
        <h1>Generate Student Report</h1>
        <form id="reportForm">
            <div class="form-group">
                <label for="startDate">Start Date:</label>
                <input type="date" class="form-control" id="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">End Date:</label>
                <input type="date" class="form-control" id="endDate" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>
        <div id="responseMessage" class="mt-3 text-center"></div>
    </div>

    <script>
        $(document).ready(function () {
            $("#reportForm").submit(function (e) {
                e.preventDefault();

                let startDate = $("#startDate").val();
                let endDate = $("#endDate").val();

                if (!startDate || !endDate) {
                    $("#responseMessage").html('<span class="text-danger">Please select a valid date range.</span>');
                    return;
                }

                $.ajax({
                    url: "generate_report.php",
                    type: "POST",
                    data: { startDate: startDate, endDate: endDate },
                    xhrFields: {
                        responseType: "blob" // Handle binary PDF file
                    },
                    success: function (data, status, xhr) {
                        let blob = new Blob([data], { type: "application/pdf" });
                        let link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "student_report.pdf";
                        link.click();
                        $("#responseMessage").html('<span class="text-success">Report generated successfully.</span>');
                    },
                    error: function () {
                        $("#responseMessage").html('<span class="text-danger">Failed to generate report.</span>');
                    }
                });
            });
        });
    </script>
    <footer class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>
</body>
</html>
