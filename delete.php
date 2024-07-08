<?php
// Include database connection parameters
include 'connection.php';

// Get the ID, name, and table name from the form
$name = $_POST['name'];
$id = $_POST['id'];
$table = $_POST['table'];

// Establish a connection to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the connection is successful
if (!$conn) {
    echo "Error: Unable to connect to the database.";
} else {
    // Delete the record from the specified table
    $query = "DELETE FROM $table WHERE {$table}_id = $id";
    $result = pg_query($conn, $query);

    if ($result) {
        // Record deleted successfully from the specified table
        
        // Check if the name exists in table one
        $check_query_one = "SELECT COUNT(*) FROM one WHERE o_username = $1";
        $check_result_one = pg_query_params($conn, $check_query_one, array($name));
        $row_one = pg_fetch_row($check_result_one);
        $count_one = $row_one[0];

        if ($count_one > 0) {
            // Delete the record from table one
            $delete_query_one = "DELETE FROM one WHERE o_username = $1";
            $delete_result_one = pg_query_params($conn, $delete_query_one, array($name));
            if (!$delete_result_one) {
                echo "Error deleting record from table one: " . pg_last_error($conn);
                exit();
            }
            
            // Delete the record from payment_one
            $delete_payment_query = "DELETE FROM payment_one WHERE o_name = $1";
            $delete_payment_result = pg_query_params($conn, $delete_payment_query, array($name));
            if (!$delete_payment_result) {
                echo "Error deleting record from payment_one: " . pg_last_error($conn);
                exit();
            }
        } else {
            // Check if the name exists in table two
            $check_query_two = "SELECT COUNT(*) FROM two WHERE t_username = $1";
            $check_result_two = pg_query_params($conn, $check_query_two, array($name));
            $row_two = pg_fetch_row($check_result_two);
            $count_two = $row_two[0];

            if ($count_two > 0) {
                // Delete the record from table two
                $delete_query_two = "DELETE FROM two WHERE t_username = $1";
                $delete_result_two = pg_query_params($conn, $delete_query_two, array($name));
                if (!$delete_result_two) {
                    echo "Error deleting record from table two: " . pg_last_error($conn);
                    exit();
                }
                
                // Delete the record from payment_two
                $delete_payment_query = "DELETE FROM payment_two WHERE t_name = $1";
                $delete_payment_result = pg_query_params($conn, $delete_payment_query, array($name));
                if (!$delete_payment_result) {
                    echo "Error deleting record from payment_two: " . pg_last_error($conn);
                    exit();
                }
            } else {
                // Name not found in both tables one and two
                echo "Error: Name not found in tables one or two.";
                exit();
            }
        }

        // Redirect back to the page with deletion success message
        header("Location: {$_SERVER['HTTP_REFERER']}?deleted=true");
        exit();
    } else {
        echo "Error deleting record from $table: " . pg_last_error($conn);
    }
}

// Close the database connection
pg_close($conn);
?>
