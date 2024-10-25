<?php
// Incluir la conexión a la base de datos
include 'connection.php';

// Manejo de la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_cliente = $_POST['nombre_cliente'];
    $numero_de_celular = $_POST['numero_de_celular'];
    $carnet_de_identidad = $_POST['carnet_de_identidad'];
    $edad = $_POST['edad'];
    $enfermedades = $_POST['enfermedades'];

    // Datos del historial médico
    $detalle_problemas = $_POST['detalle_problemas'];
    $detalle_alergias = $_POST['detalle_alergias'];
    $fecha_registro_medico = $_POST['fecha_registro_medico'];

    // Datos del historial de tatuajes
    $fecha_realizacion_tatuaje = $_POST['fecha_realizacion_tatuaje'];
    $descripcion_del_tatuaje = $_POST['descripcion_del_tatuaje'];

    // Insertar en la tabla historial_medico
    $sql_historial_medico = "INSERT INTO historial_medico (detalle_de_problemas_de_salud, detalle_de_alergia, fecha_de_registro)
                             VALUES ('$detalle_problemas', '$detalle_alergias', '$fecha_registro_medico')";
    if ($conn->query($sql_historial_medico) === TRUE) {
        $id_historial_medico = $conn->insert_id;

        // Insertar en la tabla historial_de_tatuajes
        $sql_historial_tatuajes = "INSERT INTO historial_de_tatuajes (fecha_de_realizacion, descripcion_del_tae)
                                   VALUES ('$fecha_realizacion_tatuaje', '$descripcion_del_tatuaje')";
        if ($conn->query($sql_historial_tatuajes) === TRUE) {
            $id_historial_tatuajes = $conn->insert_id;

            // Insertar en la tabla cliente con las referencias a los historiales
            $sql_cliente = "INSERT INTO cliente (nombre, numero_de_celular, carnet_de_identidad, edad, enfermedades, historial_medico_id_hm, historial_tat_id_historial)
                            VALUES ('$nombre_cliente', '$numero_de_celular', '$carnet_de_identidad', '$edad', '$enfermedades', '$id_historial_medico', '$id_historial_tatuajes')";
            if ($conn->query($sql_cliente) === TRUE) {
                $message = "Cliente y sus historiales registrados exitosamente.";
            } else {
                $message = "Error al registrar el cliente: " . $conn->error;
            }
        } else {
            $message = "Error al registrar el historial de tatuajes: " . $conn->error;
        }
    } else {
        $message = "Error al registrar el historial médico: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <h1>Registro de Clientes y sus Historias</h1>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="gestionar_clientes.php">
        <h2>Datos del Cliente</h2>
        <label for="nombre_cliente">Nombre:</label>
        <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Ingrese el nombre del cliente" required>

        <label for="numero_de_celular">Número de Celular:</label>
        <input type="number" id="numero_de_celular" name="numero_de_celular" placeholder="Ingrese el número de celular" required>

        <label for="carnet_de_identidad">Carnet de Identidad:</label>
        <input type="number" id="carnet_de_identidad" name="carnet_de_identidad" placeholder="Ingrese el CI del cliente" required>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" placeholder="Ingrese la edad" required>

        <label for="enfermedades">¿Tiene enfermedades? (S/N):</label>
        <input type="text" id="enfermedades" name="enfermedades" maxlength="1" placeholder="S o N" required>

        <h2>Historial Médico</h2>
        <label for="detalle_problemas">Detalle de Problemas de Salud:</label>
        <textarea id="detalle_problemas" name="detalle_problemas" placeholder="Describa los problemas de salud" required></textarea>

        <label for="detalle_alergias">Detalle de Alergias:</label>
        <textarea id="detalle_alergias" name="detalle_alergias" placeholder="Describa las alergias" required></textarea>

        <label for="fecha_registro_medico">Fecha de Registro:</label>
        <input type="date" id="fecha_registro_medico" name="fecha_registro_medico" required>

        <h2>Historial de Tatuajes</h2>
        <label for="fecha_realizacion_tatuaje">Fecha de Realización:</label>
        <input type="date" id="fecha_realizacion_tatuaje" name="fecha_realizacion_tatuaje" required>

        <label for="descripcion_del_tatuaje">Descripción del Tatuaje:</label>
        <textarea id="descripcion_del_tatuaje" name="descripcion_del_tatuaje" placeholder="Describa el tatuaje realizado"></textarea>

        <button type="submit">Registrar Cliente</button>
    </form>
</body>
</html>
