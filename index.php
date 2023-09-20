<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Registration</title>
    <link rel="stylesheet" href="style.css"/>
    <style>
        /* Add some styling for the "+" and "-" buttons */
        .add-remove-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .add-remove-buttons button {
            margin-bottom: 10px;
        }

        /* Styling for the success message */
        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 5px;
        }
    </style>
    <script>
        // JavaScript function to add more student fields
        function addStudent() {
            var studentFields = document.getElementById('student-fields');
            var newStudentField = document.createElement('div');
            newStudentField.innerHTML = `
                <div class="student-input">
                    <input type="text" class="login-input" name="student_name[]" placeholder="Student Name" required />
                    <input type="text" class="login-input" name="student_roll[]" placeholder="Roll Number" required />
                    <input type="text" class="login-input" name="student_tshirt[]" placeholder="T-shirt Size" required />
                </div>
                <div class="add-remove-buttons">
                    <button type="button" onclick="addStudent()">Add</button>
                    <button type="button" onclick="removeStudent(this)">Remove</button>
                </div>
            `;
            studentFields.appendChild(newStudentField);
        }

        // JavaScript function to remove a student field
        function removeStudent(button) {
            var studentFields = document.getElementById('student-fields');
            studentFields.removeChild(button.parentNode.parentNode);
        }

        // JavaScript function to show the confirmation message and play the success sound
        function showConfirmation() {
            var registrationForm = document.getElementById('registration-form');
            var confirmationMessage = document.getElementById('confirmation-message');
            registrationForm.style.display = 'none';
            confirmationMessage.style.display = 'block';

            // Play the success sound
            var audio = new Audio('success.mp3');
            audio.play();
        }
    </script>
</head>
<body>
<?php
    require('db.php');
    // Initialize a variable to track whether registration was successful
    $registrationSuccessful = false;

    // When form submitted, insert values into the database.
    if (isset($_POST['submit'])) {
        // Remove backslashes and escape special characters
        $team_name = stripslashes($_POST['team_name']);
        $team_name = mysqli_real_escape_string($con, $team_name);
        $university_name = stripslashes($_POST['university_name']);
        $university_name = mysqli_real_escape_string($con, $university_name);
        date_default_timezone_set('Asia/Dhaka'); // Set the time zone to Bangladesh

        // Get the current date and time in the desired format
        $registration_time = date('Y-m-d H:i:s');

        // Insert team information into the 'teams' table, including registration time
        $query = "INSERT INTO `teams` (team_name, university_name, registration_time)
                  VALUES ('$team_name', '$university_name', '$registration_time')";
        $result = mysqli_query($con, $query);

        // Check for errors and handle them as needed
        if ($result) {
            $team_id = mysqli_insert_id($con);

            $student_names = $_POST['student_name'];
            $student_rolls = $_POST['student_roll'];
            $student_tshirt_sizes = $_POST['student_tshirt'];

            foreach ($student_names as $index => $student_name) {
                $student_name = mysqli_real_escape_string($con, $student_name);
                $student_roll = mysqli_real_escape_string($con, $student_rolls[$index]);
                $student_tshirt_size = mysqli_real_escape_string($con, $student_tshirt_sizes[$index]);

                $query = "INSERT INTO `students` (team_id, student_name, student_roll, student_tshirt)
                          VALUES ('$team_id', '$student_name', '$student_roll', '$student_tshirt_size')";
                $result = mysqli_query($con, $query);

                if (!$result) {
                    echo "Error: " . mysqli_error($con);
                }
            }

            // Set the registrationSuccessful variable to true
            $registrationSuccessful = true;
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
?>
    <!-- Check if registration was successful and show the appropriate content -->
    <?php if (!$registrationSuccessful) : ?>
        <form class="form" action="" method="post" id="registration-form">
            <h1 class="login-title">Registration</h1>
            <input type="text" class="login-input" name="team_name" placeholder="Team Name" required />
            <input type="text" class="login-input" name="university_name" placeholder="University Name" required />
            <div id="student-fields">
                <!-- Initial student input fields -->
                <div class="student-input">
                    <input type="text" class="login-input" name="student_name[]" placeholder="Student Name" required />
                    <input type="text" class="login-input" name="student_roll[]" placeholder="Roll Number" required />
                    <input type="text" class="login-input" name="student_tshirt[]" placeholder="T-shirt Size" required />
                    <div class="add-remove-buttons">
                        <button type="button" onclick="addStudent()">Add</button>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" value="Register" class="login-button">
        </form>
    <?php endif; ?>

    <!-- Confirmation Message (Initially Hidden) -->
    <div class="success-message" id="confirmation-message" style="display: <?php echo $registrationSuccessful ? 'block' : 'none'; ?>;">
        <h3>You are registered successfully.</h3><br/>
        <p class="link"> <a href='index.php'>Back to Home </a></p>
    </div>
</body>
</html>
