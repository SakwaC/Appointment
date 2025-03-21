<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body { background-color: lightblue; }
        h2 { text-align: center; color: blue; }
        label { font-weight: bold; }
        .back-button {
            position: absolute;
            top: 20px;
            right: 40px;
            background-color: black;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Dashboard.php'">Back</button>
    <div class="container mt-5">
        <h2>Create Appointment</h2>
        <form id="appointmentForm">
            <input type="hidden" id="studentId" name="student_id" value="<?php echo $studentID; ?>">
            <div class="form-group">
                <label for="appointmentDate">Appointment Date:</label>
                <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
            </div>
            <div class="form-group">
                <label for="department">Select Department:</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="">Select Department</option>
                </select>
            </div>
            <div class="form-group">
                <label for="lecturer">Select Lecturer:</label>
                <select class="form-control" id="lecturer" name="lecturer" required>
                    <option value="">Select Lecturer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Lecturer Schedule:</label>
                <div id="lecturerSchedule">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Meeting Duration</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label for="appointmentTime">Appointment Time:</label>
                <select class="form-control" id="appointmentTime" name="time_of_appointment" required>
                    <option value="">Select Time</option>
                </select>
            </div>
            <div class="form-group">
                <label for="appointmentDescription">Describe the Appointment:</label>
                <textarea class="form-control" id="appointmentDescription" name="appointment_description" rows="3" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            const studentId = localStorage.getItem('student_id');
            $('#studentId').val(studentId);
            let today = new Date().toISOString().split('T')[0];
            $('#appointmentDate').attr('min', today);

            $.getJSON("http://localhost/Appointments/get_department.php", function(data) {
                $.each(data, function(index, value) {
                    $('#department').append(`<option value="${value.department}">${value.department}</option>`);
                });
            });

            $('#department').change(function() {
                let departmentName = $(this).val();
                $('#lecturer').empty().append('<option value="">Select Lecturer</option>');
                if (departmentName) {
                    $.getJSON(`http://localhost/Appointments/get_lecturer.php?department=${departmentName}`, function(data) {
                        $.each(data, function(index, value) {
                            $('#lecturer').append(`<option value="${value.lecturer_ID}">${value.name}</option>`);
                        });
                    });
                }
            });

            $('#lecturer, #appointmentDate').change(function () {
                let lecturerId = $('#lecturer').val();
                let appointmentDate = $('#appointmentDate').val();
                $('#lecturerSchedule tbody').empty();
                $('#appointmentTime').empty().append('<option value="">Select Time</option>');

                if (lecturerId && appointmentDate) {
                    $.getJSON(`http://localhost/Appointments/get_lecturer_schedule.php?lecturer_ID=${lecturerId}`, function(data) {
                        if (data.data && data.data.length > 0) {
                            $.each(data.data, function(index, value) {
                                $('#lecturerSchedule tbody').append(`
                                    <tr>
                                        <td>${value.days}</td>
                                        <td>${value.start_time}</td>
                                        <td>${value.end_time}</td>
                                        <td>${value.meeting_duration} mins</td>
                                    </tr>
                                `);
                            });
                        } else {
                            $('#lecturerSchedule tbody').append('<tr><td colspan="4" class="text-center">No schedule available</td></tr>');
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.log("AJAX Error (schedule):", { status: textStatus, error: errorThrown, response: jqXHR.responseText });
                    });

                    $.getJSON(`http://localhost/Appointments/get_available_slots.php?lecturer_ID=${lecturerId}&appointment_date=${appointmentDate}`, function(data) {
                        if (data.time_slots && data.time_slots.length > 0) {
                            $.each(data.time_slots, function(index, slot) {
                                $('#appointmentTime').append(`<option value="${slot}">${slot}</option>`);
                            });
                        } else {
                            $('#appointmentTime').append('<option value="">No slots available for this date</option>');
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.log("AJAX Error (slots):", { status: textStatus, error: errorThrown, response: jqXHR.responseText });
                    });
                }
            });

            $('#appointmentForm').submit(function (event) {
                event.preventDefault();
                let appointmentTime = $('#appointmentTime').val();
                if (!appointmentTime) {
                    alert("Please select an appointment time.");
                    return;
                }
                let formData = $(this).serializeArray().filter((item, index, self) =>
                    index === self.findIndex((t) => t.name === item.name)
                );
                if (!formData.some(item => item.name === 'student_id')) {
                    formData.push({ name: 'student_id', value: studentId });
                }
                console.log("Form Data Details:", {
                    studentId: studentId,
                    appointmentDate: $('#appointmentDate').val(),
                    department: $('#department').val(),
                    lecturer: $('#lecturer').val(),
                    appointmentTime: appointmentTime,
                    description: $('#appointmentDescription').val()
                });

                $.ajax({
                    type: "POST",
                    url: "http://localhost/Appointments/Create_back.php",
                    data: $.param(formData),
                    dataType: "json",
                    success: function(response) {
                        console.log("Server response:", response);
                        if (response.status === "success") {
                            alert("Appointment successfully created!");
                            window.location.href = 'Dashboard.php';
                        } else {
                            alert(response.message || 'Appointment failed. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", { xhr, status, error });
                        alert("An error occurred while processing the request.");
                    }
                });
            });
        });
    </script>
</body>
</html>