<?php
session_start();
include 'connection.php';

// Lógica para editar un tatuador
if (isset($_GET['id'])) {
    $id_tar = $_GET['id'];

    // Consultar el tatuador específico
    $stmt = $conn->prepare("SELECT * FROM tatuador WHERE id_tar = ?");
    $stmt->bind_param("i", $id_tar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tatuador = $result->fetch_assoc();
    } else {
        $_SESSION['mensaje'] = "Tatuador no encontrado.";
        header("Location: tatuadores.php");
        exit;
    }

    // Actualizar los datos si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $especialidad = $_POST['especialidad'];

        $update_stmt = $conn->prepare("UPDATE tatuador SET nombre = ?, telefono = ?, especialidad = ? WHERE id_tar = ?");
        $update_stmt->bind_param("sssi", $nombre, $telefono, $especialidad, $id_tar);
        
        if ($update_stmt->execute()) {
            $_SESSION['mensaje'] = "Tatuador actualizado exitosamente.";
            header("Location: tatuadores.php");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar el tatuador: " . $conn->error;
        }
        $update_stmt->close();
    }

    $stmt->close();
} else {
    $_SESSION['mensaje'] = "ID de tatuador no especificado.";
    header("Location: tatuadores.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tatuador</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>

<div class="form-container">
    <h2>Editar Tatuador</h2>
</div>

<form method="POST">
    <!-- Campo para el nombre -->
    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($tatuador['nombre']); ?>" required>
    
    <!-- Campo para el teléfono -->
    <input type="tel" name="telefono" placeholder="Teléfono" value="<?php echo htmlspecialchars($tatuador['telefono']); ?>" required pattern="[0-9]{8}" title="Solo se permiten números (8 dígitos)">
    
    <!-- Campo para la especialidad (select con opciones preseleccionadas) -->
    <label for="especialidad">Especialidad:</label>
    <select name="especialidad" required>
        <option value="Realista" <?php if ($tatuador['especialidad'] == 'Realista') echo 'selected'; ?>>Realista</option>
        <option value="BlackWork" <?php if ($tatuador['especialidad'] == 'BlackWork') echo 'selected'; ?>>BlackWork</option>
        <option value="Microrealismo" <?php if ($tatuador['especialidad'] == 'Microrealismo') echo 'selected'; ?>>Microrealismo</option>
        <option value="Gótico" <?php if ($tatuador['especialidad'] == 'Gótico') echo 'selected'; ?>>Gótico</option>
    </select>

    <button type="submit">Actualizar Tatuador</button>
</form>

<a href="tatuadores.php" class="btn">Volver a la lista de Tatuadores</a>

</body>
</html>
