<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: lightblue;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            user-select: none;
        }

        .btn {
            width: 50%;
            padding: 12px;
            border-radius: 5px;
            background-color: #007bff;
            border: none;
            font-size: 18px;
            color: white;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
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

        .form-row {
            display: flex;
            justify-content: space-between;
        }

        .form-column {
            width: 48%;
        }
    </style>
        
</head>
<body>
    <div class="logo">
        <img src="Ku_logo.jpeg" alt="System Logo" width="100">
    </div>

    <button class="back-button" onclick="window.location.href='land_in.php'">Back</button>

    <div class="form-container">
        <h2>Student Registration</h2>
        <form id="registrationForm" class="needs-validation" novalidate action="http://localhost/Appointments/Student_reg.php" method="POST">
            <div class="form-row">
                <div class="form-column">
                    <input type="text" id="studentId" name="Student_ID" class="form-control" placeholder="Student ID No" required>
                    <input type="text" id="name" name="Name" class="form-control" placeholder="Name" required>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>

                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <span class="toggle-password" onclick="togglePassword('password')">⌒</span>
                    </div>

                    <div class="password-container">
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
                        <span class="toggle-password" onclick="togglePassword('confirmPassword')">⌒</span>
                    </div>

                    <input type="text" id="contactNo" name="contactNo" value="+254" maxlength="13" oninput="restrictPhoneNumber()" required style="margin-bottom: 20px;">
                </div>
                <div class="form-column">
                    <select id="school" name="school" class="form-control" required>
                        <option value="">Select School</option>
                        <option value="SPAS">SPAS</option>
                        <option value="School of Business">School of Business</option>
                        <option value="SEA">SEA</option>
                        <option value="School of Education">School of Education</option>
                    </select>
                    <select id="department" name="department" class="form-control" required>
                        <option value="">Select Department</option>
                    </select>
                    <select id="course" name="course" class="form-control" required>
                        <option value="">Select Course</option>
                    </select>
                    <input type="date" id="registrationDate" name="registration_date" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn mt-3">Register</button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    
        $(document).ready(function() {
            $('#contactNo').val('+254'); // Initialize with +254
    
            let today = new Date().toISOString().split('T')[0];
            $('#registrationDate').attr('min', today).attr('max', today);
    
            // Data for schools, departments, and courses
            const schoolData = {
                'SPAS': {
                    'Computing and Information Science': ['Information Technology', 'Computer Science'],
                    'Biology and Chemistry': ['Analytical Chemistry', 'Biochemistry', 'Industrial Chemistry'],
                    'Biotechnology': ['Biology', 'Biotechnology'],
                },
                'School of Business': {
                    'Accounting': ['Accounts'],
                    'Business Administration': ['BCOM', 'Business Management', 'Procurement']
                },
                'SEA': {
                    'Electrical': ['Electrical Engineering'],
                    'Mechanical': ['Mechanical Engineering']
                },
                'School of Education': {
                    'Geography': ['Geography Business'],
                    'Languages': ['English Literature', 'Kiswahili History']
                }
            };
    
            // Populate departments based on selected school
            $('#school').change(function() {
                const selectedSchool = $(this).val();
                const departmentDropdown = $('#department');
                const courseDropdown = $('#course');
    
                departmentDropdown.empty().append('<option value="">Select Department</option>');
                courseDropdown.empty().append('<option value="">Select Course</option>');
    
                if (schoolData[selectedSchool]) {
                    $.each(schoolData[selectedSchool], function(department) {
                        departmentDropdown.append(`<option value="${department}">${department}</option>`);
                    });
                }
            });
    
            // Populate courses based on selected department
            $('#department').change(function() {
                const selectedSchool = $('#school').val();
                const selectedDepartment = $(this).val();
                const courseDropdown = $('#course');
    
                courseDropdown.empty().append('<option value="">Select Course</option>');
    
                if (schoolData[selectedSchool] && schoolData[selectedSchool][selectedDepartment]) {
                    $.each(schoolData[selectedSchool][selectedDepartment], function(index, course) {
                        courseDropdown.append(`<option value="${course}">${course}</option>`);
                    });
                }
            });
    
            // Validation and error clearing on input change
            $('#studentId').on('input', function() {
                let studentId = $(this).val();
                let studentIdPattern = /^[A-Z]\d{2}\/\d{4}\/\d{4}$/;
                if (studentIdPattern.test(studentId)) {
                    $(this).next('.error-message').remove();
                }
            });
    
            $('#name').on('input', function() {
                if ($(this).val().trim()) {
                    $(this).next('.error-message').remove();
                }
            });
    
            $('#email').on('input', function() {
                let email = $(this).val();
                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (emailPattern.test(email) || !email) {
                    $(this).next('.error-message').remove();
                }
            });
    
            $('#password, #confirmPassword').on('input', function() {
                let password = $('#password').val();
                let confirmPassword = $('#confirmPassword').val();
                if (password === confirmPassword && password.trim()) {
                    $('#confirmPassword').next('.error-message').remove();
                    $('#password').next('.error-message').remove();
                }
            });
    
            $('#contactNo').on('input', function() {
                if ($(this).val().startsWith('+254') && $(this).val().trim()) {
                    $(this).next('.error-message').remove();
                }
            });
    
            $('#school, #department, #course, #registrationDate').on('change', function() {
                if ($(this).val().trim()) {
                    $(this).next('.error-message').remove();
                }
            });
    
            $('#registrationForm').on('submit', function(event) {
                event.preventDefault();
                $('.error-message').remove(); // Clear previous errors
    
                let isValid = true;
    
                // Student ID Validation
                let studentId = $('#studentId').val();
                let studentIdPattern = /^[A-Z]\d{2}\/\d{4}\/\d{4}$/;
                if (!studentIdPattern.test(studentId)) {
                    $('#studentId').after('<small class="error-message" style="color:red;">Invalid Student ID format (A10/1000/2000)</small>');
                    isValid = false;
                }
                if(!studentId.trim()){
                    $('#studentId').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
    
                // Name Validation
                let name = $('#name').val();
                if (!name.trim()) {
                $('#name').after('<small class="error-message" style="color:red;">This field is required</small>');
                isValid = false;
                } else if (!/^[A-Za-z\s]+$/.test(name)) { // Check for letters and spaces only
                $('#name').after('<small class="error-message" style="color:red;">invalid Name format</small>');
                isValid = false;
                }
    
                // Email Validation
                let email = $('#email').val();
                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (email && !emailPattern.test(email)) {
                    $('#email').after('<small class="error-message" style="color:red;">Enter a valid email</small>');
                    isValid = false;
                }
                if(!email.trim()){
                    $('#email').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
    
                // Password Validation
                let password = $('#password').val();
                let confirmPassword = $('#confirmPassword').val();
                if (password !== confirmPassword) {
                    $('#confirmPassword').after('<small class="error-message" style="color:red;">Passwords do not match!</small>');
                    isValid = false;
                }
                if(!password.trim()){
                    $('#password').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
                if(!confirmPassword.trim()){
                    $('#confirmPassword').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
    
               
                // Contact Number Validation
                 let contactNo = $('#contactNo').val();
                 let contactPattern = /^\+254\d{9}$/;

                 if (!contactNo.trim()) {
                  $('#contactNo').after('<small class="error-message" style="color:red;">This field is required</small>');
                   isValid = false;
                 } else if (!contactPattern.test(contactNo)) {
                         $('#contactNo').after('<small class="error-message" style="color:red;">Contact number should be in the format +254XXXXXXXX</small>');
                  isValid = false;
                }

    
                // School, Department, Course and Registration Date Validation
                if(!$('#school').val().trim()){
                    $('#school').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
                if(!$('#department').val().trim()){
                    $('#department').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
                if(!$('#course').val().trim()){
                    $('#course').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
                if(!$('#registrationDate').val().trim()){
                    $('#registrationDate').after('<small class="error-message" style="color:red;">This field is required</small>');
                    isValid = false;
                }
    
                if (!isValid) return; // Stop if validation fails
    
                var formData = $(this).serialize();
    
                $.ajax({
                    type: 'POST',
                    url: 'http://localhost/Appointments/Student_reg(back).php',
                    data: formData,
                    success: function(response) {
                        let alertClass = response.status === "success" ? "alert-success" : "alert-danger";
                        let alertMessage = response.status === "success" ? "Registration successful!" : response.message;
    
                        $("#registrationForm").before('<div class="alert ' + alertClass + ' text-center">' + alertMessage + '</div>');
    
                        if (response.status === "success") {
                            setTimeout(function() {
                                window.location.href = "student_login.php"; // Redirect after 3 seconds
                            }, 3000);
                        }
                    },
                    error: function() {
                        $("#registrationForm").before('<div class="alert alert-danger text-center">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
                  
    </script>
</body>
</html>