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

<?php
include 'template/header.php';
include 'template/navbar.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <div class="image-container">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($foto_del_diseño); ?>" class="img-fluid" alt="Tatuaje" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="tattoo-details">
                <h2 class="tattoo-title"><?php echo htmlspecialchars($diseño); ?></h2>
                <div class="tattoo-info">
                    <p><strong>Tamaño:</strong> <?php echo htmlspecialchars($tamaño); ?></p>
                    <p><strong>Zona del Cuerpo:</strong> <?php echo htmlspecialchars($zona_del_cuerpo); ?></p>
                    <p><strong>Nivel de Detalle:</strong> <?php echo htmlspecialchars($nivel_de_detalle); ?></p>
                    <p><strong>Precio:</strong> $<?php echo htmlspecialchars(number_format($precio, 2)); ?></p>
                </div>
                <button class="btn btn-primary" onclick="window.history.back();">Volver a la Galería</button>
            </div>
        </div>
    </div>
</main>

<?php
include 'template/footer.php';
?>