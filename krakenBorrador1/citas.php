<!-- citas.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar, editar o eliminar citas

$result = $conn->query("SELECT * FROM cita");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>

<div class="form-container">
       <h2>Gestión de Citas</h2>
 </div>


    <form method="POST">
        <!-- Formulario para agregar/editar cita -->
        <input type="number" name="cle_id" placeholder="ID Cliente" required>
        <input type="number" name="tar_id_tar" placeholder="ID Tatuador" required>
        <input type="datetime-local" name="fecha_y_hora" required>
        <input type="text" name="estado" placeholder="Estado" required>
        <button type="submit">Guardar Cita</button>
    </form>

    <h3>Lista de Citas</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Cliente</th>
                <th>ID Tatuador</th>
                <th>Fecha y Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_cia']; ?></td>
                    <td><?php echo $row['cle_id']; ?></td>
                    <td><?php echo $row['tar_id_tar']; ?></td>
                    <td><?php echo $row['fecha_y_hora']; ?></td>
                    <td><?php echo $row['estado']; ?></td>
                    <td>
                        <a href="edit_cita.php?id=<?php echo $row['id_cia']; ?>">Editar</a>
                        <a href="delete_cita.php?id=<?php echo $row['id_cia']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
