<?php
// Database connection parameters
include 'connection.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if connection is successful
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize inputs to prevent SQL injection (you may need to use proper sanitization techniques)
    $username = pg_escape_string($conn, $username);
    $password = pg_escape_string($conn, $password);

    // SQL query to check if username and password match in any table
    $sql = "
        SELECT 'student' AS user_type, st_name AS username, st_password AS password FROM student
        WHERE st_name = '$username' AND st_password = '$password'
        UNION
        SELECT 'hostel' AS user_type, h_name AS username, h_password AS password FROM hostel
        WHERE h_name = '$username' AND h_password = '$password'
        UNION
        SELECT 'staff' AS user_type, s_name AS username, s_password AS password FROM staff
        WHERE s_name = '$username' AND s_password = '$password'
        UNION
        SELECT 'other' AS user_type, o_name AS username, o_password AS password FROM other
        WHERE o_name = '$username' AND o_password = '$password'
    ";

    // Additional condition for admin login
    if ($username === "admin" && $password === "pass") {
        // Redirect to another page for admin
        header("Location: daily.html");
        exit;
    }

    $result = pg_query($conn, $sql);

    // If result has at least one row, login successful
    if ($result && pg_num_rows($result) > 0) {
        // Start the session
        session_start();
        // Store username and user type in session variables
        $row = pg_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $password;
        // Redirect to the welcome page
        header("Location: pricing.php");
        exit; // Ensure script stops execution after redirection
    } else {
        // If username and password don't match, set error message and redirect back to login page
        $error_message = "Invalid username or password";
        header("Location: login1.php?error=" . urlencode($error_message));
        exit; // Ensure script stops execution after redirection
    }
}

// Close the connection
pg_close($conn);
?>
