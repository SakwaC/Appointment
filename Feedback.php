<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: lightblue;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
            position: relative;
        }
        h1 {
            color: #343a40;
        }
        .btn-submit {
            background-color: blue;
            border-color: blue;
        }
        .btn-submit:hover {
            background-color: darkblue;
            border-color: darkblue;
        }
        label {
            font-weight: bold;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: black;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #333;
        }
        
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Dashboard.php'">Back</button>
    <div class="container">
        <h1 class="text-center">Feedback</h1>
        <form id="feedbackForm">
            <div class="form-group">
                <label for="Student_ID">Student ID</label>
                <input type="text" class="form-control" id="Student_ID" name="Student_ID" placeholder="Student ID" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="feedback_text">Feedback</label>
                <textarea class="form-control" id="feedback_text" name="feedback_text" rows="3" placeholder="Enter your Feedback here" required></textarea>
            </div>
            <div class="form-group">
                <label for="feedback_date">Feedback Date</label>
                <input type="date" class="form-control" id="feedback_date" name="feedback_date" required min="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-submit">Submit</button>
            </div>
        </form>
        <div id="responseMessage" class="mt-3"></div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#feedbackForm').submit(function(e) {
                e.preventDefault(); // Prevent standard form submission

                $.ajax({
                    type: 'POST',
                    url: 'feed_db.php', // Correct URL to your PHP script
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#responseMessage').removeClass('alert-danger alert-success');
                        if (response.status === 'success') {
                            $('#responseMessage').addClass('alert alert-success').text(response.message);
                            $('#feedbackForm')[0].reset(); // Clear the form
                        } else {
                            $('#responseMessage').addClass('alert alert-danger').text(response.message);
                        }
                    },
                    error: function() {
                        $('#responseMessage').addClass('alert alert-danger').text('An error occurred.');
                    }
                });
            });
        });
    </script>
</body>
</html>