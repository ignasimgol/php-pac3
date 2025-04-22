<?php
require_once 'db_config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Random Video Game</title>
    <style>
        .game-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .game-image {
            width: 100%;
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto 20px;
        }
        .game-details {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
        }
        .field-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="game-container">
        <?php
        $query = "SELECT * FROM videogames_jocs ORDER BY RAND() LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && $videogame = mysqli_fetch_assoc($result)) {
            echo "<h1>" . htmlspecialchars($videogame['titol']) . "</h1>";
            
            // Display image
            echo "<img src='" . htmlspecialchars($videogame['imatge']) . "' 
                      alt='" . htmlspecialchars($videogame['titol']) . "' 
                      class='game-image'>";
            
            echo "<div class='game-details'>";
            foreach ($videogame as $field => $value) {
                if ($field !== 'imatge') { // Skip image field as it's already displayed
                    echo "<div class='field-label'>" . htmlspecialchars(ucfirst($field)) . ":</div>";
                    echo "<div>" . htmlspecialchars($value) . "</div>";
                }
            }
            echo "</div>";
        } else {
            echo "<p>No video games found in the database.</p>";
        }

        mysqli_close($conn);
        ?>
    </div>
</body>
</html>