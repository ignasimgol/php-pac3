<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<style>
    * {
        font-family: sans-serif;
    }
    .main-nav {
        background-color: #333;
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 34px;
        text-align: center;
        justify-content: center;    
        justify-items: center;
        max-width: 50%;
        margin: 0 auto;
    }
    .nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .nav-list li a {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .nav-list li a:hover {
        background-color: #555;
    }
    @media (max-width: 768px) {
        .nav-list {
            flex-direction: column;
        }
    }
</style>

<nav class="main-nav">
    <ul class="nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="random-videogame.php">Videojoc Aleatori</a></li>
        <li><a href="videogames.php">Videojocs</a></li>
        <li><a href="api/videogame/1" target="_blank">API Videojoc</a></li>
        
        <?php if (!$is_logged_in): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Sign up</a></li>
        <?php else: ?>
            <li><a href="edit.php">Perfil d'usuari</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>