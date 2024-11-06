<!-- clientes.php -->
<?php
session_start();
include 'connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lógica para agregar, editar o eliminar clientes
}

// Consulta para obtener todos los clientes
$result = $conn->query("SELECT * FROM cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Panel de Administración</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Gestión de Clientes</h2>
    </div>
    

    <form method="POST">
        <div class="form-container"> <!-- Contenedor para estilizar los campos -->
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="numero_de_celular" placeholder="Número de Celular" required>
            <input type="text" name="carnet_de_identidad" placeholder="Carnet de Identidad" required>
            <input type="number" name="edad" placeholder="Edad" required>
            <input type="text" name="enfermedades" placeholder="Enfermedades" required>
            <input type="number" name="historial_medico_id_hm" placeholder="ID Historial Médico" required>
            <input type="number" name="historial_tat_id_historial" placeholder="ID Historial de Tatuajes" required>
            <button type="submit">Guardar Cliente</button>
        </div>
    </form>

    <h3>Lista de Clientes</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Número de Celular</th>
                <th>Carnet de Identidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['numero_de_celular']; ?></td>
                    <td><?php echo $row['carnet_de_identidad']; ?></td>
                    <td>
                        <a href="edit_cliente.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="delete_cliente.php?id=<?php echo $row['id']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
