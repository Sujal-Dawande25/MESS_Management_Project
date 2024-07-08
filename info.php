<?php
session_start(); // Start the session

// Database connection parameters
include 'connection.php';
// Connect to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch user information from the database
    $query = "SELECT start_date, end_date, usertype, subscription_id FROM (
                SELECT start_date, end_date, 'One Time' AS usertype, o_subscription_id AS subscription_id FROM one WHERE o_username = $1
                UNION ALL
                SELECT start_date, end_date, 'Two Time' AS usertype, t_subscription_id AS subscription_id FROM two WHERE t_username = $1
            ) AS user_info";
    $result = pg_query_params($conn, $query, array($username));

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $startDate = $row['start_date'];
        $endDate = $row['end_date'];
        $messOption = $row['usertype'];
        $subscriptionId = $row['subscription_id'];

        // Fetch payment status based on mess option and subscription ID
        $paymentQuery = "";
        if ($messOption === 'One Time') {
            $paymentQuery = "SELECT payment_status FROM one WHERE o_subscription_id = $1";
        } else if ($messOption === 'Two Time') {
            $paymentQuery = "SELECT payment_status FROM two WHERE t_subscription_id = $1";
        }

        if (!empty($paymentQuery)) {
            $paymentResult = pg_query_params($conn, $paymentQuery, array($subscriptionId));

            if ($paymentResult && pg_num_rows($paymentResult) > 0) {
                $paymentRow = pg_fetch_assoc($paymentResult);
                $paymentStatus = $paymentRow['payment_status'];
            } else {
                $paymentStatus = "N/A";
            }
        } else {
            $paymentStatus = "N/A";
        }
    } else {
        $startDate = "N/A";
        $endDate = "N/A";
        $messOption = "N/A";
        $paymentStatus = "N/A";
    }
} else {
    echo "User not logged in.";
    exit; // Stop further execution if user is not logged in
}

// Close the database connection
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information and Today's Menu</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            font-family: 'Ubuntu', sans-serif;
            color: #555;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* 100% viewport height */
        }

        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 11px 35px 2px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            margin-bottom: 20px; /* Add margin-bottom to create space between containers */
            width: 80%; /* Adjust width as needed */
        }

        #menu-container,
        #info-container {
            flex: 1;
            padding: 20px;
            border-right: 2px solid #8C55AA; /* Add border to separate containers */
        }

        #info-container {
            border-right: none; /* Remove right border for last container */
        }

        h2 {
            color: #8C55AA;
            font-size: 24px;
            margin-bottom: 15px;
            /* text-decoration: underline; Add underline to h2 */
        }

        #menu {
            border-top: 2px solid #8C55AA;
            padding-top: 10px;
        }

        #student-info-form {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #8C55AA;
        }

        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .back-button {
    text-align: center; /* Center the button */
    margin-bottom: 20px; /* Add some space below the button */
}

.back-button a {
    text-align: right;
    
    display: inline-block; /* Ensure the link behaves as a block element */
    background-color: white; /* Purple color */
    color: #8C55AA; /* White color for text */
    text-decoration: none; /* Remove underline */
    padding: 10px 20px; /* Add padding for better click area */
    border-radius: 4px; /* Rounded corners */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

.back-button a:hover {
    background-color: whitesmoke; /* Darker purple on hover */
}
    </style>
</head>
<body>
    <div class="container">
         <div class="back-button">
            <a href="login1.php">Logout</a>
        </div>
        <div id="menu-container">
            <h2>Today's Menu</h2>
            
            <div id="menu">
                <p>Starter: Soup</p>
                <p>Main Course: Grilled Chicken</p>
                <p>Dessert: Chocolate Cake</p>
            </div>
        </div>

        <div id="info-container">
            <h2>Student Information</h2>
            <div id="menu">
                <p>Your mess is from <span id="startDateDisplay"><?php echo $startDate; ?></span> to <span id="endDateDisplay"><?php echo $endDate; ?></span></p>
                <p>Your mess option: <span id="messOptionDisplay"><?php echo $messOption; ?></span></p>
                <p>Your payment status: <span id="paymentStatusDisplay"><?php echo $paymentStatus; ?></span></p>
            </div>
        </div>
    </div>
</body>
</html>