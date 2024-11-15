<?php
session_start();
include 'connection.php';

// Verificar si el usuario ya está logueado y ha pasado la fase de verificación
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión activa
    exit();
}

// Verificar que el formulario haya sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];  // Obtener el código ingresado por el usuario
    $id_usuario = $_SESSION['id_usuario'];  // Obtener el ID del usuario de la sesión

    // Verificar si el código ingresado es correcto
    $query = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario AND codigo_verificacion = '$codigo'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Código correcto, permitir acceso
        $_SESSION['verificado'] = true;  // Marcamos al usuario como verificado
        header("Location: admin_dashboard.php"); // Redirigir al dashboard
        exit();
    } else {
        // Código incorrecto
        echo "<p style='color:red;'>Código de verificación incorrecto. Por favor, inténtalo de nuevo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación - Kraken</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body style="color: white;">
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
        <h2>Verificación de Cuenta - Administrador</h2>
        <form action="verify.php" method="POST">
            <label for="codigo">Código de Verificación:</label>
            <input type="text" id="codigo" name="codigo" required>
            <button type="submit">Verificar</button>
        </form>
    </main>

    <footer>
        <p>© 2024 Kraken. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
