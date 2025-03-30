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
        .past-day-current-week {
    opacity: 0.6; 
    color: #999;   
    pointer-events: none; 
}
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='Dashboard.php'">Back</button>
    <div class="container mt-5">
        <h2>Book Appointment</h2>
        <form id="appointmentForm">
            <input type="hidden" id="studentId" name="student_id" value="<?php echo $studentID; ?>">
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
            <div class="form-group">
                <label for="appointmentDate">Appointment Date:</label>
                <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            // Function to get the current week's dates (Monday to Sunday)
            function getWeekDates() {
                let today = new Date();
                let dayOfWeek = today.getDay(); 
                let monday = new Date(today);
                monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1)); // Get Monday of this week

                let dates = [];
                for (let i = 0; i < 7; i++) {
                    let date = new Date(monday);
                    date.setDate(monday.getDate() + i);
                    let formattedDate = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                    dates.push({ date: formattedDate, day: date.toLocaleDateString('en-US', { weekday: 'long' }) });
                }
                return dates;
            }

            // Populate the Appointment Date dropdown with the current week's dates
            function populateWeekDates() {
            let weekDates = getWeekDates();
             let appointmentDateDropdown = $('#appointmentDate');
            appointmentDateDropdown.empty();
             let today = new Date();
             let todayFormatted = today.toISOString().split('T')[0];

             weekDates.forEach(dateInfo => {
             if (dateInfo.date >= todayFormatted) { // Only add dates that are today or in the future
            appointmentDateDropdown.append(`<option value="${dateInfo.date}">${dateInfo.day} (${dateInfo.date})</option>`);
            }
           });
           }
            // Call the function to populate the date dropdown when the page loads
            populateWeekDates();
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

    if (lecturerId) {
        $.getJSON(`http://localhost/Appointments/get_lecturer_schedule.php?lecturer_ID=${lecturerId}`, function(data) {
            if (data.data && data.data.length > 0) {
                // Function to format time (remove seconds)
                function formatTime(timeString) {
                    if (timeString) {
                        return timeString.split(':').slice(0, 2).join(':');
                    }
                    return '';
                }

                const $scheduleBody = $('#lecturerSchedule tbody').empty(); // Get a reference to the tbody
                const daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                const today = new Date();
                const todayDay = today.toLocaleDateString('en-US', { weekday: 'long' });
                const currentDayIndex = daysOfWeek.indexOf(todayDay);
                const selectedDate = appointmentDate ? new Date(appointmentDate) : null;

                $.each(data.data, function(index, value) {
                    let rowClass = '';
                    const scheduleDayIndex = daysOfWeek.indexOf(value.days);

                    if (selectedDate) {
                        const selectedDateObj = new Date(selectedDate);
                        const todayObj = new Date();
                        todayObj.setHours(0, 0, 0, 0); // Compare only dates

                        // Get the current week's Monday
                        const currentMonday = new Date(todayObj);
                        const dayOfWeek = currentMonday.getDay();
                        currentMonday.setDate(currentMonday.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
                        currentMonday.setHours(0, 0, 0, 0);

                        // Get next week's Monday
                        const nextMonday = new Date(currentMonday);
                        nextMonday.setDate(currentMonday.getDate() + 7);
                        nextMonday.setHours(0, 0, 0, 0);

                        const isCurrentWeek = selectedDateObj >= currentMonday && selectedDateObj < nextMonday;

                        if (isCurrentWeek && selectedDateObj.toDateString() === todayObj.toDateString()) {
                            const isPastDayCurrentWeek = scheduleDayIndex < currentDayIndex;
                            if (isPastDayCurrentWeek) {
                                rowClass = 'past-day-current-week';
                            }
                        } else if (isCurrentWeek && selectedDateObj < todayObj) {
                            rowClass = 'past-day-current-week';
                        }
                        // If selectedDate is not in the current week, no blurring
                    } else {
                        // If no appointmentDate is selected, and it's the current week view on load
                        const isPastDayCurrentWeek = scheduleDayIndex < currentDayIndex;
                        if (isPastDayCurrentWeek) {
                            rowClass = 'past-day-current-week';
                        }
                    }

                    const row = $(`
                        <tr class="${rowClass}">
                            <td>${value.days}</td>
                            <td>${formatTime(value.start_time)}</td>
                            <td>${formatTime(value.end_time)}</td>
                            
                        </tr>
                    `);

                    // Disable click action for past days in the current week
                    if (selectedDate) {
                        const selectedDateObj = new Date(selectedDate);
                        const todayObj = new Date();
                        todayObj.setHours(0, 0, 0, 0);

                        const currentMonday = new Date(todayObj);
                        const dayOfWeek = currentMonday.getDay();
                        currentMonday.setDate(currentMonday.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
                        currentMonday.setHours(0, 0, 0, 0);

                        const nextMonday = new Date(currentMonday);
                        nextMonday.setDate(currentMonday.getDate() + 7);
                        nextMonday.setHours(0, 0, 0, 0);

                        const isCurrentWeek = selectedDateObj >= currentMonday && selectedDateObj < nextMonday;

                        if (isCurrentWeek && selectedDateObj.toDateString() === todayObj.toDateString()) {
                            const isPastDayCurrentWeek = scheduleDayIndex < currentDayIndex;
                            if (isPastDayCurrentWeek) {
                                row.on('click', function(event) {
                                    event.preventDefault();
                                });
                            }
                        } else if (isCurrentWeek && selectedDateObj < todayObj) {
                            row.on('click', function(event) {
                                event.preventDefault();
                            });
                        }
                    } else {
                        const isPastDayCurrentWeek = scheduleDayIndex < currentDayIndex;
                        if (isPastDayCurrentWeek) {
                            row.on('click', function(event) {
                                event.preventDefault();
                            });
                        }
                    }

                    $scheduleBody.append(row);
                });
            } else {
                $('#lecturerSchedule tbody').append('<tr><td colspan="4" class="text-center">No schedule available</td></tr>');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX Error (schedule):", { status: textStatus, error: errorThrown, response: jqXHR.responseText });
        });

        $.getJSON(`http://localhost/Appointments/get_available_slots.php?lecturer_ID=${lecturerId}&appointment_date=${appointmentDate}`, function(data) {
            if (data.time_slots && data.time_slots.length > 0) {
                $('#appointmentTime').empty().append('<option value="">Select Time</option>');
                $.each(data.time_slots, function(index, slot) {
                    $('#appointmentTime').append(`<option value="${slot}">${slot}</option>`);
                });
            } else {
                $('#appointmentTime').empty().append('<option value="">No slots available for this date</option>');
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
            $('#lecturerSchedule tbody').on('click', 'tr', function () {
                $('#lecturerSchedule tbody tr').css("background-color", ""); 
                $(this).css("background-color", "yellow");

                let selectedSchedule = {
                    day: $(this).find('td:nth-child(1)').text(), // Get the selected day
                    start_time: $(this).find('td:nth-child(2)').text(), // Get start time
                    end_time: $(this).find('td:nth-child(3)').text(),
                    duration: $(this).find('td:nth-child(4)').text()
                };

                console.log("Selected Schedule:", selectedSchedule);

                // Find the corresponding date from the dropdown
                let matchingDate = $('#appointmentDate option').filter(function () {
                    return $(this).text().includes(selectedSchedule.day);
                }).val();

                // Set the appointment date and time automatically
                if (matchingDate) {
                    $('#appointmentDate').val(matchingDate);
                }
                $('#appointmentTime').html(`<option value="${selectedSchedule.start_time}">${selectedSchedule.start_time}</option>`);
            });
        });
    </script>
</body>
</html>