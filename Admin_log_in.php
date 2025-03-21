<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body { background-color: skyblue; }
    .login-container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    h3 { text-decoration: underline; }
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
    .logo { position: absolute; top: 20px; left: 20px; }
</style>
<body>
<div class="logo">
        <img src="Ku_logo.jpeg" alt="System Logo" width="100">
    </div>
    <button class="back-button" onclick="window.location.href='land_in.php'">Back</button>
    <div class="container">
        <div class="login-container">
            <h3 class="text-center mb-4">Admin Login</h3>
            <p id="feedback"></p>
            <form id="adminLoginForm">
                <div class="form-group">
                    <label for="adminId">Admin ID</label>
                    <input type="text" class="form-control" id="adminId" name="adminId" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Log In</button>
                <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a></p>
            </form>
        </div>
    </div>

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
                            <label for="resetAdminId">Admin ID</label>
                            <input type="text" class="form-control" id="resetAdminId" name="adminId" required>
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
    $('#adminLoginForm').submit(function (event) {
        event.preventDefault();

        var adminIdInput = $('#adminId').val().trim();

        // Input Validation: Check if adminIdInput is a valid number
        if (!/^\d+$/.test(adminIdInput)) {
            $("#feedback").text("Admin ID must be a number.").css("color", "red").show();
            return; // Stop the form submission
        }

        console.log("Admin ID (before parseInt):", adminIdInput);
        console.log("Admin ID (after parseInt):", parseInt(adminIdInput, 10));
        console.log("Type of Admin ID:", typeof parseInt(adminIdInput, 10));

        $.ajax({
            type: "POST",
            url: "http://localhost/Appointments/Admin_log_in(back).php",
            data: {
                admin_ID: parseInt(adminIdInput, 10), // Use the validated input
                password: $('#password').val().trim()
            },
            success: function (response) {
                if (response.success) {
                    window.location.href = "Admin_dashboard.php";
                } else {
                    $("#feedback").text(response.error).css("color", "red").show();
                }
            },
            error: function (xhr, status, error) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    $("#feedback").text(response.error).css("color", "red").show();
                } catch (e) {
                    console.error("Error parsing JSON:", xhr.responseText, e);
                    $("#feedback").text("An unexpected error occurred.").css("color", "red").show();
                }
            }
        });
    });

    $('#forgotPasswordForm').submit(function (event) {
    event.preventDefault();

    const adminId = $('#resetAdminId').val().trim();
    const newPassword = $('#newPassword').val().trim();

    const dataToSend = {
        admin_ID: adminId,
        new_password: newPassword,
    };

    console.log("Data to send:", dataToSend);
    console.log("JSON string:", JSON.stringify(dataToSend));

    $.ajax({
        type: "POST",
        url: "http://localhost/Appointments/Admin_forgot_password.php",
        contentType: "application/json",
        data: JSON.stringify(dataToSend),
        success: function (response) {
            console.log("Success response:", response);
            alert(response.message);
            if (response.status === "success") {
                $('#forgotPasswordModal').modal('hide');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error response:", xhr);
            try {
                const response = JSON.parse(xhr.responseText);
                alert(response.message);
            } catch (e) {
                console.error("Error parsing JSON:", xhr.responseText, e);
                alert("An unexpected error occurred.");
            }
        }
    });
});
});
</script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
