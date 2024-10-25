<!-- materiales.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar, editar o eliminar materiales

$result = $conn->query("SELECT * FROM tipo_de_material");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materiales - Panel de Administración</title>

    <link rel="stylesheet" href="css/estilo.css">

</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
        <h2>Gestión de Materiales</h2>
</div>


    <form method="POST">
        <!-- Formulario para agregar/editar material -->
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="tipo" placeholder="Tipo" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit">Guardar Material</button>
    </form>

    <h3>Lista de Materiales</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_material']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['tipo']; ?></td>
                    <td><?php echo $row['descripcion']; ?></td>
                    <td>
                        <a href="edit_material.php?id=<?php echo $row['id_material']; ?>">Editar</a>
                        <a href="delete_material.php?id=<?php echo $row['id_material']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
