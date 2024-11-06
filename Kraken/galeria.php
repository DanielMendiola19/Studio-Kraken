<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - Estudio de Tatuajes Kraken</title>
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
        <li><a href="tatuadores_cliente.php">Tatuadores</a></li>
        <li><a href="servicios_cliente.php">Servicios</a></li>
        <li><a href="contacto_cliente.php">Contacto</a></li>
        <li><a href="login.php">Acceso Admin</a></li>
    </ul>
</nav>

<main>
    <div id="carouselExampleIndicators" class="carousel slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
        <div class="carousel-inner">

        <?php
        // Incluir el archivo de conexión Singleton
        include 'connection_singleton.php';

        // Obtener la instancia única de la conexión
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Consulta para obtener todas las imágenes de tatuajes junto con sus IDs
        $stmt = $conn->prepare("SELECT id_tat, foto_del_diseño FROM tatuaje");
        $stmt->execute();
        $stmt->bind_result($id_tat, $foto_del_diseño);

        $first = true; // Para manejar la clase 'active' en el primer elemento
        while ($stmt->fetch()) {
            // Verifica si se encontró la imagen
            if ($foto_del_diseño) {
                // Si es la primera imagen, añadir la clase 'active'
                $active_class = $first ? 'active' : '';
                echo "<div class='carousel-item $active_class'>";
                // Crear enlace hacia la página de detalles del tatuaje
                echo "<a href='tatuaje.php?id=$id_tat'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($foto_del_diseño) . "' class='d-block custom-img' alt='Tatuaje'>";
                echo "</a>";
                echo "</div>";
                $first = false; // Cambiar a false después de la primera iteración
            }
        }

        // Cerrar la declaración
        $stmt->close();
        ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</main>

<footer>
    <p>&copy; 2024 Estudio de Tatuajes Kraken. Todos los derechos reservados.</p>
</footer>

</body>
</html>
