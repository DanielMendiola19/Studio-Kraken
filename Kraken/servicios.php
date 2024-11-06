<!-- servicios.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar un nuevo servicio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $stmt = $conn->prepare("INSERT INTO servicio (descripcion, precio) VALUES (?, ?)");
    $stmt->bind_param("sd", $nombre, $precio);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Servicio agregado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al agregar servicio: " . $conn->error;
    }
    $stmt->close();
}

// Lógica para eliminar un servicio
if (isset($_GET['delete_id'])) {
    $id_servicio = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM servicio WHERE id_seo = ?");
    $stmt->bind_param("i", $id_servicio);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Servicio eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar servicio: " . $conn->error;
    }
    $stmt->close();

    // Redirigir a la misma página para evitar reenvío de formularios
    header("Location: servicios.php");
    exit();
}

// Lógica para obtener la lista de servicios
$result = $conn->query("SELECT * FROM servicio");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
    <h2>Gestión de Servicios</h2>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>
</div>

<form method="POST">
    <!-- Formulario para agregar servicio -->
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="number" name="precio" placeholder="Precio" required>
    <button type="submit">Guardar Servicio</button>
</form>

<h3>Lista de Servicios</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_seo']; ?></td>
                <td><?php echo $row['descripcion']; ?></td>
                <td><?php echo $row['precio']; ?></td>
                <td>
                    <a href="editar_servicios.php?id=<?php echo $row['id_seo']; ?>">Editar</a>
                    <a href="servicios.php?delete_id=<?php echo $row['id_seo']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este servicio?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
