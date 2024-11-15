<?php
session_start();
include 'connection.php';

// Lógica para agregar un tatuador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $especialidad = $_POST['especialidad'];

    // Agregar tatuador a la base de datos
    $stmt = $conn->prepare("INSERT INTO tatuador (nombre, telefono, especialidad) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $telefono, $especialidad);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Tatuador agregado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al agregar el tatuador: " . $conn->error;
    }
    $stmt->close();
}

// Lógica para eliminar un tatuador
if (isset($_GET['delete_id'])) {
    $id_tar = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM tatuador WHERE id_tar = ?");
    $stmt->bind_param("i", $id_tar);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Tatuador eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar el tatuador: " . $conn->error;
    }
    $stmt->close();
}

// Consulta para obtener la lista de tatuadores
$result = $conn->query("SELECT * FROM tatuador");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tatuadores - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>

<div class="form-container">
    <h2>Gestión de Tatuadores</h2>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>
</div>

<form method="POST">
    <!-- Formulario para agregar/editar tatuador -->
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="tel" name="telefono" placeholder="Teléfono" required pattern="[0-9]{8}" title="Solo se permiten números (8 dígitos)">
    
    <label for="especialidad">Especialidad:</label>
    <select name="especialidad" required>
        <option value="">Seleccione Especialidad</option>
        <option value="Realista">Realista</option>
        <option value="BlackWork">BlackWork</option>
        <option value="Microrealismo">Microrealismo</option>
        <option value="Gótico">Gótico</option>
    </select>

    <button type="submit">Guardar Tatuador</button>
</form>

<h3>Lista de Tatuadores</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Especialidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_tar']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['telefono']; ?></td>
                <td><?php echo $row['especialidad']; ?></td>
                <td>
                    <a href="editar_tatuadores.php?id=<?php echo $row['id_tar']; ?>">Editar</a>
                    <a href="tatuadores.php?delete_id=<?php echo $row['id_tar']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este tatuador?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
