<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment One Monthly Report</title>
    <link rel="stylesheet" href="details.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container">
        <div class="header">
            <h1 class="details-heading">Payment One Monthly Report</h1>
            <div class="back-button">
                <a href="daily.html">Back</a>
            </div>
        </div>
        <div id="datetime" class="datetime"></div><br>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Amount</th>
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
                    echo "<tr><td colspan='2'>Error: Unable to connect to the database.</td></tr>";
                } else {
                    // Query to fetch monthly total amounts from the 'payment_one' table
                    $query = "SELECT TO_CHAR(DATE_TRUNC('month', o_date_of_payment), 'Mon YYYY') AS month_name, SUM(o_amount) AS total_amount 
                              FROM payment_one 
                              GROUP BY DATE_TRUNC('month', o_date_of_payment) 
                              ORDER BY DATE_TRUNC('month', o_date_of_payment)";

                    // Execute the query
                    $result = pg_query($conn, $query);

                    // Check if there are any rows returned
                    if (pg_num_rows($result) > 0) {
                        // Loop through each row and display the monthly report
                        while ($row = pg_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['month_name'] . "</td>";
                            echo "<td>" . $row['total_amount'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No records found.</td></tr>";
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
