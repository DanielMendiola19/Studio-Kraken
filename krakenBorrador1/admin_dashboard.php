<!-- admins_dashboard.php -->
<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php"); // Redirige al login si no es admin o no ha iniciado sesión
    exit();
}

include 'connection.php'; // Asegúrate de incluir la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Kraken</title>
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div id="banner">
            <h1>Panel de Control - Kraken</h1>
        </div>
        <nav>
            <ul>
                <li><a href="gestionar_clientes.php">Clientes</a></li>
                <li><a href="citas.php">Citas</a></li>
                <li><a href="tatuadores.php">Tatuadores</a></li>
                <li><a href="materiales.php">Materiales</a></li>
                <li><a href="servicios.php">Servicios</a></li>
                <li><a href="pagos.php">Pagos</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main style="color: white">
        <h2>Bienvenido al Panel de Administración, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h2>

        <div class="services">
            <div class="service-box">
                <h3>Gestión de Clientes</h3>
                <p>Administra la información de los clientes.</p>
            </div>
            <div class="service-box">
                <h3>Gestión de Citas</h3>
                <p>Visualiza y programa citas.</p>
            </div>
            <div class="service-box">
                <h3>Gestión de Tatuadores</h3>
                <p>Controla la información de los tatuadores.</p>
            </div>
            <div class="service-box">
                <h3>Gestión de Materiales</h3>
                <p>Mantén un registro de los materiales disponibles.</p>
            </div>
            <div class="service-box">
                <h3>Gestión de Servicios</h3>
                <p>Define y modifica los servicios ofrecidos.</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Kraken Tattoo Studio. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
