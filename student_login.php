<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>
<style>
    body {
        background-color: lightblue;
    }
    .login-container {
        max-width: 400px;
        margin: 100px auto; 
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    h3 {
        text-decoration: underline;
    }
    label {
        font-weight: bold;
    }
    .logo {
        position: absolute;
        top: 20px;
        left: 20px;
    }
    p {
        text-align: center;
    }
    #feedback {
        display: none;
        text-align: center;
        color: red;
        font-weight: bold;
    }
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
</style>
<body>
    <div class="logo">
        <img src="Ku_logo.jpeg" alt="System Logo" width="100">
    </div>

    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='land_in.php'">Back</button>

    <div class="container">
        <div class="login-container">
            <h3 class="text-center mb-4">Student Login</h3>
            <p id="feedback"></p> <!-- Feedback message area -->
            <form id="loginForm">
                <div class="form-group">
                    <label for="idNumber">ID Number</label>
                    <input type="text" class="form-control" id="idNumber" name="Student_ID" placeholder="Enter ID Number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Log In</button>
                <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a></p>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm">
                        <div class="form-group">
                            <label for="resetId">Student ID</label>
                            <input type="text" class="form-control" id="resetId" name="Student_ID" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#loginForm').submit(function (event) {
                event.preventDefault();
                var formData = {
                    Student_ID: $('#idNumber').val().trim(),
                    password: $('#password').val().trim()
                };
                $.ajax({
                    type: "POST",
                    url: "http://localhost/Appointments/student_login(back).php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        console.log("Login Response:", response);
                        if(response.status === "success") {
                            console.log("Session ID:", response.session_id); // Check session ID
                            localStorage.setItem('student_id', response.student_id);
                            localStorage.setItem('session_id', response.session_id);
                            console.log("Stored Session ID:", localStorage.getItem('session_id')); 
                            window.location.href = "Dashboard.php";
                        } else {
                            $("#feedback").text(response.message).css("color", "red").show();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        try {
                            var response = JSON.parse(xhr.responseText);
                            $("#feedback").text(response.message).css("color", "red").show();
                        } catch (e) {
                            $("#feedback").text("An unexpected error occurred. Please try again.").css("color", "red").show();
                        }
                    }
                });
            });

            $('#forgotPasswordForm').submit(function (event) {
                event.preventDefault();
                var resetData = JSON.stringify({
                    student_id: $('#resetId').val().trim(),
                    new_password: $('#newPassword').val().trim()
                });

                $.ajax({
                    type: "POST",
                    url: "http://localhost/Appointments/reset_password.php",
                    data: resetData,
                    contentType: "application/json",
                    dataType: "json",
                    success: function (response) {
                        alert(response.message);
                        if (response.status === "success") {
                            $('#forgotPasswordModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("An error occurred.");
                    }
                });
            });
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

