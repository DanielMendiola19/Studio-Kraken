<!-- tatuadores.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar, editar o eliminar tatuadores

$result = $conn->query("SELECT * FROM tatuador");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tatuadores - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
        <h2>Gestión de Tatuadores</h2>
</div>

    <form method="POST">
        <!-- Formulario para agregar/editar tatuador -->
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="telefono" placeholder="Teléfono" required>
        <input type="text" name="especialidad" placeholder="Especialidad" required>
        <button type="submit">Guardar Tatuador</button>
    </form>

    <h3>Lista de Tatuadores</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_tar']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td><?php echo $row['especialidad']; ?></td>
                    <td>
                        <a href="edit_tatuador.php?id=<?php echo $row['id_tar']; ?>">Editar</a>
                        <a href="delete_tatuador.php?id=<?php echo $row['id_tar']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
