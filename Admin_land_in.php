<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Landing Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #ffffff;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .logo-container {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
        }
        .logo-container img {
            width: 100px;
        }
        .logo-container .separator {
            width: 2px;
            height: 50px;
            background-color: black;
            margin: 0 10px;
        }
        .logo-container p {
            font-weight: bold;
            font-size: 20px;
            margin: 0;
        }
        .container-wrapper {
            display: flex;
            justify-content: center;
            width: 60%;
        }
        .container-box {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin: 10px;
        }
        .container-box h2 {
            color: #007bff;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        h1{
            font-size: 30px;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: auto;
            width: 100%;
            background-color: lightblue;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            position: absolute;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="Ku_logo.jpeg" alt="System Logo">
        <div class="separator"></div>
        <p>Kenyatta University</p>
    </div>
    <h1>Welcome Administrator</h1>
    <p>
        Please log into the system as an administrator to manage the  system.
    </p>
    <div class="container-wrapper">
        <div class="container-box">
            <h2>Admin Login</h2>
            <p>Log in to manage the system.</p>
            <button onclick="location.href='Admin_log_in.php'">Log In</button>
        </div>
        <div class="container-box">
            <h2>Admin Sign Up</h2>
            <p>Register for an administrator account.</p>
            <button onclick="location.href='Admin_registration.php'">Sign Up</button>
        </div>
    </div>
    <footer class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>
</body>
</html>
