<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
    body {
        background-color: #ffffff;
        height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: center; 
        text-align: center;
    }
    header {
        width: 100%;
        display: flex;
        justify-content: flex-start; 
        padding: 20px; 
        box-sizing: border-box; 
    }
    .logo-container {
        display: flex;
        align-items: center;
    }
    .logo-container img {
        max-width: 120px;
        height: auto;
    }
    .logo-container .separator {
        width: 2px;
        height: 50px;
        background-color: #003366;
        margin: 0 10px;
    }
    .logo-container p {
        font-weight: bold;
        font-size: 20px;
        margin: 0;
        color: #003366;
    }
    main {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .welcome-message {
    margin-top: 20px;
    padding: 20px;
    font-size: 1.2em;
}

.welcome-message h1 {
    font-size: 1.8em;
    margin-bottom: 15px; 
}

.welcome-message p {
    margin-bottom: 10px;
    position: relative;
    bottom: 10px;
}
    .container-wrapper {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
        width: 80%;
    }
    .container-box {
        background-color: #e7f3ff;
        padding: 20px;
        border-radius: 10px;
        width: 30%;
        min-width: 250px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }
    .container-box:hover {
        box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2);
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
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #004499;
    }
    .learn-more {
        margin-top: 40px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .learn-more a {
        text-decoration: none;
        color: #007bff;
    }
    .footer {
        margin-top: auto;
        width: 100%;
        background-color: lightblue;
        padding: 20px;
        text-align: center;
        font-size: 14px;
    }
</style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="Ku_logo.jpeg" alt="System Logo">
            <div class="separator"></div>
            <p>Kenyatta University</p>
        </div>
    </header>
    <main>
        <div class="welcome-message">
            <h1>Welcome to Student-Lecturer Appointment System</h1>
            <p>Please log into the system as a student or a lecturer to book an appointment with a lecturer.</p>
        </div>
        <div class="container-wrapper">
            <div class="container-box">
                <h2>Student</h2>
                <p>Log in or sign up as a student.</p>
                <button onclick="location.href='student_login.php'">Log In</button>
                <button onclick="location.href='Student_registration.php'">Sign Up</button>
            </div>
            <div class="container-box">
                <h2>Lecturer</h2>
                <p>Log in or sign up as a lecturer.</p>
                <button onclick="location.href='Lecturer_login.php'">Log In</button>
                <button onclick="location.href='lecturer_registration.php'">Sign Up</button>
            </div>
            <div class="container-box">
                <h2>Admin</h2>
                <p>Log in or sign up as an administrator.</p>
                <button onclick="location.href='Admin_log_in.php'">Log In</button>
                <button onclick="location.href='Admin_registration.php'">Sign Up</button>
            </div>
        </div>
        <div class="learn-more">
            <p><a href="About.php">Learn more about the system</a></p>
        </div>
    </main>
    <footer class="footer">
        &copy; 2025 Kenyatta University. All rights reserved.
    </footer>
</body>
</html>