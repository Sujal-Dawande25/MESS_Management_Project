<?php
session_start(); // Start the session

// Function to check if the user has already subscribed to any plan
function hasSubscribed($username, $conn) {
    $query = "SELECT COUNT(*) AS num_plans FROM (
                  SELECT * FROM one WHERE o_username = $1
                  UNION ALL
                  SELECT * FROM two WHERE t_username = $1
              ) AS combined_plans";
    $result = pg_query_params($conn, $query, array($username));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['num_plans'] > 0;
    } else {
        return false;
    }
}

// Database connection parameters
include 'connection.php';

// Connect to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from session
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];

    // Function to get the user type based on username and password
    function getUserType($username, $password, $conn) {
        $query = "SELECT usertype FROM (
                      SELECT 'hostel' AS usertype, h_name AS username, h_password AS password FROM hostel
                      UNION ALL
                      SELECT 'staff' AS usertype, s_name AS username, s_password AS password FROM staff
                      UNION ALL
                      SELECT 'student' AS usertype, st_name AS username, st_password AS password FROM student
                      UNION ALL
                      SELECT 'other' AS usertype, o_name AS username, o_password AS password FROM other
                  ) AS users
                  WHERE username = $1 AND password = $2";
        $result = pg_query_params($conn, $query, array($username, $password));
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            return $row['usertype'];
        } else {
            return null;
        }
    }

    // Check if the user has already subscribed to any plan
    if (hasSubscribed($username, $conn)) {
        $error_message = "You have already subscribed to a plan.";
    } else {
        // Get the user type based on username and password
        $userType = getUserType($username, $password, $conn);

        // If user type is found, insert data into 'one' or 'two' table and redirect
        if ($userType) {
            if (isset($_POST['one'])) {
                // Insert data into 'one' table
                $startDate = date('d/m/y');
                $endDate = date('d/m/y', strtotime('+28 days'));

                $query = "INSERT INTO one (o_username, o_usertype, start_date, end_date, attendance,payment_status) VALUES ($1, $2, $3, $4, $5,$6)";
                $result = pg_query_params($conn, $query, array($username, $userType, $startDate, $endDate, 'Not Taken','Not Paid'));

                if ($result) {
                    // Data inserted successfully, redirect to payment page with username parameter
                    header("Location: payment.php?username=$username");
                    exit;
                } else {
                    $error_message = "Error inserting data into 'one' table: " . pg_last_error($conn);
                }
            } elseif (isset($_POST['two'])) {
                // Insert data into 'two' table
                $startDate = date('d/m/y');
                $endDate = date('d/m/y', strtotime('+28 days'));
                $query = "INSERT INTO two (t_username, t_usertype, start_date, end_date, attendance,payment_status) VALUES ($1, $2, $3, $4, $5,$6)";
                $result = pg_query_params($conn, $query, array($username, $userType, $startDate, $endDate, 'Not Taken','Not Paid'));

                if ($result) {
                    // Data inserted successfully, redirect to payment page with username parameter
                    header("Location: payment.php?username=$username");
                    exit;
                } else {
                    $error_message = "Error inserting data into 'two' table: " . pg_last_error($conn);
                }
            } else {
                $error_message = "Invalid subscription request.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}

// Close database connection
pg_close($conn);
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
             <!-- <li><a href="#">Home</a></li> -->
             <li><a href="about.html">About</a></li>
             <li><a href="contact.html">Contact</a></li>
             <li><a href="login1.php">Logout</a></li> 
        </ul>
        <img src="image/pro.jpg" alt="user-pic" class="user-pic" onclick="toggleMenu()">

        <div class="sub-menu-wrap" id="subMenu">
            <div class="sub-menu">
                <div class="user-info">
                    <img src="image/pro.jpg" alt="userimage">
                    <h3><?php echo $_SESSION['username']; ?></h3>
                </div>
                <hr>
                <a href="edit.php" class="sub-menu-link">
                    <img src="image/profile.png" alt="profileimage">
                    <p>Edit Profile</p>
                    <span>></span>
                </a>
                <a href="faq.html" class="sub-menu-link">
                    <img src="image/setting.png" alt="settingimage">
                    <p>FAQ</p>
                    <span>></span>
                </a>
               
                <a href="login1.php" class="sub-menu-link">
                    <img src="image/logout.png" alt="profileimage">
                    <p>Logout</p>
                    <span>></span>
                </a>
            </div>
        </div>
    </nav>
    <h1 class="display-4 fw-normal text-body-emphasis">Welcome to S.A.H mess!!</h1>
    <div class="pricing-table">
        <!-- Content from the second HTML file (pricing table) goes here -->
        <div class="plan">
            <h2>1 Month lunch+dinner</h2>
            <p class="price">&#8377 3200</p>
            <ul>
                <li>Delicious food</li>
                <li>Sunday special fest</li>    
                <li>4 holidays are allowed in a month</li>
            </ul>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="subscription_type" value="two"> 
                <button type="submit" name="two">Subscribe</button>
            </form>
        </div>
        <div class="plan">
            <h2>1 Month/1time(lunch or dinner)</h2>
            <p class="price">&#8377 1800</p>
            <ul>
                <li>Delicious Food</li>
                <li>Sunday special fest</li>
                <li>4 holidays are allowed in a month</li>
            </ul>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="subscription_type" value="one"> 
                <button type="submit" name="one">Subscribe</button>
            </form>
        </div>
    </div>
    <?php
  if (isset($error_message)) {
    echo "<div class='error-message'>$error_message</div>";
    echo "<div style='text-align: center;'>";
    echo "<button onclick=\"window.location.href='info.php'\">Information</button>";
    echo "</div>";
}
?>

</div>

<script>
    let subMenu = document.getElementById("subMenu");
    function toggleMenu() {
        subMenu.classList.toggle("open-menu");
    }
</script>

<footer>
    <div class="footer-content">
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <p>&copy; 2023 S.a.H Mess Company...</p>
    </div>
</footer>

</body>
</html>
