<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="details.css"> <!-- Include details.css -->
    <style>
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 class="details-heading">User Details</h1>
        <div class="back-button">
            <a href="pricing.php">Back</a>
        </div>
    </div>
    <?php
    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        // Redirect the user to the login page if not logged in
        header("Location: login1.php");
        exit;
    }
    
    // Database connection parameters
    include 'connection.php';
    
    // Connect to the database
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    
    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }
    
    // Retrieve username from the session
    $username = $_SESSION['username'];
    
    // Initialize user type variable
    $userType = '';
    
    // Query to fetch user details based on the username
    $query = "SELECT * FROM (
                  SELECT 'Hostel' AS user_type, h_name AS name, h_address AS address, h_mobile_number AS mobile_number, h_email AS email FROM hostel WHERE h_name = $1
                  UNION ALL
                  SELECT 'Staff' AS user_type, s_name AS name, s_address AS address, s_mobile_number AS mobile_number, s_email AS email FROM staff WHERE s_name = $1
                  UNION ALL
                  SELECT 'Student' AS user_type, st_name AS name, st_address AS address, st_mobile_number AS mobile_number, st_email AS email FROM student WHERE st_name = $1
                  UNION ALL
                  SELECT 'Other' AS user_type, o_name AS name, o_address AS address, o_mobile_number AS mobile_number, o_email AS email FROM other WHERE o_name = $1
              ) AS user_details";
    
    $result = pg_query_params($conn, $query, array($username));
    
    if ($result) {
        // Fetch user details
        while ($row = pg_fetch_assoc($result)) {
            // Store user type in session variable
            $_SESSION['user_type'] = $row['user_type'];
    
            // Display user details
            // echo "<h2>User Details</h2>";
            echo "<h3>User Type: " . $row['user_type'] . "</h3>";
            echo "<p>Name: " . $row['name'] . "</p>";
            echo "<p>Address: " . $row['address'] . "</p>";
    
            // Display mobile number with an edit button
            echo "<p>Mobile Number: " . $row['mobile_number'] . "</p>";
            echo "<button onclick='toggleEdit(\"MobileNumber\")'>Edit Mobile Number</button>";
            echo "<div class='edit-form' id='editMobileNumber'>";
            echo "<form action='update_profile.php' method='post'>";
            echo "<label for='new_mobile'>New Mobile Number:</label><br>";
            echo "<input type='text' id='new_mobile' name='new_value' value='" . $row['mobile_number'] . "'><br><br>";
            echo "<input type='hidden' name='edit_field' value='mobile'>"; // Hidden input to specify the field being edited
            echo "<input type='hidden' name='username' value='" . $username . "'>"; // Hidden input to pass the username
            echo "<input type='hidden' name='user_type' value='" . $_SESSION['user_type'] . "'>"; // Hidden input to pass the user type
            echo "<input type='submit' value='Update Mobile Number'>";
            echo "</form>";
            echo "</div>";
    
            // Display email with an edit button
            echo "<p>Email: " . $row['email'] . "</p>";
            echo "<button onclick='toggleEdit(\"Email\")'>Edit Email</button>";
            echo "<div class='edit-form' id='editEmail'>";
            echo "<form action='update_profile.php' method='post'>";
            echo "<label for='new_email'>New Email:</label><br>";
            echo "<input type='email' id='new_email' name='new_value' value='" . $row['email'] . "'><br><br>";
            echo "<input type='hidden' name='edit_field' value='email'>"; // Hidden input to specify the field being edited
            echo "<input type='hidden' name='username' value='" . $username . "'>"; // Hidden input to pass the username
            echo "<input type='hidden' name='user_type' value='" . $_SESSION['user_type'] . "'>"; // Hidden input to pass the user type
            echo "<input type='submit' value='Update Email'>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "Error: " . pg_last_error($conn);
    }
    
    // Close the database connection
    pg_close($conn);
    ?>

    <!-- Button to direct to the next page -->
    
</div>

<script>
    function toggleEdit(field) {
        var editForm = document.getElementById("edit" + field);
        if (editForm.style.display === "none") {
            editForm.style.display = "block";
        } else {
            editForm.style.display = "none";
        }
    }
</script>

</body>
</html>
