<?php
session_start();
include 'connection.php';

// Lógica para editar un material
if (isset($_GET['id'])) {
    $id_material = $_GET['id'];

    // Obtener el material actual
    $stmt = $conn->prepare("SELECT * FROM tipo_de_material WHERE id_material = ?");
    $stmt->bind_param("i", $id_material);
    $stmt->execute();
    $result = $stmt->get_result();
    $material = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar el material
    $nombre_id = $_POST['nombre'];  // ID del tatuador seleccionado
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];

    // Consultar el nombre del tatuador usando su ID
    $tatuador_query = $conn->prepare("SELECT nombre FROM tatuador WHERE id_tar = ?");
    $tatuador_query->bind_param("i", $nombre_id);
    $tatuador_query->execute();
    $tatuador_result = $tatuador_query->get_result();

    if ($tatuador_result->num_rows > 0) {
        $tatuador_data = $tatuador_result->fetch_assoc();
        $nombre_tatuador = $tatuador_data['nombre'];

        // Actualizar la base de datos con el nuevo tatuador
        $stmt = $conn->prepare("UPDATE tipo_de_material SET nombre = ?, tipo = ?, descripcion = ? WHERE id_material = ?");
        $stmt->bind_param("sssi", $nombre_tatuador, $tipo, $descripcion, $id_material);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Material actualizado exitosamente.";
            header("Location: materiales.php"); // Redirigir a la lista de materiales
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar material: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Tatuador no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Material</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>

<div class="form-container">
    <h2>Editar Material</h2>
</div>

<form method="POST">
    <!-- Formulario para editar material -->
    
    <!-- ComboBox para seleccionar un tatuador -->
    <label for="nombre">Tatuador:</label>
    <select name="nombre" required>
        <option value="">Seleccione un Tatuador</option>
        <?php
        // Obtener la lista de tatuadores
        $tatuadores_result = $conn->query("SELECT id_tar, nombre FROM tatuador");
        while ($tatuador = $tatuadores_result->fetch_assoc()):
            // Marcar el tatuador actual como seleccionado
            $selected = ($tatuador['nombre'] == $material['nombre']) ? 'selected' : '';
        ?>
            <option value="<?php echo $tatuador['id_tar']; ?>" <?php echo $selected; ?>>
                <?php echo $tatuador['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- ComboBox para seleccionar el tipo de material -->
    <label for="tipo">Tipo de Material:</label>
    <select name="tipo" required>
        <option value="">Seleccione un Tipo</option>
        <option value="Máquina de tatuar" <?php echo ($material['tipo'] == 'Máquina de tatuar') ? 'selected' : ''; ?>>Máquina de tatuar</option>
        <option value="Agujas" <?php echo ($material['tipo'] == 'Agujas') ? 'selected' : ''; ?>>Agujas</option>
        <option value="Tinta" <?php echo ($material['tipo'] == 'Tinta') ? 'selected' : ''; ?>>Tinta</option>
        <option value="Papel hidrografico" <?php echo ($material['tipo'] == 'Papel hidrografico') ? 'selected' : ''; ?>>Papel hidrografico</option>
        <option value="Guantes" <?php echo ($material['tipo'] == 'Guantes') ? 'selected' : ''; ?>>Guantes</option>
        <option value="Papel plástico" <?php echo ($material['tipo'] == 'Papel plástico') ? 'selected' : ''; ?>>Papel plástico</option>
        <option value="Vaselina" <?php echo ($material['tipo'] == 'Vaselina') ? 'selected' : ''; ?>>Vaselina</option>
    </select>

    <!-- Campo para la descripción -->
    <input type="text" name="descripcion" value="<?php echo $material['descripcion']; ?>" placeholder="Descripción" required>

    <button type="submit">Actualizar Material</button>
</form>

<a href="materiales.php" class="btn">Volver a la lista de materiales</a>

</body>
</html>
