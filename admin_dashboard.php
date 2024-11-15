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
    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            color: #333;
        }

        body {
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Estilos del encabezado */
        header {
            background-color: #2c3e50;
            padding: 20px;
            color: #ecf0f1;
            text-align: center;
        }

        header #banner h1 {
            font-size: 2.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Menú de navegación */
        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        nav a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1em;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #34495e;
        }

        /* Estilos del contenido principal */
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            flex: 1;
            background: linear-gradient(to bottom, #2c3e50 0%, #2c3e50 40%, #f0f2f5 40%, #f0f2f5 100%);
        }

        main h2 {
            color: #ecf0f1;
            margin-bottom: 30px;
            font-size: 1.8em;
        }

        /* Contenedor de servicios */
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
        }

        .service-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-box h3 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .service-box p {
            font-size: 1em;
            color: #666;
        }

        .service-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Estilos del pie de página */
        footer {
            background-color: #2c3e50;
            padding: 15px;
            color: #ecf0f1;
            text-align: center;
            font-size: 0.9em;
        }

        footer p {
            margin: 0;
        }
    </style>
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

    <main>
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

