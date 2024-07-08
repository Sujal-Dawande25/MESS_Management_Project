<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Details</title>
    <link rel="stylesheet" href="details.css"> <!-- Link to your CSS file -->
</head>

<body>
<div class="container">
        <div class="header">
            <h1 class="details-heading">Database Details</h1>
            <div class="back-button">
                <a href="daily.html">Back</a>
            </div>
        </div>
        <!-- Display success message if record is deleted -->
        <?php
        if (isset($_GET['deleted']) && $_GET['deleted'] == 'true') {
            echo "<div style='background-color: #d4edda; color: #155724; padding: 10px;'>Record deleted successfully.</div>";
        }
        ?>

        <!-- Hostel Details -->
        <h2>Hostel Details</h2>
        <table class="details-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Block No</th>
                    <th>Room No</th>
                    <th>Action</th>
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
                    echo "<tr><td colspan='9'>Error: Unable to connect to the database.</td></tr>";
                } else {
                    // Query to fetch hostel details
                    $query = "SELECT * FROM hostel";

                    // Execute the query
                    $result = pg_query($conn, $query);

                    // Check if there are any rows returned
                    if (pg_num_rows($result) > 0) {
                        // Loop through each row and display the data
                        while ($row = pg_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['hostel_id'] . "</td>";
                            echo "<td>" . $row['h_name'] . "</td>";
                            echo "<td>" . $row['h_address'] . "</td>";
                            echo "<td>" . $row['h_mobile_number'] . "</td>";
                            echo "<td>" . $row['h_email'] . "</td>";
                            echo "<td>" . $row['h_password'] . "</td>";
                            echo "<td>" . $row['block_no'] . "</td>";
                            echo "<td>" . $row['room_no'] . "</td>";
                            echo "<td>
                                 <form method='POST' action='delete.php' onsubmit='return confirmDelete()'>
                                 <input type='hidden' name='id' value='" . $row['hostel_id'] . "'>
                                 <input type='hidden' name='name' value='" . $row['h_name'] . "'>
                                 <input type='hidden' name='table' value='hostel'> <!-- Specify the table -->
                                 <button type='submit'>Delete</button>
                                </form>
                                 </td>";
                                 echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No records found.</td></tr>";
                            }
                        }
                        ?>
            </tbody>
        </table>
        
        <!-- Staff Details -->
        <h2>Staff Details</h2>
        <table class="details-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch staff details
                $query = "SELECT * FROM staff";
                
                // Execute the query
                $result = pg_query($conn, $query);
                
                // Check if there are any rows returned
                if (pg_num_rows($result) > 0) {
                    // Loop through each row and display the data
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['staff_id'] . "</td>";
                        echo "<td>" . $row['s_name'] . "</td>";
                        echo "<td>" . $row['s_address'] . "</td>";
                        echo "<td>" . $row['s_mobile_number'] . "</td>";
                        echo "<td>" . $row['s_email'] . "</td>";
                        echo "<td>" . $row['s_password'] . "</td>";
                        echo "<td>
                        <form method='POST' action='delete.php' onsubmit='return confirmDelete()'>
                        <input type='hidden' name='id' value='" . $row['staff_id'] . "'>
                        <input type='hidden' name='name' value='" . $row['s_name'] . "'>
                        <input type='hidden' name='table' value='staff'> <!-- Specify the table -->
                        <button type='submit'>Delete</button>
                        </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Student Details -->
        <h2>Student Details</h2>
        <table class="details-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch student details
                $query = "SELECT * FROM student";
                
                // Execute the query
                $result = pg_query($conn, $query);
                
                // Check if there are any rows returned
                if (pg_num_rows($result) > 0) {
                    // Loop through each row and display the data
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['student_id'] . "</td>";
                        echo "<td>" . $row['st_name'] . "</td>";
                        echo "<td>" . $row['st_address'] . "</td>";
                        echo "<td>" . $row['st_mobile_number'] . "</td>";
                        echo "<td>" . $row['st_email'] . "</td>";
                        echo "<td>" . $row['st_password'] . "</td>";
                        echo "<td>
                             <form method='POST' action='delete.php' onsubmit='return confirmDelete()'>
                             <input type='hidden' name='id' value='" . $row['student_id'] . "'>
                             <input type='hidden' name='name' value='" . $row['st_name'] . "'>
                             <input type='hidden' name='table' value='student'> <!-- Specify the table -->
                             <button type='submit'>Delete</button>
                            </form>
                             </td>";
                             echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Other Details -->
        <h2>Other Details</h2>
        <table class="details-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
// Query to fetch other details
$query = "SELECT * FROM other";

// Execute the query
$result = pg_query($conn, $query);

// Check if there are any rows returned
if (pg_num_rows($result) > 0) {
    // Loop through each row and display the data
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['other_id'] . "</td>";
        echo "<td>" . $row['o_name'] . "</td>";
        echo "<td>" . $row['o_address'] . "</td>";
        echo "<td>" . $row['o_mobile_number'] . "</td>";
        echo "<td>" . $row['o_email'] . "</td>";
        echo "<td>" . $row['o_password'] . "</td>";
        echo "<td>
             <form method='POST' action='delete.php'  onsubmit='return confirmDelete()'>
             <input type='hidden' name='id' value='" . $row['other_id'] . "'>
             <input type='hidden' name='name' value='" . $row['o_name'] . "'>
             <input type='hidden' name='table' value='other'> <!-- Specify the table -->
             <button type='submit'>Delete</button>
            </form>
             </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No records found.</td></tr>";
}
?>
</tbody>
</table>
</div>
<script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>
</body>
</html>

