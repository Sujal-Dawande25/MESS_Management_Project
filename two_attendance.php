<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Details</title>
    <link rel="stylesheet" href="details.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <center><h1 class="details-heading">Two Time Attendance</h1></center>

       
        <div class="back-button">
            <a href="daily.html">Back</a>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>Username</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th>Actions</th> <!-- Added column for buttons -->
                    <th>Number of Absentees</th> <!-- Added column for buttons -->
                </tr>
            </thead>
            <tbody>
            <?php
// Database connection parameters
include 'connection.php';
// Establish a connection to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the connection is successful
if (!$conn) {
    echo "<tr><td colspan='6'>Error: Unable to connect to the database.</td></tr>";
} else {
    // Check if the form is submitted and subscription ID is provided
    if (isset($_POST['subscription_id']) && isset($_POST['attendance'])) {
        // Sanitize the inputs to prevent SQL injection
        $subscription_id = pg_escape_string($conn, $_POST['subscription_id']);
        $attendance = pg_escape_string($conn, $_POST['attendance']);
        $table = pg_escape_string($conn, $_POST['table']); // Retrieve table name from form

        // Update the attendance status and total number of absentees in the respective table
        if ($attendance === 'Absent') {
            // Retrieve the current number of absentees
            $query = "SELECT t_total_no_of_absentees FROM two WHERE t_subscription_id = $subscription_id";
            $result = pg_query($conn, $query);
            $row = pg_fetch_assoc($result);
            $currentAbsentees = (int)$row['t_total_no_of_absentees'];

            // Increment the number of absentees if it's less than 4, otherwise show an error
            if ($currentAbsentees < 4) {
                $newAbsentees = $currentAbsentees + 1;
                $update_query = "UPDATE two SET t_total_no_of_absentees = $newAbsentees, attendance = 'Absent' WHERE t_subscription_id = $subscription_id";
                $result = pg_query($conn, $update_query);

                if ($result) {
                    echo "<div class='success-message'>Attendance updated successfully. Absentees count: $newAbsentees</div>";

                    // Calculate the new end date by incrementing it by one day
                    $endDateQuery = "SELECT end_date FROM two WHERE t_subscription_id = $subscription_id";
                    $endDateResult = pg_query($conn, $endDateQuery);
                    $endDateRow = pg_fetch_assoc($endDateResult);
                    $currentEndDate = $endDateRow['end_date'];
                    // Calculate the new end date by incrementing it by one day
                    $newEndDate = date('Y-m-d', strtotime($currentEndDate . " +1 day"));

                    // Update the end date in the database
                    $updateEndDateQuery = "UPDATE two SET end_date = '$newEndDate' WHERE t_subscription_id = $subscription_id";
                    $updateEndDateResult = pg_query($conn, $updateEndDateQuery);
                    if ($updateEndDateResult) {
                        echo "<div class='success-message'>End date updated successfully. New end date: $newEndDate</div>";
                    } else {
                        echo "<div class='error-message'>Error updating end date.</div>";
                    }
                } else {
                    echo "<div class='error-message'>Error updating attendance.</div>";
                }
            } else {
                echo "<div class='error-message'>Error: Absentees count cannot exceed 4.</div>";
            }
        } else {
            // Update the attendance status only
            $update_query = "UPDATE $table SET attendance = '$attendance' WHERE ";
            if ($table === 'one') {
                $update_query .= "o_subscription_id = $subscription_id";
            } else {
                $update_query .= "t_subscription_id = $subscription_id";
            }

            // Execute the update query
            $result = pg_query($conn, $update_query);

            if ($result) {
                echo "<div class='success-message'>Attendance updated successfully.</div>";
            } else {
                echo "<div class='error-message'>Error updating attendance.</div>";
            }
        }
    }

    // Query to fetch data from the 'two' table
    $query = "SELECT t_subscription_id, t_username, t_usertype, attendance ,t_total_no_of_absentees FROM two";
    $result = pg_query($conn, $query);

    // Check if there are any rows returned
    if (pg_num_rows($result) > 0) {
        // Loop through each row and display the data
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr id='row_" . $row['t_subscription_id'] . "'>"; // Assign a unique ID to each row
            echo "<td>" . $row['t_subscription_id'] . "</td>";
            echo "<td>" . $row['t_username'] . "</td>";
            echo "<td>" . $row['t_usertype'] . "</td>";
            echo "<td id='status_" . $row['t_subscription_id'] . "'>" . $row['attendance'] . "</td>";
            echo "<td id='actions_" . $row['t_subscription_id'] . "'>" .
                "<form method='post'>" . // Form for submitting attendance updates
                "<input type='hidden' name='subscription_id' value='" . $row['t_subscription_id'] . "'>" .
                "<input type='hidden' name='table' value='two'>" . // Hidden field for table name
                "<button type='submit' name='attendance' value='Present'>Present</button>" . // Button to mark present
                "<button type='submit' name='attendance' value='Absent'>Absent</button>" . // Button to mark absent
                "</form>" .
                "</td>";
            echo "<td id='total_absentees_" . $row['t_subscription_id'] . "'>" . $row['t_total_no_of_absentees'] . "</td>"; 
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found.</td></tr>";
    }
}

// Close the database connection
pg_close($conn);
?>





            </tbody>
        </table>
    </div>

    <script>
       function markAttendance(table, subscriptionId, status) {
    if (status === 'Absent') {
        // Update the status text content based on the button clicked
        var currentStatus = document.getElementById('status_' + subscriptionId).textContent;
        if (currentStatus === 'Absent') {
            // Increment the number of absentees if it's less than 4, otherwise show an error
            var currentAbsentees = parseInt(document.getElementById('total_absentees_' + subscriptionId).textContent);
            if (currentAbsentees < 4) {
                var newAbsentees = currentAbsentees + 1;
                document.getElementById('total_absentees_' + subscriptionId).textContent = newAbsentees;
            } else {
                alert('Error: Absentees count cannot exceed 4.');
                return;
            }
        }
    }

    // Update the status text content based on the button clicked
    document.getElementById('status_' + subscriptionId).textContent = status;
    // Remove the "Present" and "Absent" buttons and replace with "Edit"
    document.getElementById('actions_' + subscriptionId).innerHTML =
        "<button onclick=\"editAttendance('" + table + "', " + subscriptionId + ")\">Edit</button>";
}

        function editAttendance(table, subscriptionId) {
            // Restore the "Present" and "Absent" buttons
            document.getElementById('actions_' + subscriptionId).innerHTML =
                "<button onclick=\"markAttendance('" + table + "', " + subscriptionId + ", 'Present')\">Present</button>" +
                "<button onclick=\"markAttendance('" + table + "', " + subscriptionId + ", 'Absent')\">Absent</button>";
        }

        function markAllPresent() {
    var subscriptionForms = document.querySelectorAll("[id^='actions_'] form");
    subscriptionForms.forEach(function (form) {
        var subscriptionInput = form.querySelector("input[name='subscription_id']");
        var attendanceInput = form.querySelector("input[name='attendance']");
        var tableInput = form.querySelector("input[name='table']");
        
        // Check if all necessary inputs are found before proceeding
        if (subscriptionInput && attendanceInput && tableInput) {
            var subscriptionId = subscriptionInput.value;
            var tableName = (subscriptionId >= 500) ? 'one' : 'two';
            attendanceInput.value = 'Present';
            tableInput.value = tableName;
            form.submit(); // Submit the form
        } else {
            console.error("Required input elements not found in form:", form);
        }
    });
}


    </script>
</body>
</html>
