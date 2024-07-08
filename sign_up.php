<?php

include 'connection.php';
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isUsernameUnique($username, $conn) {
    // Check if username exists in any table (hostel, other, student, staff)
    $query = "SELECT COUNT(*) FROM (
                SELECT h_name AS username FROM hostel UNION ALL
                SELECT o_name AS username FROM other UNION ALL
                SELECT st_name AS username FROM student UNION ALL
                SELECT s_name AS username FROM staff
             ) AS all_usernames WHERE username = $1";

    $result = pg_query_params($conn, $query, array($username));
    if ($result) {
        $row = pg_fetch_row($result);
        $count = $row[0];
        return $count == 0; // Returns true if username is unique, false otherwise
    } else {
        die("Error checking username uniqueness: " . pg_last_error($conn));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = clean_input($_POST["name"]);
    $address = clean_input($_POST["address"]);
    $mobile = clean_input($_POST["mobile"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);
    $userType = clean_input($_POST["hello"]);
    $blockNo = clean_input($_POST["option1"]);
    $roomNo = clean_input($_POST["option2"]);

    // Check if the username is unique
    if (!isUsernameUnique($name, $conn)) {
        die("Error: Username already exists");
    }

    $stmt = null;

    if (!empty($userType)) {
        // Set the primary key based on the user type
        switch ($userType) {
            case "student":
                $primaryKey = "student_id";
                break;
            case "hostel":
                $primaryKey = "hostel_id";
                break;
            case "staff":
                $primaryKey = "staff_id";
                break;
            case "other":
                $primaryKey = "other_id";
                break;
            default:
                die("Invalid user type");
        } 
        function getNextId($table, $primaryKey, $conn) {
            $query = "SELECT MAX($primaryKey) FROM $table";
            $result = pg_query($conn, $query);
        
            if ($result) {
                $row = pg_fetch_row($result);
                $maxId = $row[0];
                // Increment the maximum ID by 1
                $nextId = $maxId + 1;
                return $nextId;
            } else {
                die("Error getting next ID for $table: " . pg_last_error($conn));
            }
        }

        // Get the next ID using the correct primary key
        $nextId = getNextId($userType, $primaryKey, $conn);

        // Prepare the SQL statement based on the user type and primary key
        switch ($userType) {
            case "student":
                $stmt = pg_prepare($conn, "student_insert", "INSERT INTO student (student_id, st_name, st_address, st_mobile_number, st_email, st_password) VALUES ($1, $2, $3, $4, $5, $6)");
                break;
            case "hostel":
                $stmt = pg_prepare($conn, "hostel_insert", "INSERT INTO hostel (hostel_id, h_name, h_address, h_mobile_number, h_email, h_password, block_no, room_no) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)");
                break;
            case "staff":
                $stmt = pg_prepare($conn, "staff_insert", "INSERT INTO staff (staff_id, s_name, s_address, s_mobile_number, s_email, s_password) VALUES ($1, $2, $3, $4, $5, $6)");
                break;
            case "other":
                $stmt = pg_prepare($conn, "other_insert", "INSERT INTO other (other_id, o_name, o_address, o_mobile_number, o_email, o_password) VALUES ($1, $2, $3, $4, $5, $6)");
                break;
            default:
                die("Invalid user type");
        }

        if ($stmt) {
            // Execute the prepared statement with the appropriate parameters
            switch ($userType) {
                case "student":
                case "staff":
                case "other":
                    $result = pg_execute($conn, $userType . "_insert", array($nextId, $name, $address, $mobile, $email, $password)); 
                    break;
                case "hostel":
                    $result = pg_execute($conn, "hostel_insert", array($nextId, $name, $address, $mobile, $email, $password, $blockNo, $roomNo)); 
                    break;
                default:
                    die("Invalid user type");
            }

            if ($result) {
                echo "Record inserted successfully";
                // Redirect to the next page after successful insertion
                header("Location: login1.php?username=" . urlencode($name));
                exit;
            } else {
                echo "Error: " . pg_last_error($conn);
            }
        } else {
            echo "Error preparing statement";
        }
    } else {
        echo "User type not selected";
    }

    if ($stmt) {
        pg_free_result($stmt);
    }
}

pg_close($conn);

?>
