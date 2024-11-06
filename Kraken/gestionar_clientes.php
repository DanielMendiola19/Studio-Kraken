<?php
// Incluir la conexión a la base de datos
include 'connection.php';

// Manejo de la eliminación de cliente
if (isset($_GET['delete_id'])) {
    $id_cliente_a_eliminar = $_GET['delete_id'];

    // Primero, eliminar los pagos asociados a las citas del cliente
    $sql_eliminar_pagos = "DELETE FROM pago WHERE cia_id_cia IN (SELECT id_cia FROM cita WHERE cle_id = '$id_cliente_a_eliminar')";
    if ($conn->query($sql_eliminar_pagos) === TRUE) {

        // Luego, eliminar las citas asociadas al cliente
        $sql_eliminar_citas = "DELETE FROM cita WHERE cle_id = '$id_cliente_a_eliminar'";
        if ($conn->query($sql_eliminar_citas) === TRUE) {

            // Finalmente, eliminar el cliente
            $sql_eliminar_cliente = "DELETE FROM cliente WHERE id = '$id_cliente_a_eliminar'";
            if ($conn->query($sql_eliminar_cliente) === TRUE) {
                echo "<script>alert('Cliente eliminado correctamente.'); window.location.href='gestionar_clientes.php';</script>";
            } else {
                echo "Error al eliminar el cliente: " . $conn->error;
            }
        } else {
            echo "Error al eliminar las citas: " . $conn->error;
        }
    } else {
        echo "Error al eliminar los pagos: " . $conn->error;
    }
}


// Manejo de la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_cliente = $_POST['nombre_cliente'];
    $numero_de_celular = $_POST['numero_de_celular'];
    $carnet_de_identidad = $_POST['carnet_de_identidad'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];  // Usamos fecha de nacimiento en vez de edad
    $enfermedades = $_POST['enfermedades'];

    // Datos del historial médico
    $detalle_problemas = $_POST['detalle_problemas'];
    $detalle_alergias = $_POST['detalle_alergias'];
    $fecha_registro_medico = $_POST['fecha_registro_medico'];

    // Datos del historial de tatuajes
    $fecha_realizacion_tatuaje = $_POST['fecha_realizacion_tatuaje'];
    $descripcion_del_tatuaje = $_POST['descripcion_del_tatuaje'];

    // Calcular la edad
    $fecha_nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento)->y;

    // Insertar en la tabla cliente
    $sql_cliente = "INSERT INTO cliente (nombre, numero_de_celular, carnet_de_identidad, edad, enfermedades)
                    VALUES ('$nombre_cliente', '$numero_de_celular', '$carnet_de_identidad', '$edad', '$enfermedades')";
    if ($conn->query($sql_cliente) === TRUE) {
        $id_cliente = $conn->insert_id;

        // Insertar en la tabla historial_medico con el id del cliente como cle_id
        $sql_historial_medico = "INSERT INTO historial_medico (detalle_de_problemas_de_salud, detalle_de_alergia, fecha_de_registro, cle_id)
                                 VALUES ('$detalle_problemas', '$detalle_alergias', '$fecha_registro_medico', '$id_cliente')";
        if ($conn->query($sql_historial_medico) === TRUE) {
            $id_historial_medico = $conn->insert_id;

            // Insertar en la tabla historial_de_tatuajes con el id del cliente como id_historial
            $sql_historial_tatuajes = "INSERT INTO historial_de_tatuajes (tae_id_tat, id_historial, fecha_de_realizacion, descripcion_del_tae)
                                       VALUES ('$id_cliente', '$id_historial_medico', '$fecha_realizacion_tatuaje', '$descripcion_del_tatuaje')";
            if ($conn->query($sql_historial_tatuajes) === TRUE) {
                echo "Datos insertados correctamente.";
            } else {
                echo "Error al insertar los datos en historial_de_tatuajes: " . $conn->error;
            }
        } else {
            echo "Error al insertar los datos en historial_medico: " . $conn->error;
        }
    } else {
        echo "Error al insertar los datos en cliente: " . $conn->error;
    }
}

