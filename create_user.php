<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_config.php';

$db_conn = mysqli_connect($host, $username, $password, $database);

if (!$db_conn) {
    die("Connection failed: " . mysqli_connect_error() . 
        "<br>Host: $host" .
        "<br>Database: $database");
}

try {
    // Drop existing table if it exists
    $drop_table = "DROP TABLE IF EXISTS videogames_users";
    if (!mysqli_query($db_conn, $drop_table)) {
        throw new Exception("Error dropping table: " . mysqli_error($db_conn));
    }

    // Create table with correct structure
    $create_table = "CREATE TABLE videogames_users (
        username VARCHAR(50) PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        surname VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";

    if (!mysqli_query($db_conn, $create_table)) {
        throw new Exception("Error creating table: " . mysqli_error($db_conn));
    }

    // Create user
    $new_username = 'ignasimgol';
    $new_name = 'Ignasi';
    $new_surname = 'M';
    $new_password = password_hash('ignasimgol', PASSWORD_BCRYPT);

    // Check if user already exists
    $check_query = "SELECT username FROM videogames_users WHERE username = ?";
    $check_stmt = mysqli_prepare($db_conn, $check_query);
    
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($db_conn));
    }

    mysqli_stmt_bind_param($check_stmt, "s", $new_username);
    
    if (!mysqli_stmt_execute($check_stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($check_stmt));
    }
    
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo "User already exists";
    } else {
        // Insert new user
        $insert_query = "INSERT INTO videogames_users (username, name, surname, password) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($db_conn, $insert_query);
        
        if (!$insert_stmt) {
            throw new Exception("Prepare insert failed: " . mysqli_error($db_conn));
        }

        mysqli_stmt_bind_param($insert_stmt, "ssss", $new_username, $new_name, $new_surname, $new_password);

        if (mysqli_stmt_execute($insert_stmt)) {
            echo "User created successfully";
        } else {
            throw new Exception("Insert failed: " . mysqli_stmt_error($insert_stmt));
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
    if (isset($insert_stmt)) mysqli_stmt_close($insert_stmt);
    if (isset($db_conn)) mysqli_close($db_conn);
}
?>