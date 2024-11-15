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
        $cliente_id = $_POST['cle_id'];
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
    <h2>Editar Cita</h2>
    <form method="POST">
        <label for="cle_id">Cliente:</label>
        <select name="cle_id" required>
            <option value="">Seleccione Cliente</option>
            <?php
            // Obtener todos los clientes para el combobox
            $result_clientes = $conn->query("SELECT id, nombre FROM cliente");
            while ($row = $result_clientes->fetch_assoc()):
                $selected = ($row['id'] == $cita['cle_id']) ? 'selected' : '';
            ?>
                <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="tar_id_tar">Tatuador:</label>
        <select name="tar_id_tar" required>
            <option value="">Seleccione Tatuador</option>
            <?php
            // Obtener todos los tatuadores para el combobox
            $result_tatuadores = $conn->query("SELECT id_tar, nombre FROM tatuador");
            while ($row = $result_tatuadores->fetch_assoc()):
                $selected = ($row['id_tar'] == $cita['tar_id_tar']) ? 'selected' : '';
            ?>
                <option value="<?php echo $row['id_tar']; ?>" <?php echo $selected; ?>>
                    <?php echo $row['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="fecha_y_hora">Fecha y Hora:</label>
        <input type="datetime-local" name="fecha_y_hora" value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_y_hora'])); ?>" required>

        <label for="estado">Estado:</label>
        <select name="estado" required>
            <option value="Pendiente" <?php echo $cita['estado'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
            <option value="Realizado" <?php echo $cita['estado'] == 'Realizado' ? 'selected' : ''; ?>>Realizado</option>
        </select>

        <button type="submit">Actualizar Cita</button>
    </form>
</body>
</html>