// Obtener la lista de clientes
$sql_clientes = "SELECT * FROM cliente"; // Asegúrate de que la tabla 'cliente' exista en tu base de datos.
$result_clientes = $conn->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        body {
            background-color: #b5dee9; /* Estilo de fondo del segundo HTML */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar contenido horizontalmente */
            min-height: 100vh; /* Asegurarse de que el cuerpo ocupe toda la altura de la ventana */
            margin: 0; /* Eliminar margen por defecto */
        }
        .form-container {
            margin: 20px; /* Espacio para contener el formulario */
            width: 80%; /* Ancho del contenedor */
            max-width: 600px; /* Máximo ancho para pantallas grandes */
            background-color: white; /* Fondo blanco para el formulario */
            padding: 20px; /* Espaciado interno */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Sombra para dar profundidad */
        }
        .btn {
            background-color: #4CAF50; /* Color de fondo del botón */
            color: white; /* Color del texto del botón */
            padding: 10px 20px; /* Espaciado interno */
            text-decoration: none; /* Sin subrayado */
            border-radius: 5px; /* Bordes redondeados */
            display: inline-block; /* Para que se comporte como un bloque */
            margin-bottom: 20px; /* Margen inferior para separación */
        }
        .btn:hover {
            background-color: #45a049; /* Color de fondo al pasar el ratón por encima */
        }
        h2 {
            color: #333; /* Color de los títulos */
            text-align: center; /* Centrar título */
        }
        /* Estilos para la tabla */
        table {
            width: 100%; /* Ancho de la tabla para que llene todo el contenedor */
            border-collapse: collapse; /* Colapsar bordes de la tabla */
            margin-top: 20px; /* Espacio entre el título y la tabla */
        }
        th, td {
            border: 1px solid #ddd; /* Borde de las celdas */
            padding: 8px; /* Espaciado interno en las celdas */
            text-align: left; /* Alinear texto a la izquierda */
        }
        th {
            background-color: #f2f2f2; /* Color de fondo de los encabezados */
        }
    </style>
</head>
<body>
    <h1>Registro de Clientes y sus Historias</h1>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>

    <div class="form-container">
        <form method="post" action="gestionar_clientes.php">
            <h2>Datos del Cliente</h2>
            <label for="nombre_cliente">Nombre:</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese el nombre del cliente" required>

            <label for="numero_de_celular">Número de Celular:</label>
            <input type="number" id="numero_de_celular" name="numero_de_celular" placeholder="Ingrese el número de celular" required>

            <label for="carnet_de_identidad">Carnet de Identidad:</label>
            <input type="number" id="carnet_de_identidad" name="carnet_de_identidad" placeholder="Ingrese el CI del cliente" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="calcularEdad()">

            <label for="enfermedades">¿Tiene enfermedades?</label>
            <select id="enfermedades" name="enfermedades" required onchange="mostrarHistorialMedico()">
                <option value="No">No</option>
                <option value="Sí">Sí</option>
            </select>

            <div id="historial_medico" style="display: none;">
                <h2>Historial Médico</h2>
                <label for="detalle_problemas">Detalle de Problemas de Salud:</label>
                <textarea id="detalle_problemas" name="detalle_problemas" placeholder="Describa los problemas de salud"></textarea>

                <label for="detalle_alergias">Detalle de Alergias:</label>
                <textarea id="detalle_alergias" name="detalle_alergias" placeholder="Describa las alergias"></textarea>

                <label for="fecha_registro_medico">Fecha de Registro:</label>
                <input type="date" id="fecha_registro_medico" name="fecha_registro_medico">
            </div>

            <h2>Historial de Tatuajes</h2>
            <label for="fecha_realizacion_tatuaje">Fecha de Realización:</label>
            <input type="date" id="fecha_realizacion_tatuaje" name="fecha_realizacion_tatuaje">

            <label for="descripcion_del_tatuaje">Descripción del Tatuaje:</label>
            <textarea id="descripcion_del_tatuaje" name="descripcion_del_tatuaje" placeholder="Describa el tatuaje realizado"></textarea>

            <button type="submit">Registrar Cliente</button>
        </form>
    </div>

    <h2>Lista de Clientes Registrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Número de Celular</th>
                <th>Carnet de Identidad</th>
                <th>Edad</th>
                <th>Enfermedades</th>
                <th>Acciones</th> <!-- Nueva columna para acciones -->
            </tr>
        </thead>
        <tbody>
            <?php while ($cliente = $result_clientes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $cliente['numero_de_celular']; ?></td>
                    <td><?php echo $cliente['carnet_de_identidad']; ?></td>
                    <td><?php echo $cliente['edad']; ?></td>
                    <td><?php echo $cliente['enfermedades']; ?></td>
                    <td>
                        <a href="editar_clientes.php?id=<?php echo $cliente['id']; ?>">Editar</a> | 
                        <a href="gestionar_clientes.php?delete_id=<?php echo $cliente['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        function calcularEdad() {
            const fechaNacimiento = new Date(document.getElementById('fecha_nacimiento').value);
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
            const m = hoy.getMonth() - fechaNacimiento.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                edad--;
            }
            document.getElementById('edad').value = edad;
        }

        function mostrarHistorialMedico() {
            const enfermedades = document.getElementById('enfermedades').value;
            const historialMedico = document.getElementById('historial_medico');
            if (enfermedades === 'Sí') {
                historialMedico.style.display = 'block';
            } else {
                historialMedico.style.display = 'none';
            }
        }
    </script>
</body>
</html>
