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

// Lógica para la búsqueda
$whereClauses = [];
if (isset($_GET['search_nombre']) && !empty($_GET['search_nombre'])) {
    $search_nombre = $_GET['search_nombre'];
    $whereClauses[] = "nombre LIKE '%" . $conn->real_escape_string($search_nombre) . "%'";
}
if (isset($_GET['search_especialidad']) && !empty($_GET['search_especialidad'])) {
    $search_especialidad = $_GET['search_especialidad'];
    $whereClauses[] = "especialidad = '" . $conn->real_escape_string($search_especialidad) . "'";
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Consulta para obtener la lista de tatuadores
$result = $conn->query("SELECT * FROM tatuador $whereSql");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tatuadores - Panel de Administración</title>
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
<body style='background-color: #b5dee9;'>

<div class="form-container">
    <h2>Gestión de Tatuadores</h2>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>
</div>

<!-- Formulario para agregar tatuador -->
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="number" name="telefono" placeholder="Teléfono" required pattern="[0-9]{8}" title="Solo se permiten números (8 dígitos)">
    
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

<!-- Formulario de búsqueda -->


<h3>Lista de Tatuadores</h3>  
<div class="search-container">
    <form method="GET" class="search-form">
        <input type="text" name="search_nombre" placeholder="Buscar por nombre" value="<?php echo isset($_GET['search_nombre']) ? htmlspecialchars($_GET['search_nombre']) : ''; ?>" />
        <select name="search_especialidad">
            <option value="">Buscar por especialidad</option>
            <option value="Realista" <?php echo (isset($_GET['search_especialidad']) && $_GET['search_especialidad'] == 'Realista') ? 'selected' : ''; ?>>Realista</option>
            <option value="BlackWork" <?php echo (isset($_GET['search_especialidad']) && $_GET['search_especialidad'] == 'BlackWork') ? 'selected' : ''; ?>>BlackWork</option>
            <option value="Microrealismo" <?php echo (isset($_GET['search_especialidad']) && $_GET['search_especialidad'] == 'Microrealismo') ? 'selected' : ''; ?>>Microrealismo</option>
            <option value="Gótico" <?php echo (isset($_GET['search_especialidad']) && $_GET['search_especialidad'] == 'Gótico') ? 'selected' : ''; ?>>Gótico</option>
        </select>
        <button type="submit" class="btn-buscar">Buscar</button>
            <!-- Botón de Limpiar -->
        <button type="reset" onclick="window.location.href='tatuadores.php';" class="btn-limpiar">Limpiar</button>
    </form>
</div>  
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
