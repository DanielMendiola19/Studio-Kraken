<!-- editar_servicios.php -->
<?php
session_start();
include 'connection.php';

// Comprobar si se ha enviado un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_servicio = $_GET['id'];

    // Obtener los datos del servicio a editar
    $stmt = $conn->prepare("SELECT * FROM servicio WHERE id_seo = ?");
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $servicio = $result->fetch_assoc();
    } else {
        $_SESSION['mensaje'] = "Servicio no encontrado.";
        header("Location: servicios.php");
        exit();
    }
} else {
    $_SESSION['mensaje'] = "ID de servicio no válido.";
    header("Location: servicios.php");
    exit();
}

// Lógica para actualizar el servicio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $stmt = $conn->prepare("UPDATE servicio SET descripcion = ?, precio = ? WHERE id_seo = ?");
    $stmt->bind_param("sdi", $nombre, $precio, $id_servicio);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Servicio actualizado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar servicio: " . $conn->error;
    }
    $stmt->close();

    // Redirigir a la página de servicios
    header("Location: servicios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
    <h2>Editar Servicio</h2>
    <a href="servicios.php" class="btn">Volver a la lista de servicios</a>
</div>

<form method="POST">
    <!-- Formulario para editar servicio -->
    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo $servicio['descripcion']; ?>" required>
    <input type="number" name="precio" placeholder="Precio" value="<?php echo $servicio['precio']; ?>" required>
    <button type="submit">Actualizar Servicio</button>
</form>
</body>
</html>
