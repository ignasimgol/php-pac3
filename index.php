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
        * {
            font-family: sans-serif;
        }
        .game-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .game-card {
            border: 1px solid #ddd;
            padding: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .game-card-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
        }
        .game-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .game-card-content {
            padding: 20px;
        }
        .game-card h2 {
            margin: 0 0 15px 0;
            font-size: 1.4em;
        }
        .game-card h2 a {
            color: #333;
            text-decoration: none;
        }
        .game-card h2 a:hover {
            color: #666;
        }
        .game-card p {
            margin: 8px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <?php if (isset($_SESSION['username'])): ?>
    <div style="margin: 10px 20px;">
        Benvingut, <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </div>
    <?php endif; ?>
    <h1>Videojocs</h1>
    <div class="game-list">
        <?php
        while ($game = mysqli_fetch_assoc($fixed_result)) {
            echo "<div class='game-card'>";
            echo "<div class='game-card-image'>";
            echo "<img src='" . htmlspecialchars($game['imatge']) . "' alt='" . htmlspecialchars($game['titol']) . "'>";
            echo "</div>";
            echo "<div class='game-card-content'>";
            echo "<h2><a href='post.php?id=" . $game['id'] . "'>" . htmlspecialchars($game['titol']) . "</a></h2>";
            echo "<p><strong>Desenvolupador:</strong> " . htmlspecialchars($game['desenvolupador']) . "</p>";
            echo "<p><strong>Plataforma:</strong> " . htmlspecialchars($game['plataforma']) . "</p>";
            echo "</div>";
            echo "</div>";
        }

        while ($game = mysqli_fetch_assoc($random_result)) {
            echo "<div class='game-card'>";
            echo "<div class='game-card-image'>";
            echo "<img src='" . htmlspecialchars($game['imatge']) . "' alt='" . htmlspecialchars($game['titol']) . "'>";
            echo "</div>";
            echo "<div class='game-card-content'>";
            echo "<h2><a href='post.php?id=" . $game['id'] . "'>" . htmlspecialchars($game['titol']) . "</a></h2>";
            echo "<p><strong>Desenvolupador:</strong> " . htmlspecialchars($game['desenvolupador']) . "</p>";
            echo "<p><strong>Plataforma:</strong> " . htmlspecialchars($game['plataforma']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="videogames.php" style="display: inline-block; background-color: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            Veure tots els videojocs
        </a>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
