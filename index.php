<?php
require_once 'db_config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$fixed_query = "SELECT id, titol, desenvolupador, plataforma, imatge FROM videogames_jocs WHERE id IN (1, 2)";
$fixed_result = mysqli_query($conn, $fixed_query);

$random_query = "SELECT id, titol, desenvolupador, plataforma, imatge FROM videogames_jocs 
                 WHERE id NOT IN (1, 2) 
                 ORDER BY RAND() LIMIT 3";
$random_result = mysqli_query($conn, $random_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Games Collection</title>
    <style>
        .game-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .game-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }
        .game-card img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <?php if (isset($_SESSION['username'])): ?>
    <div style="text-align: center; margin: 10px 0;">
        Benvingut, <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </div>
    <?php endif; ?>
    <h1>Videojocs</h1>
    <div class="game-list">
        <?php
        while ($game = mysqli_fetch_assoc($fixed_result)) {
            echo "<div class='game-card'>";
            echo "<img src='" . htmlspecialchars($game['imatge']) . "' alt='" . htmlspecialchars($game['titol']) . "'>";
            echo "<h2><a href='post.php?id=" . $game['id'] . "'>" . htmlspecialchars($game['titol']) . "</a></h2>";
            echo "<p>Desenvolupador: " . htmlspecialchars($game['desenvolupador']) . "</p>";
            echo "<p>Plataforma: " . htmlspecialchars($game['plataforma']) . "</p>";
            echo "</div>";
        }

        while ($game = mysqli_fetch_assoc($random_result)) {
            echo "<div class='game-card'>";
            echo "<img src='" . htmlspecialchars($game['imatge']) . "' alt='" . htmlspecialchars($game['titol']) . "'>";
            echo "<h2><a href='post.php?id=" . $game['id'] . "'>" . htmlspecialchars($game['titol']) . "</a></h2>";
            echo "<p>Desenvolupador: " . htmlspecialchars($game['desenvolupador']) . "</p>";
            echo "<p>Plataforma: " . htmlspecialchars($game['plataforma']) . "</p>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
