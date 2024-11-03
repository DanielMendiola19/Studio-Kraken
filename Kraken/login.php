<!-- login.php -->
<?php
include 'connection.php';
session_start();
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
    header("Location: admin_dashboard.php"); // Redirige al dashboard si ya está logueado
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Kraken</title>
    <link rel="stylesheet" href="css/estilo.css"> 
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">


</head>
<body style='color: white'>
<header>
    <div id="banner"> 
        <img class="logoBanner" src="recursos/logo.jpg" alt="logoKraken">
    </div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="galeria.php">Galería</a></li>
        <li><a href="#">Tatuadores</a></li>
        <li><a href="#">Servicios</a></li>
        <li><a href="#">Contacto</a></li>
        <li><a href="login.php">Acceso Admin</a></li>
    </ul>
</nav>

    <main>
        <h2>Iniciar Sesión - Administrador</h2>
        <form action="login.php" method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Iniciar Sesión</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include 'connection.php';
            
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Hashea la contraseña antes de compararla
            $hashed_password = MD5($password);

            // Modifica la consulta para usar los nombres correctos de las columnas
            $query = "SELECT * FROM usuarios WHERE email_usuario = '$email' AND password_usuario = '$password'";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['rol'] = $row['rol'];
                $_SESSION['nombre_usuario'] = $row['nombre_usuario'];
                header("Location: admin_dashboard.php"); // Redirige al dashboard
                exit();
            } else {
                echo "<p style='color:red;'>Credenciales incorrectas.</p>";
            }
        }
        ?>
    </main>

    <footer>
        <p>© 2024 Kraken. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
