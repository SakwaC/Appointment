<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: lightblue;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .center-btn {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            width: 60%;
            padding: 12px 20px;
            font-size: 16px;
            text-align: center;
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
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #333;
        }
        .password-container {
            position: relative;
            margin-bottom: 25px;
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
    </style>
</head>
<body>
    <div class="logo">
        <img src="Ku_logo.jpeg" alt="System Logo" width="100">
    </div>
    <button class="back-button" onclick="window.location.href='land_in.php'">Back</button>
    <div class="form-container">
        <h2 class="text-center">Lecturer Registration</h2>
        <form id="registrationForm" class="needs-validation" novalidate action="http://localhost/Appointments/lecturer_reg.php" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control form-group" name="lecturerId" id="lecturerId" placeholder="Lecturer ID No" required>
                    <input type="text" class="form-control form-group" name="name" id="name" placeholder="Name" required>
                    <input type="email" class="form-control form-group" name="email" id="email" placeholder="Email" required>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                    </div>
                    <div class="password-container">
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
                        <span class="toggle-password" onclick="togglePassword('confirmPassword')">👁️</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <select id="school" name="school" class="form-control form-group" required>
                        <option value="">Select School</option>
                        <option value="SPAS">SPAS</option>
                        <option value="School of Business">School of Business</option>
                        <option value="SEA">SEA</option>
                        <option value="School of Education">School of Education</option>
                    </select>
                    <select class="form-control form-group" name="department" id="department" required>
                        <option value="">Select Department</option>
                    </select>
                    <input type="text" id="contactNo" name="contactNo" value="+254" maxlength="13" oninput="restrictPhoneNumber()" required style="margin-bottom: 20px;">
                    <input type="date" id="registrationDate" name="registration_date" class="form-control form-group" required>
                </div>
            </div>
            <div class="center-btn">
                <button type="submit" id="submitBtn" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
    
    
    <script>
        // Function to toggle password visibility
        function togglePassword(fieldId) {
            let field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    
        // Function to enforce phone number starting with +254
        function enforcePhoneNumberFormat() {
            let input = document.getElementById('contactNo');
            if (!input.value.startsWith('+254')) {
                input.value = '+254';
            }
            input.value = input.value.replace(/[^0-9+]/g, ''); // Allow only numbers and "+"
        }
    
        $(document).ready(function() {
            // Ensure phone number starts with +254
            $('#contactNo').val('+254');
    
            // Department selection based on school
            $('#school').change(function() {
                var school = $(this).val();
                var departmentDropdown = $('#department');
                departmentDropdown.empty();
                departmentDropdown.append('<option value="">Select Department</option>');
                var departments = {
                    'SPAS': ['Computing and Information Science', 'Biology and Chemistry', 'Biotechnology'],
                    'School of Business': ['Accounting', 'Business Administration'],
                    'SEA': ['Mechanical', 'Electrical'],
                    'School of Education': ['Geography', 'English Literature']
                };
                if (departments[school]) {
                    departments[school].forEach(function(dept) {
                        departmentDropdown.append('<option value="' + dept + '">' + dept + '</option>');
                    });
                }
            });
    
            // Name validation (Only letters, show error once)
            $('#name').on('input', function() {
                let validName = this.value.replace(/[^a-zA-Z\s]/g, ''); // Remove non-alphabet characters
                if (this.value !== validName) {
                    if ($('.error-message').length === 0) { // Ensure error appears only once
                        $(this).after('<small class="error-message" style="color:red;">Only letters are allowed</small>');
                    }
                } else {
                    $('.error-message').remove(); // Remove error if input is valid
                }
                this.value = validName; // Update input with valid characters only
            });
    
            // Clear error message on focus
            $('#name').focus(function() {
                $('.error-message').remove();
            });
    
            // Password input now allows any character (no removal of non-numeric)
            // Password strength evaluation
            $('#password').on('input', function() {
                let password = $(this).val();
                let strength = evaluatePasswordStrength(password);
                $('#passwordStrength').text('Password strength: ' + strength);
            });
    
            function evaluatePasswordStrength(password) {
                if (/^\d+$/.test(password)) {
                    return "Weak";
                } else if (/^[a-zA-Z0-9]+$/.test(password)) {
                    return "Fair";
                } else {
                    return "Strong";
                }
            }
    
            // Validate form on submit with AJAX
            $('#registrationForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                let isValid = true;
                $('.error-message').remove(); // Clear previous errors
                $('.alert').remove(); // Clear previous alerts
    
                // Check required fields
                $('#registrationForm input, #registrationForm select').each(function() {
                    if ($(this).prop('required') && !$(this).val().trim()) {
                        $(this).after('<small class="error-message" style="color:red;">This field is required</small>');
                        isValid = false;
                    }
                });
    
                // Validate email format
                let email = $('#email').val();
                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (email && !emailPattern.test(email)) {
                    $('#email').after('<small class="error-message" style="color:red;">Enter a valid email</small>');
                    isValid = false;
                }
    
                // Validate password match
                let password = $('#password').val();
                let confirmPassword = $('#confirmPassword').val();
                if (password !== confirmPassword) {
                    $('#confirmPassword').after('<small class="error-message" style="color:red;">Passwords do not match</small>');
                    isValid = false;
                }
    
                if (!isValid) return; // Stop form submission if validation fails
    
                // Send form data using AJAX
                $.ajax({
                    type: "POST",
                    url: "http://localhost/Appointments/lecturer_reg(back).php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        let alertClass = response.status === "success" ? "alert-success" : "alert-danger";
                        let alertMessage = response.status === "success" ? "Registration successful!" : response.message;
    
                        $("#registrationForm").before('<div class="alert ' + alertClass + ' text-center">' + alertMessage + '</div>');
    
                        if (response.status === "success") {
                            setTimeout(function() {
                                window.location.href = "lecturer_login.php"; // Redirect after 3 seconds
                            }, 3000);
                        }
                    },
                    error: function() {
                        $("#registrationForm").before('<div class="alert alert-danger text-center">An error occurred. Please try again.</div>');
                    }
                });
            });
    
            // Set today's date for registrationDate field
            let today = new Date().toISOString().split('T')[0];
            $('#registrationDate').attr('min', today).attr('max', today);
    
            // Prevent form resubmission on page reload
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
    
</body>
</html>
