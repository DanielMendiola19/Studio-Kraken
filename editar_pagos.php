<?php
session_start();
include 'connection.php';

// Inicializar un mensaje de sesión para mostrar al usuario
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
}

// Lógica para editar un pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pago = $_POST['id_pago'];  // ID del pago que se va a editar
    $cita_id = $_POST['cita_id'];  // ID de la cita
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];
    $servicio_id = $_POST['servicio'];  // ID del servicio seleccionado

    // Verifica que el ID de cita ingresado exista en la tabla 'cita'
    $cita_query = $conn->prepare("SELECT id_cia FROM cita WHERE id_cia = ?");
    $cita_query->bind_param("i", $cita_id);
    $cita_query->execute();
    $cita_query->store_result();

    if ($cita_query->num_rows > 0) {
        // Actualiza el pago en la base de datos
        $stmt = $conn->prepare("UPDATE pago SET cia_id_cia = ?, monto = ?, metodo_de_pao = ?, servicio_id = ?, fecha = NOW() WHERE id_pao = ?");
        $stmt->bind_param("idsii", $cita_id, $monto, $metodo_pago, $servicio_id, $id_pago);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Pago actualizado exitosamente.";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar pago: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error: El ID de cita no existe.";
    }

    $cita_query->close();

    // Redirigir a la lista de pagos después de la actualización
    header("Location: pagos.php");
    exit();
}

// Lógica para obtener los datos del pago a editar
if (isset($_GET['id'])) {
    $id_pago = $_GET['id'];

    // Obtener los datos del pago
    $stmt = $conn->prepare("SELECT * FROM pago WHERE id_pao = ?");
    $stmt->bind_param("i", $id_pago);
    $stmt->execute();
    $result = $stmt->get_result();
    $pago = $result->fetch_assoc();

    if (!$pago) {
        $_SESSION['mensaje'] = "Error: Pago no encontrado.";
        header("Location: pagos.php");
        exit();
    }
    $stmt->close();
} else {
    // Redirigir si no se proporciona un ID
    header("Location: pagos.php");
    exit();
}

// Lógica para obtener los clientes con citas y servicios
$clientes_query = $conn->query("SELECT c.id_cia, cl.nombre AS cliente_nombre, s.id_seo, s.descripcion AS servicio_nombre, s.precio 
                                FROM cita c
                                JOIN cliente cl ON c.cle_id = cl.id
                                JOIN servicio s ON c.tar_id_tar = s.id_seo");

// Lógica para obtener todos los servicios disponibles
$servicios_query = $conn->query("SELECT id_seo, descripcion AS servicio_nombre, precio FROM servicio");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pago - Panel de Administración</title>
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
    <h2>Editar Pago</h2>
    <a href="pagos.php" class="btn">Volver a la Lista de Pagos</a>

    <!-- Mostrar mensaje de sesión -->
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert">
            <?php echo $_SESSION['mensaje']; ?>
            <?php unset($_SESSION['mensaje']); // Limpiar mensaje después de mostrarlo ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para editar pago -->
    <form method="POST">
        <input type="hidden" name="id_pago" value="<?php echo $pago['id_pao']; ?>">

        <!-- ComboBox para seleccionar el cliente -->
        <label for="cita_id">Cliente:</label>
        <select name="cita_id" id="cita_id" required>
            <option value="">Seleccione un Cliente</option>
            <?php while ($cliente = $clientes_query->fetch_assoc()): ?>
                <option value="<?php echo $cliente['id_cia']; ?>" <?php echo ($cliente['id_cia'] == $pago['cia_id_cia']) ? 'selected' : ''; ?>>
                    <?php echo $cliente['cliente_nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- ComboBox para seleccionar el servicio -->
        <label for="servicio">Servicio:</label>
        <select name="servicio" id="servicio" required onchange="updateMonto();">
            <option value="">Seleccione un Servicio</option>
            <?php while ($servicio = $servicios_query->fetch_assoc()): ?>
                <option value="<?php echo $servicio['id_seo']; ?>" data-precio="<?php echo $servicio['precio']; ?>" 
                    <?php echo ($servicio['id_seo'] == $pago['servicio_id']) ? 'selected' : ''; ?>>
                    <?php echo $servicio['servicio_nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- Campo para monto (será actualizado automáticamente) -->
        <input type="number" name="monto" id="monto" value="<?php echo $pago['monto']; ?>" placeholder="Monto" required readonly>

        <!-- ComboBox para seleccionar el método de pago -->
        <label for="metodo_pago">Método de Pago:</label>
        <select name="metodo_pago" id="metodo_pago" required>
            <option value="QR" <?php echo ($pago['metodo_de_pao'] == 'QR') ? 'selected' : ''; ?>>QR</option>
            <option value="Efectivo" <?php echo ($pago['metodo_de_pao'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
            <option value="Tarjeta" <?php echo ($pago['metodo_de_pao'] == 'Tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
        </select>

        <button type="submit">Actualizar Pago</button>
    </form>
</div>

<script>
    // Función para actualizar el monto según el servicio seleccionado
    function updateMonto() {
        const selectServicio = document.getElementById('servicio');
        const selectedOption = selectServicio.options[selectServicio.selectedIndex];
        const monto = selectedOption.getAttribute('data-precio');
        document.getElementById('monto').value = monto;
    }
</script>

</body>
</html>
