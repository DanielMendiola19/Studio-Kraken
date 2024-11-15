<!-- editar_servicios.php -->
<?php
session_start();
include 'connection.php';

// Comprobar si se ha enviado un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_servicio = $_GET['id'];

    // Obtener los datos del servicio a editar
    $stmt = $conn->prepare("SELECT * FROM servicio WHERE id_seo = ?");
    $stmt->bind_param("i", $id_servicio);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $servicio = $result->fetch_assoc();
    } else {
        $_SESSION['mensaje'] = "Servicio no encontrado.";
        header("Location: servicios.php");
        exit();
    }
} else {
    $_SESSION['mensaje'] = "ID de servicio no válido.";
    header("Location: servicios.php");
    exit();
}

// Lógica para actualizar el servicio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $stmt = $conn->prepare("UPDATE servicio SET descripcion = ?, precio = ? WHERE id_seo = ?");
    $stmt->bind_param("sdi", $nombre, $precio, $id_servicio);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Servicio actualizado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar servicio: " . $conn->error;
    }
    $stmt->close();

    // Redirigir a la página de servicios
    header("Location: servicios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio - Panel de Administración</title>
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
<body style='background-color: #b5dee9;'>
<div class="form-container">
    <h2>Editar Servicio</h2>
    <a href="servicios.php" class="btn">Volver a la lista de servicios</a>
</div>

<form method="POST">
    <!-- Formulario para editar servicio -->
    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo $servicio['descripcion']; ?>" required>
    <input type="number" name="precio" placeholder="Precio" value="<?php echo $servicio['precio']; ?>" required>
    <button type="submit">Actualizar Servicio</button>
</form>
</body>
</html>
