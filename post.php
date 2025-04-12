<?php
require_once 'db_config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get game ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get game details
$query = "SELECT * FROM videogames_jocs WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$game = mysqli_fetch_assoc($result);

if (!$game) {
    die("Joc no trobat");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($game['titol']); ?></title>
    <style>
        .game-details {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .game-details img {
            max-width: 100%;
            height: auto;
        }
        .field {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="game-details">
        <h1><?php echo htmlspecialchars($game['titol']); ?></h1>
        <img src="<?php echo htmlspecialchars($game['imatge']); ?>" 
             alt="<?php echo htmlspecialchars($game['titol']); ?>">
        
        <?php
        foreach ($game as $field => $value) {
            if ($field != 'imatge') {
                echo "<div class='field'>";
                echo "<strong>" . htmlspecialchars(ucfirst($field)) . ":</strong> ";
                echo htmlspecialchars($value);
                echo "</div>";
            }
        }
        ?>
        
        <p><a href="videogames.php">Tornar a la llista de jocs</a></p>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>