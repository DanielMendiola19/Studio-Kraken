<?php
session_start();
include 'connection.php';

// Lógica para agregar un nuevo material
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_id = $_POST['nombre']; // ID del tatuador seleccionado
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];

    // Consultar el nombre del tatuador utilizando su ID
    $tatuador_query = $conn->prepare("SELECT nombre FROM tatuador WHERE id_tar = ?");
    $tatuador_query->bind_param("i", $nombre_id);
    $tatuador_query->execute();
    $tatuador_result = $tatuador_query->get_result();

    if ($tatuador_result->num_rows > 0) {
        // Obtener el nombre del tatuador
        $tatuador_data = $tatuador_result->fetch_assoc();
        $nombre_tatuador = $tatuador_data['nombre'];

        // Insertar el material en la base de datos, guardando el nombre del tatuador
        $stmt = $conn->prepare("INSERT INTO tipo_de_material (nombre, tipo, descripcion) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_tatuador, $tipo, $descripcion);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Material agregado exitosamente.";
        } else {
            $_SESSION['mensaje'] = "Error al agregar material: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Tatuador no encontrado.";
    }
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

// Lógica para obtener los nombres de los tatuadores
$tatuadores_result = $conn->query("SELECT id_tar, nombre FROM tatuador");
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
    
    <!-- ComboBox para seleccionar un tatuador -->
    <label for="nombre">Tatuador:</label>
    <select name="nombre" required>
        <option value="">Seleccione un Tatuador</option>
        <?php while ($tatuador = $tatuadores_result->fetch_assoc()): ?>
            <option value="<?php echo $tatuador['id_tar']; ?>"><?php echo $tatuador['nombre']; ?></option>
        <?php endwhile; ?>
    </select>

    <!-- ComboBox para seleccionar el tipo de material -->
    <label for="tipo">Tipo de Material:</label>
    <select name="tipo" required>
        <option value="">Seleccione un Tipo</option>
        <option value="Máquina de tatuar">Máquina de tatuar</option>
        <option value="Agujas">Agujas</option>
        <option value="Tinta">Tinta</option>
        <option value="Papel hidrografico">Papel hidrografico</option>
        <option value="Guantes">Guantes</option>
        <option value="Papel plástico">Papel plástico</option>
        <option value="Vaselina">Vaselina</option>
    </select>

    <!-- Campo para la descripción -->
    <input type="text" name="descripcion" placeholder="Descripción" required>

    <button type="submit">Guardar Material</button>
</form>

<h3>Lista de Materiales</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tatuador</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id_material']; ?></td>
                <td><?php echo $row['nombre']; ?></td>  <!-- El nombre ya está almacenado en la columna 'nombre' -->
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
