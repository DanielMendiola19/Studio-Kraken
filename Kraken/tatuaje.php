<?php
// Incluir el archivo de conexión
include 'connection.php';

// Recuperar el ID del tatuaje desde la URL
$id_tatuaje = isset($_GET['id']) ? $_GET['id'] : null;

// Inicializar variables para los detalles del tatuaje
$diseño = $tamaño = $zona_del_cuerpo = $nivel_de_detalle = $precio = $foto_del_diseño = "";

// Verificar si se proporcionó un ID de tatuaje
if ($id_tatuaje !== null) {
    // Consulta para obtener los detalles del tatuaje
    $stmt = $conn->prepare("SELECT diseño, tamaño, zona_del_cuerpo, nivel_de_detalle, precio, foto_del_diseño FROM tatuaje WHERE id_tat = ?");
    $stmt->bind_param("i", $id_tatuaje);
    $stmt->execute();
    $stmt->bind_result($diseño, $tamaño, $zona_del_cuerpo, $nivel_de_detalle, $precio, $foto_del_diseño);
    $stmt->fetch();
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Tatuaje - Estudio de Tatuajes Kraken</title>
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

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

<main class="container my-5">
    <h1 class="text-center">Detalles del Tatuaje</h1>
    <div class="row">
        <div class="col-md-6">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($foto_del_diseño); ?>" class="img-fluid" alt="Tatuaje">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($diseño); ?></h2>
            <p><strong>Tamaño:</strong> <?php echo htmlspecialchars($tamaño); ?></p>
            <p><strong>Zona del Cuerpo:</strong> <?php echo htmlspecialchars($zona_del_cuerpo); ?></p>
            <p><strong>Nivel de Detalle:</strong> <?php echo htmlspecialchars($nivel_de_detalle); ?></p>
            <p><strong>Precio:</strong> $<?php echo htmlspecialchars(number_format($precio, 2)); ?></p>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Estudio de Tatuajes Kraken. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
