<?php
session_start();
include 'connection.php';

// Lógica para agregar una cita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cle_id = $_POST['cle_id'];  // ID del cliente
    $tar_id_tar = $_POST['tar_id_tar'];  // ID del tatuador
    $fecha_y_hora = $_POST['fecha_y_hora'];
    $estado = $_POST['estado'];

    // Verificar si es una actualización de cita existente
    if (isset($_POST['id_cia']) && !empty($_POST['id_cia'])) {
        // Actualizar la cita
        $id_cia = $_POST['id_cia'];
        $sql_update = "UPDATE cita SET cle_id = ?, tar_id_tar = ?, fecha_y_hora = ?, estado = ? WHERE id_cia = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("iissi", $cle_id, $tar_id_tar, $fecha_y_hora, $estado, $id_cia);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insertar una nueva cita
        $sql_insert = "INSERT INTO cita (cle_id, tar_id_tar, fecha_y_hora, estado) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("iiss", $cle_id, $tar_id_tar, $fecha_y_hora, $estado);
        $stmt->execute();
        $stmt->close();
    }

    // Redirigir para evitar resubmit en F5
    header("Location: citas.php");
    exit();
}

// Lógica para eliminar una cita
if (isset($_GET['delete_id'])) {
    $id_cia = $_GET['delete_id'];
    $sql_delete = "DELETE FROM cita WHERE id_cia = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_cia);
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar resubmit en F5
    header("Location: citas.php");
    exit();
}

// Obtener todos los clientes para mostrarlos en el combobox
$sql_clientes = "SELECT id, nombre FROM cliente";
$result_clientes = $conn->query($sql_clientes);

// Obtener todos los tatuadores para mostrarlos en el combobox
$sql_tatuadores = "SELECT id_tar, nombre FROM tatuador";
$result_tatuadores = $conn->query($sql_tatuadores);

// Obtener todas las citas para mostrarlas en la tabla
$result = $conn->query("SELECT * FROM cita");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>

<div class="form-container">
    <h2>Gestión de Citas</h2>
    <!-- Botón para volver a la página de administración -->
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>
</div>

<form method="POST">
    <!-- Formulario para agregar/editar cita -->
    <input type="hidden" name="id_cia" value="<?php echo isset($_GET['edit_id']) ? $_GET['edit_id'] : ''; ?>">

    <label for="cle_id">Cliente:</label>
    <select name="cle_id" required>
        <option value="">Seleccione Cliente</option>
        <?php while ($row = $result_clientes->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>" <?php echo isset($cliente) && $cliente['cle_id'] == $row['id'] ? 'selected' : ''; ?>>
                <?php echo $row['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="tar_id_tar">Tatuador:</label>
    <select name="tar_id_tar" required>
        <option value="">Seleccione Tatuador</option>
        <?php while ($row = $result_tatuadores->fetch_assoc()): ?>
            <option value="<?php echo $row['id_tar']; ?>" <?php echo isset($tatuador) && $tatuador['tar_id_tar'] == $row['id_tar'] ? 'selected' : ''; ?>>
                <?php echo $row['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="fecha_y_hora">Fecha y Hora:</label>
    <input type="datetime-local" name="fecha_y_hora" required>

    <label for="estado">Estado:</label>
    <select name="estado" required>
        <option value="Pendiente" <?php echo isset($cita) && $cita['estado'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
        <option value="Realizado" <?php echo isset($cita) && $cita['estado'] == 'Realizado' ? 'selected' : ''; ?>>Realizado</option>
    </select>

    <button type="submit">Guardar Cita</button>
</form>

<h3>Lista de Citas</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>ID Cliente</th>
            <th>ID Tatuador</th>
            <th>Fecha y Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_cia']; ?></td>
                <td><?php echo $row['cle_id']; ?></td>
                <td><?php echo $row['tar_id_tar']; ?></td>
                <td><?php echo $row['fecha_y_hora']; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td>
                    <a href="editar_citas.php?id=<?php echo $row['id_cia']; ?>">Editar</a>
                    <a href="citas.php?delete_id=<?php echo $row['id_cia']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta cita?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
