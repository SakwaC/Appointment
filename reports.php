<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Generation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: aliceblue;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-top: 20px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .container {
            max-width: 500px;
            margin-top: 40px;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input {
            margin-bottom: 15px;
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
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #333;
        }
        footer {
            background-color: skyblue;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .loading {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Dashboard.php'">Back</button>

    <div class="container">
        <h1>Generate Student Report</h1>
        <form id="reportForm">
            <label for="studentId">Student ID:</label>
            <input type="text" name="studentId" id="studentId" class="form-control" required>

            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" id="startDate" class="form-control" required>

            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" id="endDate" class="form-control" required>

            <button type="submit" class="submit-button">Generate Report</button>
            <div id="responseMessage" class="mt-3"></div>
            <div id="loadingIndicator" class="loading" style="display: none;">Generating Report...</div>
        </form>
    </div>

    <footer>
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>

    <script>
    $(document).ready(function () {
        $("#reportForm").submit(function (e) {
            e.preventDefault();

            let studentId = $("#studentId").val();
            let startDate = $("#startDate").val();
            let endDate = $("#endDate").val();

            if (!studentId || !startDate || !endDate) {
                $("#responseMessage").html('<span class="text-danger">Please fill all fields.</span>');
                return;
            }

            if (startDate > endDate) {
                $("#responseMessage").html('<span class="text-danger">Start Date must be before End Date.</span>');
                return;
            }

            $("#loadingIndicator").show();
            $("#responseMessage").html('');

            $.ajax({
                url: "generate_report.php",
                type: "POST",
                data: { studentId: studentId, startDate: startDate, endDate: endDate },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data) {
                    $("#loadingIndicator").hide();

                    // Create a blob URL and trigger download or inline view
                    const blob = new Blob([data], { type: 'application/pdf' });
                    const url = URL.createObjectURL(blob);

                    // Open the PDF in a new tab or trigger download
                    window.open(url, '_blank'); // Open in a new tab

                    // Optionally, you can trigger a download directly
                    // let a = document.createElement('a');
                    // a.href = url;
                    // a.download = 'student_report.pdf';
                    // a.style.display = 'none';
                    // document.body.appendChild(a);
                    // a.click();
                    // document.body.removeChild(a);

                },
                error: function () {
                    $("#loadingIndicator").hide();
                    $("#responseMessage").html('<span class="text-danger">Failed to generate report. Check if the Student ID and dates are correct.</span>');
                }
            });
        });
    });
</script>
</body>
</html>