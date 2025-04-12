<?php
require_once 'db_config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get filter and sort parameters
$desenvolupador = isset($_GET['desenvolupador']) ? mysqli_real_escape_string($conn, $_GET['desenvolupador']) : '';
$genere = isset($_GET['genere']) ? mysqli_real_escape_string($conn, $_GET['genere']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Base query
$query = "SELECT id, titol, desenvolupador, plataforma, imatge, any_llancament, genere 
          FROM videogames_jocs 
          WHERE 1=1";

// Add filters if present
if ($desenvolupador) {
    $query .= " AND desenvolupador LIKE '%$desenvolupador%'";
}
if ($genere) {
    $query .= " AND genere LIKE '%$genere%'";
}

// Add sorting if present
if ($sort === 'any') {
    $query .= " ORDER BY any_llancament $order";
}

// Only apply pagination if no filters are active
if (!$desenvolupador && !$genere) {
    $games_per_page = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $games_per_page;
    
    // Get total games for pagination
    $total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM videogames_jocs");
    $total_games = mysqli_fetch_assoc($total_result)['total'];
    $total_pages = ceil($total_games / $games_per_page);
    
    if ($page < 1 || $page > $total_pages) {
        $page = 1;
        $offset = 0;
    }
    
    $query .= " LIMIT $offset, $games_per_page";
}

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error de connexió: " . mysqli_error($conn));
}

// Get unique developers and genres for filters
$devs_query = "SELECT DISTINCT desenvolupador FROM videogames_jocs ORDER BY desenvolupador";
$devs_result = mysqli_query($conn, $devs_query);

$genres_query = "SELECT DISTINCT genere FROM videogames_jocs ORDER BY genere";
$genres_result = mysqli_query($conn, $genres_query);
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
        .filters {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .filters select, .filters a {
            margin: 0 10px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h1>Videojocs</h1>
    
    <div class="filters">
        <form method="get">
            <select name="desenvolupador">
                <option value="">Tots els desenvolupadors</option>
                <?php while ($dev = mysqli_fetch_assoc($devs_result)): ?>
                    <option value="<?php echo htmlspecialchars($dev['desenvolupador']); ?>"
                            <?php echo $desenvolupador === $dev['desenvolupador'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dev['desenvolupador']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="genere">
                <option value="">Tots els gèneres</option>
                <?php while ($genre = mysqli_fetch_assoc($genres_result)): ?>
                    <option value="<?php echo htmlspecialchars($genre['genere']); ?>"
                            <?php echo $genere === $genre['genere'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['genere']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="sort">
                <option value="">Sense ordenar</option>
                <option value="any" <?php echo $sort === 'any' ? 'selected' : ''; ?>>Any de llançament</option>
            </select>

            <select name="order">
                <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascendent</option>
                <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descendent</option>
            </select>

            <button type="submit">Filtrar</button>
            <a href="videogames.php">Netejar filtres</a>
        </form>
    </div>

    <div class="game-list">
        <?php while ($game = mysqli_fetch_assoc($result)): ?>
            <div class='game-card'>
                <img src='<?php echo htmlspecialchars($game['imatge']); ?>' 
                     alt='<?php echo htmlspecialchars($game['titol']); ?>'>
                <h2><a href='post.php?id=<?php echo $game['id']; ?>'>
                    <?php echo htmlspecialchars($game['titol']); ?>
                </a></h2>
                <p>Desenvolupador: <?php echo htmlspecialchars($game['desenvolupador']); ?></p>
                <p>Plataforma: <?php echo htmlspecialchars($game['platforma']); ?></p>
                <p>Any: <?php echo htmlspecialchars($game['any_llancament']); ?></p>
                <p>Gènere: <?php echo htmlspecialchars($game['genere']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
    
    <?php if (!$desenvolupador && !$genere): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php $active = $i == $page ? ' class="active"' : ''; ?>
                <a href="?page=<?php echo $i; ?><?php echo $sort ? "&sort=$sort&order=$order" : ''; ?>"<?php echo $active; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</body>
</html>

<?php mysqli_close($conn); ?>