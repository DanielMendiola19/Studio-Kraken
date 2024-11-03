<!-- materiales.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar un nuevo material
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("INSERT INTO tipo_de_material (nombre, tipo, descripcion) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $tipo, $descripcion);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Material agregado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al agregar material: " . $conn->error;
    }
    $stmt->close();
}

// Lógica para eliminar un material
if (isset($_GET['delete_id'])) {
    $id_material = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM tipo_de_material WHERE id_material = ?");
    $stmt->bind_param("i", $id_material);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Material eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar material: " . $conn->error;
    }
    $stmt->close();

    // Redirigir a la misma página para evitar reenvío de formularios
    header("Location: materiales.php");
    exit();
}

// Lógica para obtener la lista de materiales
$result = $conn->query("SELECT * FROM tipo_de_material");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materiales - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
    <h2>Gestión de Materiales</h2>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>
</div>

<form method="POST">
    <!-- Formulario para agregar material -->
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="tipo" placeholder="Tipo" required>
    <input type="text" name="descripcion" placeholder="Descripción" required>
    <button type="submit">Guardar Material</button>
</form>

<h3>Lista de Materiales</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_material']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['tipo']; ?></td>
                <td><?php echo $row['descripcion']; ?></td>
                <td>
                    <a href="editar_material.php?id=<?php echo $row['id_material']; ?>">Editar</a>
                    <a href="materiales.php?delete_id=<?php echo $row['id_material']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este material?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
