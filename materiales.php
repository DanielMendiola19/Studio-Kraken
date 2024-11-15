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

// Lógica para filtrar los materiales por tatuador y tipo
$whereClauses = [];
$paramTypes = "";
$params = [];

if (isset($_GET['tatuador_id']) && $_GET['tatuador_id'] != "") {
    $whereClauses[] = "tatuador.id_tar = ?";
    $paramTypes .= "i";
    $params[] = $_GET['tatuador_id'];
}

if (isset($_GET['tipo_material']) && $_GET['tipo_material'] != "") {
    $whereClauses[] = "tipo_de_material.tipo = ?";
    $paramTypes .= "s";
    $params[] = $_GET['tipo_material'];
}

$where = "";
if (count($whereClauses) > 0) {
    $where = "WHERE " . implode(" AND ", $whereClauses);
}

$sql = "SELECT tipo_de_material.*, tatuador.nombre as tatuador_nombre 
        FROM tipo_de_material
        JOIN tatuador ON tipo_de_material.nombre = tatuador.nombre
        $where";
$stmt = $conn->prepare($sql);

if (count($params) > 0) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materiales - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
    /* Estilos generales */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
        color: #333;
    }

    body {
        background-color: #f0f2f5;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    /* Encabezado principal */
    h2 {
        color: #2c3e50;
        font-size: 2.2em;
        margin-top: 20px;
        margin-bottom: 10px;
        text-align: center;
    }

    /* Botón principal */
    .btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
        margin-bottom: 20px;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #45a049;
    }

    /* Contenedor del formulario */
    .form-container {
        width: 80%;
        max-width: 600px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
        text-align: center;
    }

    .form-container label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    .form-container h2 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.5em;
    }

    .form-container input[type="text"],
    .form-container input[type="number"],
    .form-container input[type="date"],
    .form-container input[type="datetime-local"],
    .form-container select,
    .form-container button[type="submit"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1em;
    }

    /* Botón de volver al panel */
    .form-container .btn {
        background-color: #4CAF50;
        color: white;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    .form-container .btn:hover {
        background-color: #45a049;
    }

    /* Botones de búsqueda y limpiar */
    .search-container {
        width: 80%;
        max-width: 600px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
        text-align: center;
    }

    /* Estilos de los botones de búsqueda y limpiar */
    .search-container button {
        padding: 8px 16px;
        font-size: 1em;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;
        margin-right: 10px;
        transition: background-color 0.3s;
    }

    .btn-buscar {
        background-color: #4CAF50; /* Verde */
        color: white;
        border: none;
    }

    .btn-buscar:hover {
        background-color: #45a049;
    }

    .btn-limpiar {
        background-color: #f44336; /* Rojo */
        color: white;
        border: none;
    }

    .btn-limpiar:hover {
        background-color: #e53935;
    }

    /* Alineación de los botones de búsqueda y limpiar */
    .search-buttons {
        display: flex;               /* Utiliza flexbox para alinear los botones */
        justify-content: center;     /* Centra los botones en el contenedor */
        gap: 20px;                   /* Espacio entre los botones */
    }

    /* Tabla de citas */
    table {
        width: 80%;
        max-width: 1000px;
        margin-top: 20px;
        border-collapse: collapse;
        background-color: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #2c3e50;
        color: #ecf0f1;
    }

    td {
        font-size: 0.9em;
        color: #333;
    }

    /* Enlaces de acciones en la tabla */
    a {
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        color: #388E3C;
    }
</style>
</head>
<body>


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

<!-- Formulario de búsqueda -->
<div class="search-container">
    <form method="GET">
        

        <select name="tipo_material">
            <option value="">Selecciona un Tipo de Material</option>
            <option value="Máquina de tatuar" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Máquina de tatuar") ? 'selected' : ''; ?>>Máquina de tatuar</option>
            <option value="Agujas" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Agujas") ? 'selected' : ''; ?>>Agujas</option>
            <option value="Tinta" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Tinta") ? 'selected' : ''; ?>>Tinta</option>
            <option value="Papel hidrografico" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Papel hidrografico") ? 'selected' : ''; ?>>Papel hidrografico</option>
            <option value="Guantes" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Guantes") ? 'selected' : ''; ?>>Guantes</option>
            <option value="Papel plástico" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Papel plástico") ? 'selected' : ''; ?>>Papel plástico</option>
            <option value="Vaselina" <?php echo (isset($_GET['tipo_material']) && $_GET['tipo_material'] == "Vaselina") ? 'selected' : ''; ?>>Vaselina</option>
        </select>

        <button type="submit" class="btn-buscar">Buscar</button>
            <!-- Botón de Limpiar -->
        <button type="reset" onclick="window.location.href='materiales.php';" class="btn-limpiar">Limpiar</button>
    </form>
</div>

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
                <td><?php echo $row['tatuador_nombre']; ?></td>
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