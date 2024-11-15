<?php
session_start();
include 'connection.php';

// Lógica para editar un tatuador
if (isset($_GET['id'])) {
    $id_tar = $_GET['id'];

    // Consultar el tatuador específico
    $stmt = $conn->prepare("SELECT * FROM tatuador WHERE id_tar = ?");
    $stmt->bind_param("i", $id_tar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tatuador = $result->fetch_assoc();
    } else {
        $_SESSION['mensaje'] = "Tatuador no encontrado.";
        header("Location: tatuadores.php");
        exit;
    }

    // Actualizar los datos si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $especialidad = $_POST['especialidad'];

        $update_stmt = $conn->prepare("UPDATE tatuador SET nombre = ?, telefono = ?, especialidad = ? WHERE id_tar = ?");
        $update_stmt->bind_param("sssi", $nombre, $telefono, $especialidad, $id_tar);
        
        if ($update_stmt->execute()) {
            $_SESSION['mensaje'] = "Tatuador actualizado exitosamente.";
            header("Location: tatuadores.php");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar el tatuador: " . $conn->error;
        }
        $update_stmt->close();
    }

    $stmt->close();
} else {
    $_SESSION['mensaje'] = "ID de tatuador no especificado.";
    header("Location: tatuadores.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tatuador</title>
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
    <h2>Editar Tatuador</h2>
</div>

<form method="POST">
    <!-- Campo para el nombre -->
    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($tatuador['nombre']); ?>" required>
    
    <!-- Campo para el teléfono -->
    <input type="tel" name="telefono" placeholder="Teléfono" value="<?php echo htmlspecialchars($tatuador['telefono']); ?>" required pattern="[0-9]{8}" title="Solo se permiten números (8 dígitos)">
    
    <!-- Campo para la especialidad (select con opciones preseleccionadas) -->
    <label for="especialidad">Especialidad:</label>
    <select name="especialidad" required>
        <option value="Realista" <?php if ($tatuador['especialidad'] == 'Realista') echo 'selected'; ?>>Realista</option>
        <option value="BlackWork" <?php if ($tatuador['especialidad'] == 'BlackWork') echo 'selected'; ?>>BlackWork</option>
        <option value="Microrealismo" <?php if ($tatuador['especialidad'] == 'Microrealismo') echo 'selected'; ?>>Microrealismo</option>
        <option value="Gótico" <?php if ($tatuador['especialidad'] == 'Gótico') echo 'selected'; ?>>Gótico</option>
    </select>

    <button type="submit">Actualizar Tatuador</button>
</form>

<a href="tatuadores.php" class="btn">Volver a la lista de Tatuadores</a>

</body>
</html>
