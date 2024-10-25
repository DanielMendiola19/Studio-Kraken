<!-- servicios.php -->
<?php
session_start();
include 'connection.php';

// Lógica para agregar, editar o eliminar servicios

$result = $conn->query("SELECT * FROM servicio");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
       <h2>Gestión de Servicios</h2>
 </div>


    <form method="POST">
        <!-- Formulario para agregar/editar servicio -->
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="number" name="precio" placeholder="Precio" required>
        <button type="submit">Guardar Servicio</button>
    </form>

    <h3>Lista de Servicios</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_seo']; ?></td>
                    <td><?php echo $row['descripcion']; ?></td>
                    <td><?php echo $row['precio']; ?></td>
                    <td>
                        <a href="edit_servicio.php?id=<?php echo $row['id_seo']; ?>">Editar</a>
                        <a href="delete_servicio.php?id=<?php echo $row['id_seo']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
