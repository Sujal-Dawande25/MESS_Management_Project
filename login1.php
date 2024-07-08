<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sign in</title>
</head>
<body>
    <div class="main">
        <img src="sah1.png" alt="Your Logo" class="logo">
        <div class="welcome-container">
            <?php
            // Check if the username parameter is set in the URL
            if (isset($_GET['username'])) {
                // Display the welcome message with the username
                echo "<h2>Welcome, " . htmlspecialchars($_GET['username']) . "!</h2>";
            } else {
                // If the username parameter is not set, display a default welcome message
                echo "<h2>Welcome!</h2>";
            }
            ?>
        </div>
        <form class="form1" id="loginForm" action="login.php" method="post">
            <input class="un" type="text" id="username" name="username" placeholder="Username" required>
            <input class="pass" type="password" id="password" name="password" placeholder="Password" required>
            <!-- Display error message -->
            <div class="error-container"> <!-- Container to hold the error message -->
                <?php if (!empty($_GET['error'])): ?>
                    <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
            </div>
            <!-- Submit button -->
            <button class="submit" type="submit">Login</button>
            <!-- Move Remember Me and New User links inside the form1 container -->
            <div class="login-options">
                <center>
                    
                   
                    <p class="signup-link">New user? <a href="index.html">Sign up here</a></p>
                </center>
            </div>
        </form>
    </div>
</body>
</html>
