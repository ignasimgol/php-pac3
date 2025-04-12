<?php
header('Content-Type: application/json');
require_once 'db_config.php';

// Connect to database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Remove the base path parts (adjust if needed)
$api_parts = array_slice($path_parts, array_search('api', $path_parts));

// Route the request
if (count($api_parts) >= 2) {
    switch ($api_parts[1]) {
        case 'videogames':
            // Get page number
            $page = isset($api_parts[2]) ? (int)$api_parts[2] : 1;
            getVideoGames($conn, $page);
            break;
            
        case 'videogame':
            // Get game ID
            if (isset($api_parts[2])) {
                getVideoGame($conn, (int)$api_parts[2]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Game ID required']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid API request']);
}

function getVideoGames($conn, $page) {
    $games_per_page = 10;
    $offset = ($page - 1) * $games_per_page;
    
    // Get total number of games
    $total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM videogames_jocs");
    $total_games = mysqli_fetch_assoc($total_result)['total'];
    $total_pages = ceil($total_games / $games_per_page);
    
    // Validate page number
    if ($page < 1 || ($total_games > 0 && $page > $total_pages)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid page number']);
        return;
    }
    
    // Get games for current page
    $query = "SELECT * FROM videogames_jocs LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $games_per_page, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $games = [];
    while ($game = mysqli_fetch_assoc($result)) {
        $games[] = $game;
    }
    
    echo json_encode([
        'page' => $page,
        'total_pages' => $total_pages,
        'total_games' => $total_games,
        'games_per_page' => $games_per_page,
        'games' => $games
    ]);
}

function getVideoGame($conn, $id) {
    $query = "SELECT * FROM videogames_jocs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($game = mysqli_fetch_assoc($result)) {
        echo json_encode($game);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Game not found']);
    }
}

mysqli_close($conn);
?>