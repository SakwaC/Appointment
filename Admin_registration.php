<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body { background-color: lightblue; }
    .register-container { max-width: 400px; margin: 100px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); position: relative; }
    h3 { text-decoration: underline; }
    label { font-weight: bold; }
    .logo { position: absolute; top: 20px; left: 20px; }
    .back-button { position: absolute; top: 20px; right: 20px; background-color: black; color: white; padding: 5px 15px; border: none; border-radius: 5px; cursor: pointer; }
    .back-button:hover { background-color: #333; }
</style>
<body>
    <div class="logo">
        <img src="Ku_logo.jpeg" alt="System Logo" width="100">
    </div>
    <button class="back-button" onclick="window.location.href='Admin_land_in.php'">Back</button>
    <div class="container">
        <div class="register-container">
            <h3 class="text-center mb-4">Admin Registration</h3>
            <form id="adminRegistrationForm">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                </div>
                <div class="form-group">
                    <label for="Admin_ID">Admin ID</label>
                    <input type="text" class="form-control" id="Admin_ID" name="Admin_ID" placeholder="Enter Admin ID" required>
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+254</span>
                        </div>
                        <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Enter Contact Number" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("form").submit(function (event) {
                event.preventDefault();
                let contactNoInput = $("#contact_no").val();
                let fullContactNo = "+254" + contactNoInput;
                let formData = {
                    name: $("#name").val(),
                    admin_ID: $("#Admin_ID").val(),
                    email: $("#email").val(),
                    contact_no: fullContactNo,
                    password: $("#password").val()
                };
                $.ajax({
                    url: "Admin_reg(back).php",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(formData),
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if (response.status === "success") {
                            alert(response.message);
                            if (response.clear_form) {
                                $("form")[0].reset();
                            }
                            if (response.redirect) {
                                setTimeout(function () {
                                    window.location.href = response.redirect;
                                }, 2000);
                            }
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        alert("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>
</body>
</html>