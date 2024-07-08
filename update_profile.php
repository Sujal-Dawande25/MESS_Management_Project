<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($_SESSION['user_type']) || empty($_SESSION['user_type'])) {
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

// Retrieve username, user type, and new values from the form
$username = $_SESSION['username'];
$userType = $_SESSION['user_type'];

// Ensure the form fields are set and not empty
if(isset($_POST['edit_field']) && isset($_POST['new_value'])) {
    $editField = $_POST['edit_field'];
    $newValue = $_POST['new_value'];

    // Prepare the query to update the database based on the user type
    $updateQuery = "";
     echo "User Type: " . $userType;
    switch ($userType) {
        case 'Hostel':
            if ($editField === 'mobile') {
                $updateQuery = "UPDATE hostel SET h_mobile_number = '$newValue' WHERE h_name = '$username'";
            } elseif ($editField === 'email') {
                $updateQuery = "UPDATE hostel SET h_email = '$newValue' WHERE h_name = '$username'";
            }
            break;
        case 'Staff':
            if ($editField === 'mobile') {
                $updateQuery = "UPDATE staff SET s_mobile_number = '$newValue' WHERE s_name = '$username'";
            } elseif ($editField === 'email') {
                $updateQuery = "UPDATE staff SET s_email = '$newValue' WHERE s_name = '$username'";
            }
            break;
        case 'Student':
            if ($editField === 'mobile') {
                $updateQuery = "UPDATE student SET st_mobile_number = '$newValue' WHERE st_name = '$username'";
            } elseif ($editField === 'email') {
                $updateQuery = "UPDATE student SET st_email = '$newValue' WHERE st_name = '$username'";
            }
            break;
        case 'Other':
            if ($editField === 'mobile') {
                $updateQuery = "UPDATE other SET o_mobile_number = '$newValue' WHERE o_name = '$username'";
            } elseif ($editField === 'email') {
                $updateQuery = "UPDATE other SET o_email = '$newValue' WHERE o_name = '$username'";
            }
            break;
        default:
            echo "Invalid user type";
            exit;
    }

    // Execute the update query
    $result = pg_query($conn, $updateQuery);

    if ($result) {
        // Redirect the user to the details page after successful update
        header("Location: edit.php?edit_success=true");
        exit;
    } else {
        echo "Error updating record: " . pg_last_error($conn);
    }
} else {
    // Handle case where form fields are not set or empty
    echo "Form fields are not properly set or empty";
}

// Close the database connection
pg_close($conn);
?>
