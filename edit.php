<?php
session_start();
require_once 'db_config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get current user data
$username = $_SESSION['user_id'];
$query = "SELECT username, name, surname FROM videogames_users WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($name) || empty($surname)) {
        $error = "El nom i el cognom són obligatoris";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = "Les contrasenyes no coincideixen";
    } else {
        if (empty($new_password)) {
            // Update without password change
            $update_query = "UPDATE videogames_users SET name = ?, surname = ? WHERE username = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sss", $name, $surname, $username);
        } else {
            // Update with password change
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = "UPDATE videogames_users SET name = ?, surname = ?, password = ? WHERE username = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ssss", $name, $surname, $hashed_password, $username);
        }

        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Perfil actualitzat correctament";
            // Update user data for display
            $user['name'] = $name;
            $user['surname'] = $surname;
        } else {
            $error = "Error en actualitzar el perfil";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Perfil</title>
    <style>
        * {
            font-family: sans-serif;
        }
        .edit-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[readonly] {
            background-color: #f5f5f5;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .submit-btn {
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div class="edit-container">
        <h2>Editar Perfil</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="edit.php">
            <div class="form-group">
                <label for="username">Nom d'usuari:</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="surname">Cognom:</label>
                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Nova Contrasenya: (deixar en blanc per mantenir l'actual)</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Nova Contrasenya:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit" class="submit-btn">Actualitzar Perfil</button>
        </form>
    </div>
</body>
</html>