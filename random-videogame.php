<?php
require_once 'db_config.php';


$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM videogames_jocs ORDER BY RAND() LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result) {
    $videogame = mysqli_fetch_assoc($result);
    if ($videogame) {
        echo "<h1>Random Video Game</h1>";
        echo "<table border='1'>";
        foreach ($videogame as $field => $value) {
            echo "<tr>";
            echo "<th>" . htmlspecialchars($field) . "</th>";
            echo "<td>" . htmlspecialchars($value) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No video games found in the database.";
    }
} else {
    echo "Error executing query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>