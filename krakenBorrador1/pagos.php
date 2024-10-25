<!-- pagos.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar, editar o eliminar pagos

$result = $conn->query("SELECT * FROM pago");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
       <h2>Gestión de Pagos</h2>
 </div>


    <form method="POST">
        <!-- Formulario para agregar/editar pago -->
        <input type="number" name="cita_id" placeholder="ID Cita" required>
        <input type="number" name="monto" placeholder="Monto" required>
        <input type="text" name="metodo_pago" placeholder="Método de Pago" required>
        <button type="submit">Guardar Pago</button>
    </form>

    <h3>Lista de Pagos</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Cita</th>
                <th>Monto</th>
                <th>Método de Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_pago']; ?></td>
                    <td><?php echo $row['cita_id']; ?></td>
                    <td><?php echo $row['monto']; ?></td>
                    <td><?php echo $row['metodo_pago']; ?></td>
                    <td>
                        <a href="edit_pago.php?id=<?php echo $row['id_pago']; ?>">Editar</a>
                        <a href="delete_pago.php?id=<?php echo $row['id_pago']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
