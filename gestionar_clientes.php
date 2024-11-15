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
            max-width: 600px;
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

        /* Historial Médico */
        #historial_medico {
            display: none;
            margin-top: 15px;
        }

        /* Campo de búsqueda */
        #buscar_cliente {
            margin-bottom: 15px;
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        /* Tabla de clientes */
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
<body>
    <h1>Registro de Clientes y sus Historias</h1>
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>

    <div class="form-container">
        <form method="post" action="gestionar_clientes.php" onsubmit="return validarFormulario()">
            <h2>Datos del Cliente</h2>
            <label for="nombre_cliente">Nombre:</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese el nombre del cliente" required pattern="[A-Za-z\s]+" title="Solo letras y espacios">

            <label for="numero_de_celular">Número de Celular:</label>
            <input type="number" id="numero_de_celular" name="numero_de_celular" placeholder="Ingrese el número de celular" required pattern="\d{8,10}" title="Debe tener entre 8 y 10 dígitos">

            <label for="carnet_de_identidad">Carnet de Identidad:</label>
            <input type="number" id="carnet_de_identidad" name="carnet_de_identidad" placeholder="Ingrese el CI del cliente" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="calcularEdad()">

            <label for="enfermedades">¿Tiene enfermedades?</label>
            <select id="enfermedades" name="enfermedades" required onchange="mostrarHistorialMedico()">
                <option value="No">No</option>
                <option value="Sí">Sí</option>
            </select>

            <div id="historial_medico">
                <h2>Historial Médico</h2>
                <label for="detalle_problemas">Detalle de Problemas de Salud:</label>
                <textarea id="detalle_problemas" name="detalle_problemas" placeholder="Describa los problemas de salud" minlength="5"></textarea>

                <label for="detalle_alergias">Detalle de Alergias:</label>
                <textarea id="detalle_alergias" name="detalle_alergias" placeholder="Describa las alergias" minlength="5"></textarea>

                <label for="fecha_registro_medico">Fecha de Registro:</label>
                <input type="date" id="fecha_registro_medico" name="fecha_registro_medico">
            </div>

            <h2>Historial de Tatuajes</h2>
            <label for="fecha_realizacion_tatuaje">Fecha de Realización:</label>
            <input required type="date" id="fecha_realizacion_tatuaje" name="fecha_realizacion_tatuaje">

            <label for="descripcion_del_tatuaje">Descripción del Tatuaje:</label>
            <textarea id="descripcion_del_tatuaje" name="descripcion_del_tatuaje" placeholder="Describa el tatuaje realizado" minlength="5"></textarea>

            <button type="submit">Registrar Cliente</button>
        </form>
    </div>

    <h2>Lista de Clientes Registrados</h2>
    <input type="text" id="buscar_cliente" placeholder="Buscar cliente..." onkeyup="buscarCliente()">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Número de Celular</th>
                <th>Carnet de Identidad</th>
                <th>Edad</th>
                <th>Enfermedades</th>
                <th>Acciones</th>
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

        function validarFormulario() {
            const nombre = document.getElementById('nombre_cliente').value;
            if (!/^[A-Za-z\s]+$/.test(nombre)) {
                alert('El nombre solo debe contener letras y espacios.');
                return false;
            }
            const celular = document.getElementById('numero_de_celular').value;
            if (!/^\d{8,10}$/.test(celular)) {
                alert('El número de celular debe tener entre 8 y 10 dígitos.');
                return false;
            }
            return true;
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
        function buscarCliente() {
        // Obtener el valor del campo de búsqueda y convertirlo a minúsculas
        const input = document.getElementById('buscar_cliente').value.toLowerCase();
        const filas = document.querySelectorAll('table tbody tr');

        // Iterar sobre cada fila de la tabla
        filas.forEach((fila) => {
            // Obtener todas las celdas de la fila y convertir el texto de la fila a minúsculas
            const textoFila = fila.innerText.toLowerCase();

            // Verificar si el texto de búsqueda está presente en el texto de la fila
            if (textoFila.includes(input)) {
                // Si coincide, mostramos la fila
                fila.style.display = '';
            } else {
                // Si no coincide, ocultamos la fila
                fila.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
