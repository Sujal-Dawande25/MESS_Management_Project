<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment One Details</title>
    <link rel="stylesheet" href="details.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container">
        <div class="header">
            <h1 class="details-heading">Payment One Details</h1>
            <div class="back-button">
                <a href="daily.html">Back</a>
            </div>
        </div>
        <div id="datetime" class="datetime"></div><br>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>Transaction ID</th>
                    <th>Username</th>
                    <th>Date of Payment</th>
                    <th>Time of Payment</th>
                    <th>Amount</th>
                    <th>Verify the Payment</th>
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
        // Check if the form is submitted and transaction ID is provided
        if (isset($_POST['transaction_id'])) {
            // Sanitize the input to prevent SQL injection
            $transaction_id = pg_escape_string($conn, $_POST['transaction_id']);

            // Update the payment status in the 'payment_one' table
            $update_query = "UPDATE payment_one SET verify_payment_status = 'Verified' WHERE o_transaction_id = $transaction_id";

            // Execute the update query
            $result = pg_query($conn, $update_query);

            if ($result) {
                echo "<div class='success-message'>Payment verified successfully.</div>";
            } else {
                echo "<div class='error-message'>Error: " . pg_last_error($conn) . "</div>"; // Display the error message
            }
        }

        // Query to fetch data from the 'payment_one' table
        $query = "SELECT * FROM payment_one";

        // Execute the query
        $result = pg_query($conn, $query);

        // Check if there are any rows returned
        if (pg_num_rows($result) > 0) {
            // Loop through each row and display the data
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['o_subscription_id'] . "</td>";
                echo "<td>" . $row['o_transaction_id'] . "</td>";
                echo "<td>" . $row['o_name'] . "</td>";
                echo "<td>" . $row['o_date_of_payment'] . "</td>";
                echo "<td>" . $row['o_time_of_payment'] . "</td>";
                echo "<td>" . $row['o_amount'] . "</td>";
                // Check if payment is verified
                if ($row['verify_payment_status'] == 'Verified') {
                    echo "<td>Verified</td>";
                } else {
                    echo "<td><form method='post'><input type='hidden' name='transaction_id' value='" . $row['o_transaction_id'] . "'><button id='verifyBtn_" . $row['o_transaction_id'] . "'>Verify</button></form></td>";
                }
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

        <!-- Add the button to generate the report -->
        <form action="payment_one_report.php" method="post">
            <br><input type="submit" value="Generate Report">
        </form>
    </div>

    <script>
    // Function to display current date and time
    function displayDateTime() {
        var now = new Date();
        var datetimeElement = document.getElementById('datetime');
        datetimeElement.innerHTML = now.toLocaleString(); // Change format as needed
    }

    // Function to handle the verification action
    function verifyPayment(transactionId) {
        // Perform the verification action here
        // For demonstration, let's assume the verification is successful
        // Update the button text and color
        var button = document.getElementById('verifyBtn_' + transactionId);
        button.innerHTML = 'Verified';
        button.classList.add('verified'); // Add CSS class to style the button as verified
        button.disabled = true; 
        // You can replace the above code with an AJAX call to handle the verification process
        // and update the button accordingly based on the server's response
    }

    // Call the function initially and every second to update time
    displayDateTime();
    setInterval(displayDateTime, 1000);
</script>

</body>
</html>
