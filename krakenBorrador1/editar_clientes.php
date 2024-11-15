<?php
// Incluir la conexión a la base de datos
include 'connection.php';

// Verificar si se ha pasado el ID del cliente
if (isset($_GET['id'])) {
    $id_cliente = $_GET['id'];

    // Consultar los datos del cliente
    $sql_cliente = "SELECT * FROM cliente WHERE id = '$id_cliente'";
    $result_cliente = $conn->query($sql_cliente);
    if (!$result_cliente) {
        die("Error en la consulta de cliente: " . $conn->error);
    }
    $cliente = $result_cliente->fetch_assoc();

    // Asegurarse de que el cliente existe
    if (!$cliente) {
        echo "<script>alert('Cliente no encontrado.'); window.location.href='gestionar_clientes.php';</script>";
        exit();
    }
}

// Manejo de la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre_cliente = $_POST['nombre_cliente'];
    $numero_de_celular = $_POST['numero_de_celular'];
    $carnet_de_identidad = $_POST['carnet_de_identidad'];
    $edad = $_POST['edad'];
    $enfermedades = $_POST['enfermedades'];

    // Actualizar datos del cliente
    $sql_update_cliente = "UPDATE cliente SET 
        nombre = '$nombre_cliente',
        numero_de_celular = '$numero_de_celular',
        carnet_de_identidad = '$carnet_de_identidad',
        edad = '$edad',
        enfermedades = '$enfermedades'
        WHERE id = '$id_cliente'";

    if ($conn->query($sql_update_cliente) === TRUE) {
        echo "<script>alert('Datos del cliente actualizados correctamente.'); window.location.href='gestionar_clientes.php';</script>";
    } else {
        echo "Error al actualizar el cliente: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <h1>Editar Cliente</h1>

    <form method="post" action="editar_clientes.php?id=<?php echo $id_cliente; ?>">
        <h2>Datos del Cliente</h2>
        <label for="nombre_cliente">Nombre:</label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>

        <label for="numero_de_celular">Número de Celular:</label>
        <input type="number" id="numero_de_celular" name="numero_de_celular" value="<?php echo htmlspecialchars($cliente['numero_de_celular']); ?>" required>

        <label for="carnet_de_identidad">Carnet de Identidad:</label>
        <input type="number" id="carnet_de_identidad" name="carnet_de_identidad" value="<?php echo htmlspecialchars($cliente['carnet_de_identidad']); ?>" required>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($cliente['edad']); ?>" required>

        <label for="enfermedades">¿Tiene enfermedades? (S/N):</label>
        <input type="text" id="enfermedades" name="enfermedades" value="<?php echo htmlspecialchars($cliente['enfermedades']); ?>" maxlength="1" required>

        <button type="submit">Actualizar Cliente</button>
    </form>

    <h2><a href="gestionar_clientes.php">Regresar a la lista de clientes</a></h2>
</body>
</html>
