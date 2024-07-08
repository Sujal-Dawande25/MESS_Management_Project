<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Time Mess Details</title>
    <link rel="stylesheet" href="details.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container">
        <div class="header">
            <h1 class="details-heading">Two Times Mess</h1>
            <div class="back-button">
                <a href="daily.html">Back</a>
            </div>
        </div>
        <div id="datetime" class="datetime"></div><br>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>Username</th>
                    <th>User Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Attendance</th>
                    <th>Payment Status</th>
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
    echo "<tr><td colspan='7'>Error: Unable to connect to the database.</td></tr>";
} else {
    // Query to fetch data from the 'two' table and check payment verification status
    $query = "SELECT t.*, p.verify_payment_status 
              FROM two t
              LEFT JOIN payment_two p ON t.t_subscription_id = p.t_subscription_id";

    // Execute the query
    $result = pg_query($conn, $query);

    // Check if there are any rows returned
    if (pg_num_rows($result) > 0) {
        // Loop through each row and display the data
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['t_subscription_id'] . "</td>";
            echo "<td>" . $row['t_username'] . "</td>";
            echo "<td>" . $row['t_usertype'] . "</td>";
            echo "<td>" . $row['start_date'] . "</td>";
            echo "<td>" . $row['end_date'] . "</td>";
            echo "<td>" . $row['attendance'] . "</td>";

            // Check if the payment is verified and update the payment status accordingly
            if ($row['verify_payment_status'] === 'Verified') {
                echo "<td>Paid</td>"; // Update payment status to "Paid"
            } else {
                echo "<td>Not Paid</td>"; // Update payment status to "Not Paid"
                
                // Automatically update the payment status in the database
                $updateQuery = "UPDATE two SET payment_status = 'Paid' WHERE t_subscription_id = {$row['t_subscription_id']}";
                $updateResult = pg_query($conn, $updateQuery);
                if (!$updateResult) {
                    echo "Error updating payment status in the database.";
                }
            }

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No records found.</td></tr>";
    }
}

// Close the database connection
pg_close($conn);
?>

            </tbody>
        </table>
    </div>

    <script>
        // Function to display current date and time
        function displayDateTime() {
            var now = new Date();
            var datetimeElement = document.getElementById('datetime');
            datetimeElement.innerHTML = now.toLocaleString(); // Change format as needed
        }

        // Call the function initially and every second to update time
        displayDateTime();
        setInterval(displayDateTime, 1000);
    </script>
</body>
</html>
