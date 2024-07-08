<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" href="payment.css"> <!-- Include your CSS file -->
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label, input {
            margin-bottom: 10px;
        }

        .date-time-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 10px;
        }

        .date-time-container label {
            flex-basis: 48%;
        }

        .date-time-container input {
            flex-basis: 48%;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment Details</h2>
    <?php
    session_start();

    if(isset($_SESSION['username'])) {
        include 'connection.php';
        $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

        if($conn) {
            $username = pg_escape_string($conn, $_SESSION['username']);

            // Query to fetch subscription ID and type of the user
            $query = "SELECT o_subscription_id, 'one' AS subscription_type FROM one WHERE o_username = '$username'
                      UNION
                      SELECT t_subscription_id, 'two' AS subscription_type FROM two WHERE t_username = '$username'";
            $result = pg_query($conn, $query);

            if($result) {
                $row = pg_fetch_assoc($result);

                if($row) {
                    $subscription_id = $row['o_subscription_id']; // assuming the subscription ID is always from 'one' table
                    $subscription_type = $row['subscription_type'];

                    // Display the username and subscription ID
                    echo "<p>Welcome: $username</p>";
                    echo "<p>Your Subscription ID is $subscription_id</p>";

                    // Set default amount based on subscription type
                    $default_amount = ($subscription_type === 'one') ? 1800 : 3200;

                    // Insert payment details if form is submitted
                    if(isset($_POST['transaction_id']) && isset($_POST['date_of_payment']) && isset($_POST['time_of_payment'])) {
                        $transaction_id = pg_escape_string($conn, $_POST['transaction_id']);
                        $date_of_payment = pg_escape_string($conn, $_POST['date_of_payment']);
                        $time_of_payment = pg_escape_string($conn, $_POST['time_of_payment']);
                        $amount = $default_amount;

                        // Check if the transaction ID is unique before insertion
                        $check_query = "";
                        if ($subscription_type === 'one') {
                            $check_query = "SELECT COUNT(*) FROM payment_one WHERE o_transaction_id = $transaction_id";
                        } else {
                            $check_query = "SELECT COUNT(*) FROM payment_two WHERE t_transaction_id = $transaction_id";
                        }

                        $check_result = pg_query($conn, $check_query);
                        $row_count = pg_fetch_assoc($check_result)['count'];

                        if ($row_count > 0) {
                            echo "<p><span style='color: red;'>Transaction ID already exists.</span></p>";
                        } else {
                            // Insert payment details into the respective table based on subscription type
                            if ($subscription_type === 'one') {
                                $insert_query = "INSERT INTO payment_one (o_subscription_id, o_transaction_id,o_name, o_date_of_payment, o_time_of_payment, o_amount)
                                                 VALUES ($subscription_id, $transaction_id,'$username','$date_of_payment', '$time_of_payment', $amount)";
                            } else {
                                $insert_query = "INSERT INTO payment_two (t_subscription_id, t_transaction_id,t_name,t_date_of_payment, t_time_of_payment, t_amount)
                                                 VALUES ($subscription_id, $transaction_id,'$username','$date_of_payment', '$time_of_payment', $amount)";
                            }

                            $insert_result = pg_query($conn, $insert_query);

                            if($insert_result) {
                                echo "<p>Payment details inserted successfully.</p>";
                                // Redirect to the next page
                                header("Location: info.php");
                                exit();
                            } else {
                                echo "<p>Error inserting payment details: " . pg_last_error($conn) . "</p>";
                            }
                        }
                    }

                    // Display the form
                    ?>
                    <form action="" method="post" onsubmit="return validateForm()">
                        <div style="width: 100%;">
                            <label for="transaction_id">Transaction ID:</label>
                            <input type="text" id="transaction_id" name="transaction_id" required>
                            <p class="error-message" id="transaction-id-error"></p>
                        </div>

                        <div class="date-time-container">
                            <label for="date_of_payment">Date of Payment:</label>
                            <input type="date" id="date_of_payment" name="date_of_payment" required>
                            <label for="time_of_payment">Payment Timing:</label>
                            <input type="time" id="time_of_payment" name="time_of_payment" required>
                        </div>

                        <label for="amount">Amount:</label>
                        <input type="text" id="amount" name="amount" value="<?php echo $default_amount; ?>" readonly required>
                        <p class="error-message" id="amount-error"></p>

                        <?php
                        // Displaying username in a hidden input field
                        if(isset($_SESSION['username'])) {
                            $username = $_SESSION['username'];
                            echo "<input type='hidden' name='username' value='$username'>";
                        }
                        ?>

                        <input type="submit" value="Submit">
                    </form>
                    <?php
                } else {
                    echo "<p>No subscription found for the user.</p>";
                }
            } else {
                echo "<p>Error fetching subscription details: " . pg_last_error($conn) . "</p>";
            }
            
            pg_close($conn);
        } else {
            echo "<p>Error: Unable to connect to the database.</p>";
        }
    } else {
        echo "<p>User is not logged in.</p>";
    }
    ?>

<script>
    function validateForm() {
        const transactionIdInput = document.getElementById('transaction_id');
        const dateOfPaymentInput = document.getElementById('date_of_payment');
        const timeOfPaymentInput = document.getElementById('time_of_payment');
        const amountInput = document.getElementById('amount');
        
        const transactionIdError = document.getElementById('transaction-id-error');
        const dateOfPaymentError = document.getElementById('date-of-payment-error');
        const amountError = document.getElementById('amount-error');
        
        transactionIdError.textContent = '';
        dateOfPaymentError.textContent = '';
        amountError.textContent = '';

        let isValid = true;

        if (transactionIdInput.value.trim() === '') {
            transactionIdError.textContent = 'Transaction ID is required';
            isValid = false;
        }

        if (dateOfPaymentInput.value.trim() === '' || timeOfPaymentInput.value.trim() === '') {
            dateOfPaymentError.textContent = 'Date and time of Payment are required';
            isValid = false;
        }

        if (amountInput.value.trim() === '') {
            amountError.textContent = 'Amount is required';
            isValid = false;
        }

        return isValid;
    }
</script>

</body>
</html>
