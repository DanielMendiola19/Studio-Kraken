<!-- editar_material.php -->
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
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("UPDATE tipo_de_material SET nombre = ?, tipo = ?, descripcion = ? WHERE id_material = ?");
    $stmt->bind_param("sssi", $nombre, $tipo, $descripcion, $id_material);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Material actualizado exitosamente.";
        header("Location: materiales.php"); // Redirigir a la lista de materiales
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar material: " . $conn->error;
    }
    $stmt->close();
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
    <input type="text" name="nombre" value="<?php echo $material['nombre']; ?>" placeholder="Nombre" required>
    <input type="text" name="tipo" value="<?php echo $material['tipo']; ?>" placeholder="Tipo" required>
    <input type="text" name="descripcion" value="<?php echo $material['descripcion']; ?>" placeholder="Descripción" required>
    <button type="submit">Actualizar Material</button>
</form>

<a href="materiales.php" class="btn">Volver a la lista de materiales</a>
</body>
</html>
