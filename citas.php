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

// Lógica de búsqueda de citas
$whereClauses = [];
$params = [];
$types = "";

// Si se seleccionó un estado
if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $whereClauses[] = "estado = ?";
    $params[] = $_GET['estado'];
    $types .= "s";
}

// Si se seleccionó una fecha
if (isset($_GET['fecha']) && $_GET['fecha'] != '') {
    $whereClauses[] = "fecha_y_hora LIKE ?";
    $params[] = $_GET['fecha'] . '%';
    $types .= "s";
}

// Construir la consulta con las condiciones de búsqueda
$sql = "SELECT * FROM cita";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas - Panel de Administración</title>
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
    <h2>Gestión de Citas</h2>
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

<!-- Formulario de búsqueda -->
<div class="search-container">
    <h3>Buscar Citas</h3>
    <form method="GET">
        <label for="estado">Estado:</label>
        <select name="estado">
            <option value="">Seleccione Estado</option>
            <option value="Pendiente" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
            <option value="Realizado" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'Realizado' ? 'selected' : ''; ?>>Realizado</option>
        </select>

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" value="<?php echo isset($_GET['fecha']) ? $_GET['fecha'] : ''; ?>">

        <button type="submit" class="btn-buscar">Buscar</button>
            <!-- Botón de Limpiar -->
        <button type="reset" onclick="window.location.href='citas.php';" class="btn-limpiar">Limpiar</button>

    </form>
</div>

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
