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

        h1 {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            margin-top: 20px;
            font-size: 1.5em;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #388E3C;
        }

        /* Contenedor del formulario */
        form {
            width: 80%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        /* Estilos para el formulario */
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
            width: 100%;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Formato para el botón de actualización */
        button[type="submit"] {
            font-size: 1.2em;
        }
    </style>
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

