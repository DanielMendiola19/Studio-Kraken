<?php
session_start();
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consultar los datos de la cita actual
    $query = $conn->prepare("SELECT * FROM cita WHERE id_cia = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $cita = $result->fetch_assoc();
    
    if (!$cita) {
        die("Cita no encontrada");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cliente_id = $_POST['cliente_id'];
        $tar_id_tar = $_POST['tar_id_tar'];
        $fecha_y_hora = $_POST['fecha_y_hora'];
        $estado = $_POST['estado'];

        // Validar que el ID del cliente exista en la tabla cliente
        $cliente_check = $conn->prepare("SELECT id FROM cliente WHERE id = ?");
        $cliente_check->bind_param("i", $cliente_id);
        $cliente_check->execute();
        $cliente_check->store_result();
        if ($cliente_check->num_rows == 0) {
            die("Error: El cliente con ID $cliente_id no existe.");
        }

        // Validar que tar_id_tar exista en la tabla tatuador
        $tatuador_check = $conn->prepare("SELECT id_tar FROM tatuador WHERE id_tar = ?");
        $tatuador_check->bind_param("i", $tar_id_tar);
        $tatuador_check->execute();
        $tatuador_check->store_result();
        if ($tatuador_check->num_rows == 0) {
            die("Error: El tatuador con ID $tar_id_tar no existe.");
        }

        // Actualizar la cita en la base de datos
        $update = $conn->prepare("UPDATE cita SET cle_id = ?, tar_id_tar = ?, fecha_y_hora = ?, estado = ? WHERE id_cia = ?");
        $update->bind_param("iissi", $cliente_id, $tar_id_tar, $fecha_y_hora, $estado, $id);

        if ($update->execute()) {
            echo "Cita actualizada correctamente.";
            header("Location: citas.php");
            exit();
        } else {
            echo "Error al actualizar la cita: " . $conn->error;
        }
    }
} else {
    die("ID de cita no especificado");
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>   
    <h2>Editar Cita</h2>
    <form method="POST">
        <!-- Cambié el nombre del campo a cliente_id -->
        <input type="number" name="cliente_id" value="<?php echo $cita['cle_id']; ?>" required>
        <input type="number" name="tar_id_tar" value="<?php echo $cita['tar_id_tar']; ?>" required>
        <input type="datetime-local" name="fecha_y_hora" value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_y_hora'])); ?>" required>
        <input type="text" name="estado" value="<?php echo $cita['estado']; ?>" required>
        <button type="submit">Actualizar Cita</button>
    </form>
</body>
</html>
