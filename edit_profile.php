<?php
session_start();

// Check if username is set in the session
if (isset($_SESSION['username'])) {
    $welcome_message = "Welcome " . $_SESSION['username'];
} else {
    // If username is not set, redirect to the login page
    header("Location: login1.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="drop.css"> <!-- Include your existing styles -->
    <link rel="stylesheet" href="pricing.css"> <!-- Include styles from the second file -->
    <title>Combined Page</title>
</head>
<body>

<div class="hero">
    <nav>
        <img src="image/sah1.png" alt="logo" class="logo">
        <ul>
             <!-- ... other menu items ... -->
             <li><a href="about.html">About</a></li>
             <li><a href="contact.html">Contact</a></li>
             <li><a href="login1.php">Logout</a></li>
             <li><a href="edit_profile.php">Edit Profile</a></li> <!-- Add this line for Edit Profile option -->
        </ul>
        <img src="image/pro.jpg" alt="user-pic" class="user-pic" onclick="toggleMenu()">

        <div class="sub-menu-wrap" id="subMenu">
            <!-- ... existing sub-menu content ... -->
        </div>
    </nav>
    <!-- ... existing content ... -->
</div>

<script>
    // ... existing script ...
</script>

<footer>
    <!-- ... existing footer content ... -->
</footer>

</body>
</html>
