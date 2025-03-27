<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Availability Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
        body{
            background-color: aliceblue;
        }
        .editable {
            cursor: pointer;
        }
        .edit-form {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            padding: 10px;
            z-index: 10;
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='lecturer_dashboard.php'">Back</button>
    <div class="container mt-5">
        <h2 class="text-center text-primary">Set Availability & Schedule</h2>

        <form id="availabilityForm">
            <div class="form-group">
                <label>Select Available Days:</label><br>
                <input type="checkbox" name="days[]" value="Monday"> Monday
                <input type="checkbox" name="days[]" value="Tuesday"> Tuesday
                <input type="checkbox" name="days[]" value="Wednesday"> Wednesday
                <input type="checkbox" name="days[]" value="Thursday"> Thursday
                <input type="checkbox" name="days[]" value="Friday"> Friday
                
            </div>

            <div class="form-group">
                <label for="start_time">Available From:</label>
                <input type="time" id="start_time" name="start_time" class="form-control">
            </div>

            <div class="form-group">
                <label for="end_time">Available Until:</label>
                <input type="time" id="end_time" name="end_time" class="form-control">
            </div>

            <div class="form-group">
                <label for="meeting_duration">Meeting Duration (Minutes):</label>
                <select id="meeting_duration" name="meeting_duration" class="form-control">
                    <option value="15">15 mins</option>
                    <option value="30">30 mins</option>
                    <option value="45">45 mins</option>
                    <option value="60">1 hour</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Save Availability</button>
        </form>

        <div class="mt-4">
            <h3 class="text-info">Your Current Schedule</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Days</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Meeting Duration</th>
                    </tr>
                </thead>
                <tbody id="scheduleTable">
                    </tbody>
            </table>
        </div>
    </div>

    <div id="editForm" class="edit-form">
        <label for="editTime">Time:</label>
        <input type="time" id="editTime">
        <br>
        <label for="editDuration">Duration:</label>
        <select id="editDuration" class="form-control">
            <option value="15">15 mins</option>
            <option value="30">30 mins</option>
            <option value="45">45 mins</option>
            <option value="60">1 hour</option>
        </select>
        <br>
        <button id="saveEdit" class="btn btn-success">Save</button>
        <button id="cancelEdit" class="btn btn-secondary">Cancel</button>
    </div>
   
    <script>
       $(document).ready(function() {
    if (!localStorage.getItem('lecturer_logged_in')) {
        console.log("Auth Check: Redirecting to login");
        window.location.href = 'LecturerLogin.php';
        return;
    }

    let currentCell;
    let currentScheduleId;

    function formatTimeWithoutSeconds(timeString) {
        if (timeString && timeString.length >= 5) {
            return timeString.slice(0, 5); // Extracts HH:MM
        }
        return timeString; // Return original if format is unexpected
    }

    function loadSchedule() {
        const lecturerId = localStorage.getItem('lecturer_id');
        const sessionId = localStorage.getItem('session_id');
        console.log("loadSchedule called. Lecturer ID:", lecturerId, "Session ID:", sessionId);


        $.ajax({
            url: "http://localhost/Appointments/get_schedule.php",
            method: "GET",
            xhrFields: {
                withCredentials: true
            },
            headers: {
                'Content-Type': 'application/json',
                'X-Lecturer-ID': lecturerId,
                'X-Session-ID': sessionId
            },
            crossDomain: true,
            data: {
                lecturer_id: lecturerId,
                session_id: sessionId
            },
            success: function(response) {
                console.log("get_schedule success:", response);
                let tableBody = $("#scheduleTable");
                tableBody.empty();

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response && response.data && response.data.length > 0) {
                    // Sorting the schedule data
                    const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    response.data.sort((a, b) => {
                        return dayOrder.indexOf(a.days) - dayOrder.indexOf(b.days);
                    });

                    response.data.forEach(schedule => {
                        let row = `<tr data-schedule-id="${schedule.id}">
                                        <td>${schedule.days}</td>
                                        <td class="editable" data-field="start_time">${schedule.start_time}</td>
                                        <td class="editable" data-field="end_time">${schedule.end_time}</td>
                                        <td class="editable" data-field="meeting_duration">${schedule.meeting_duration} mins</td>
                                    </tr>`;
                        tableBody.append(row);
                    });
                } else if (response.error) {
                    tableBody.append("<tr><td colspan='4' class='text-center'>" + response.error + "</td></tr>");
                } else {
                    tableBody.append("<tr><td colspan='4' class='text-center'>No schedule available</td></tr>");
                }
            },
            error: function(xhr, status, error) {
                console.log("get_schedule AJAX Error:", {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
            }
        });
    }

    $("#scheduleTable").on('click', '.editable', function(event) {
        currentCell = $(this);
        currentScheduleId = $(this).closest('tr').data('schedule-id');
        const field = $(this).data('field');
        const currentText = $(this).text();
        const rect = this.getBoundingClientRect();

        console.log("Editable cell clicked. Schedule ID:", currentScheduleId, "Field:", field);

        $('#editForm').show();
        $('#editForm').css({
            left: rect.left + 'px',
            top: rect.bottom + 'px'
        });

        if (field === 'start_time' || field === 'end_time') {
            $('#editTime').val(currentText);
            $('#editDuration').hide();
        } else if (field === 'meeting_duration') {
            $('#editTime').hide();
            $('#editDuration').show();
            $('#editDuration').val(currentText.replace(' mins', ''));
        }
    });

    $('#saveEdit').click(function() {
        const field = currentCell.data('field');
        let newValue;

        if (field === 'start_time' || field === 'end_time') {
            newValue = $('#editTime').val();
        } else if (field === 'meeting_duration') {
            newValue = $('#editDuration').val();
        }

        console.log("Save Edit clicked. Schedule ID:", currentScheduleId, "Field:", field, "New Value:", newValue);

        $.ajax({
            url: "http://localhost/Appointments/update_schedule.php",
            method: "POST",
            data: {
                schedule_id: currentScheduleId,
                field: field,
                value: newValue
            },
            xhrFields: {
                withCredentials: true
            },
            headers: {
                'X-Lecturer-ID': localStorage.getItem('lecturer_id'),
                'X-Session-ID': localStorage.getItem('session_id')
            },
            success: function(response) {
                console.log("update_schedule success:", response);
                $('#editForm').hide();
                loadSchedule();
            },
            error: function(xhr, status, error) {
                console.log("update_schedule AJAX Error:", {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
            }
        });
    });

    $('#cancelEdit').click(function() {
        $('#editForm').hide();
    });

    $("#availabilityForm").submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        formData += '&lecturer_id=' + localStorage.getItem('lecturer_id');

        $.ajax({
            url: "http://localhost/Appointments/set_lecturer_availability.php",
            method: "POST",
            data: formData,
            xhrFields: {
                withCredentials: true
            },
            headers: {
                'X-Lecturer-ID': localStorage.getItem('lecturer_id'),
                'X-Session-ID': localStorage.getItem('session_id')
            },
            success: function(response) {
                console.log("set_lecturer_availability success:", response);
                alert(response.message);
                $('input[type="checkbox"]').prop('checked', false);
                $('#start_time').val('');
                $('#end_time').val('');
                $('#meeting_duration').val('15');
                loadSchedule();
            },
            dataType: "json"
        });
    });

    loadSchedule();
});
    </script>
    </body>
    </html>