<?php
require_once 'db_config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Pagination setup
$games_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $games_per_page;

// Get total number of games
$total_query = "SELECT COUNT(*) as total FROM videogames_jocs";
$total_result = mysqli_query($conn, $total_query);
if (!$total_result) {
    die("Error de connexió: " . mysqli_error($conn));
}
$total_games = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_games / $games_per_page);

// Validate page number
if ($page < 1 || $page > $total_pages) {
    $page = 1;
    $offset = 0;
}

// Get games for current page
$query = "SELECT id, titol, desenvolupador, plataforma, imatge FROM videogames_jocs 
          LIMIT $offset, $games_per_page";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error de connexió: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Videojocs</title>
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
        .pagination {
            margin: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            text-decoration: none;
        }
        .pagination a.active {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h1>Videojocs</h1>
    <div class="game-list">
        <?php
        while ($game = mysqli_fetch_assoc($result)) {
            echo "<div class='game-card'>";
            echo "<img src='" . htmlspecialchars($game['imatge']) . "' alt='" . htmlspecialchars($game['titol']) . "'>";
            echo "<h2><a href='post.php?id=" . $game['id'] . "'>" . htmlspecialchars($game['titol']) . "</a></h2>";
            echo "<p>Desenvolupador: " . htmlspecialchars($game['desenvolupador']) . "</p>";
            echo "<p>Plataforma: " . htmlspecialchars($game['platforma']) . "</p>";
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i == $page ? ' class="active"' : '';
            echo "<a href='?page=$i'$active>$i</a>";
        }
        ?>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>