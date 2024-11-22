<?php
session_start();
include 'connection.php';

// Lógica para agregar un nuevo tatuaje
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $diseño = $_POST['diseño'];
    $tamaño = $_POST['tamaño'];
    $zona_del_cuerpo = $_POST['zona_del_cuerpo'];
    $nivel_de_detalle = $_POST['nivel_de_detalle'];
    $precio = $_POST['precio'];
    $seo_id_seo = $_POST['seo_id_seo'];
    $tar_id_tar = $_POST['tar_id_tar'];
    $fecha = date('Y-m-d H:i:s');  // Fecha actual

    // Subir la imagen (mantener lógica existente)
    if (isset($_FILES['foto_del_diseño'])) {
        if (is_uploaded_file($_FILES['foto_del_diseño']['tmp_name'])) {
            $imgData = file_get_contents($_FILES['foto_del_diseño']['tmp_name']);
            
            // Insertar en la base de datos
            $sql = "INSERT INTO tatuaje (diseño, tamaño, zona_del_cuerpo, nivel_de_detalle, precio, fecha, foto_del_diseño, seo_id_seo, tar_id_tar) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $statement = $conn->prepare($sql);
            $statement->bind_param('sssssbsii', $diseño, $tamaño, $zona_del_cuerpo, $nivel_de_detalle, $precio, $fecha, $null, $seo_id_seo, $tar_id_tar);
            
            // Enviar la imagen como BLOB
            $statement->send_long_data(6, $imgData);  // El índice 6 es el de foto_del_diseño
            
            if ($statement->execute()) {
                $_SESSION['mensaje'] = "Tatuaje agregado exitosamente.";
            } else {
                $_SESSION['mensaje'] = "Error al agregar tatuaje: " . $conn->error;
            }
            $statement->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al subir la imagen.";
        }
    }
}

// Obtener la lista de servicios
$resultServicios = $conn->query("SELECT * FROM servicio");

// Obtener la lista de tatuadores
$resultTatuadores = $conn->query("SELECT * FROM tatuador");

// Obtener la lista de tatuajes registrados
$resultTatuajes = $conn->query("SELECT t.id_tat, t.diseño, t.tamaño, t.zona_del_cuerpo, t.nivel_de_detalle, t.precio, t.fecha, s.descripcion AS servicio, ta.nombre AS tatuador
                                FROM tatuaje t
                                JOIN servicio s ON t.seo_id_seo = s.id_seo
                                JOIN tatuador ta ON t.tar_id_tar = ta.id_tar");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Galería de Tatuajes</title>
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
        }

        /* Encabezado principal */
        h1 {
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
            max-width: 1200px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .form-container h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        .form-container label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="date"],
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-container textarea {
            resize: vertical;
        }

        .form-container button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 15px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .form-container button[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Tabla de tatuajes */
        table {
            width: 100%;
            max-width: 1500px;  /* Mayor tamaño de la tabla */
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: #ecf0f1;
            font-size: 1.1em;  /* Tamaño de texto más grande para los encabezados */
        }

        td {
            font-size: 1em;
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

        /* Añadir un espacio entre los elementos de la tabla para mejorar la legibilidad */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Hacer la tabla responsiva */
        @media (max-width: 768px) {
            table {
                width: 100%;
                font-size: 0.9em;
            }

            th, td {
                padding: 10px;
            }

            td {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <h1>Gestión de Galería de Tatuajes</h1>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <h2>Agregar Tatuaje</h2>
            <label for="diseño">Diseño:</label>
            <input type="text" name="diseño" placeholder="Diseño del tatuaje" required>

            <label for="tamaño">Tamaño:</label>
            <input type="text" name="tamaño" placeholder="Tamaño" required>

            <label for="zona_del_cuerpo">Zona del Cuerpo:</label>
            <input type="text" name="zona_del_cuerpo" placeholder="Zona del cuerpo" required>

            <label for="nivel_de_detalle">Nivel de Detalle:</label>
            <input type="text" name="nivel_de_detalle" placeholder="Nivel de detalle" required>

            <label for="precio">Precio:</label>
            <input type="number" name="precio" placeholder="Precio" required>

            <label for="seo_id_seo">Servicio:</label>
            <select name="seo_id_seo" required>
                <option value="">Seleccione un servicio...</option>
                <?php while ($row = $resultServicios->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_seo']; ?>"><?php echo $row['descripcion']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="tar_id_tar">Tatuador:</label>
            <select name="tar_id_tar" required>
                <option value="">Seleccione un tatuador...</option>
                <?php while ($row = $resultTatuadores->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_tar']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="foto_del_diseño">Foto del Diseño:</label>
            <input type="file" name="foto_del_diseño" required>

            <button type="submit">Agregar Tatuaje</button>
        </form>
    </div>

    <div class="form-container">
        <h2>Lista de Tatuajes</h2>
        <table>
            <thead>
                <tr>
                    <th>Diseño</th>
                    <th>Tamaño</th>
                    <th>Zona del Cuerpo</th>
                    <th>Detalle</th>
                    <th>Precio</th>
                    <th>Servicio</th>
                    <th>Tatuador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultTatuajes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['diseño']; ?></td>
                        <td><?php echo $row['tamaño']; ?></td>
                        <td><?php echo $row['zona_del_cuerpo']; ?></td>
                        <td><?php echo $row['nivel_de_detalle']; ?></td>
                        <td><?php echo $row['precio']; ?></td>
                        <td><?php echo $row['servicio']; ?></td>
                        <td><?php echo $row['tatuador']; ?></td>
                        <td>
                            <a href="editar_tatuaje.php?id=<?php echo $row['id_tat']; ?>">Editar</a> |
                            <a href="eliminar_tatuaje.php?id=<?php echo $row['id_tat']; ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

