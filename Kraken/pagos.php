<?php
session_start();
include 'connection.php';

// Inicializar un mensaje de sesión para mostrar al usuario
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
}

// Lógica para agregar un nuevo pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cita_id = $_POST['cita_id']; // ID de cita ingresado por el usuario
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];

    // Verifica que el ID de cita ingresado exista en la tabla 'cita'
    $cita_query = $conn->prepare("SELECT id_cia FROM cita WHERE id_cia = ?");
    $cita_query->bind_param("i", $cita_id);
    $cita_query->execute();
    $cita_query->store_result();

    if ($cita_query->num_rows > 0) {
        // Inserta el pago en la base de datos
        $stmt = $conn->prepare("INSERT INTO pago (cia_id_cia, monto, metodo_de_pao) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $cita_id, $monto, $metodo_pago);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Pago agregado exitosamente.";
        } else {
            $_SESSION['mensaje'] = "Error al agregar pago: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error: El ID de cita no existe.";
    }

    $cita_query->close();
}

// Lógica para eliminar un pago
if (isset($_GET['delete_id'])) {
    $id_pago = $_GET['delete_id']; // Cambié esta variable para usar el nombre correcto

    // Preparar la consulta para eliminar el pago
    $stmt = $conn->prepare("DELETE FROM pago WHERE id_pao = ?"); // Corregí el nombre de la tabla
    $stmt->bind_param("i", $id_pago);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Pago eliminado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar pago: " . $stmt->error;
    }
    $stmt->close();

    // Redirigir a la misma página para evitar reenvío de formularios
    header("Location: pagos.php");
    exit();
}

// Lógica para obtener la lista de pagos
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
    <a href="admin_dashboard.php" class="btn">Volver al Panel de Administración</a>

    <!-- Mostrar mensaje de sesión -->
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert">
            <?php echo $_SESSION['mensaje']; ?>
            <?php unset($_SESSION['mensaje']); // Limpiar mensaje después de mostrarlo ?>
        </div>
    <?php endif; ?>
</div>

<form method="POST">
    <!-- Formulario para agregar pago -->
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
                <td><?php echo $row['id_pao']; ?></td>
                <td><?php echo $row['cia_id_cia']; ?></td> <!-- Asegúrate de que el nombre de la columna sea correcto -->
                <td><?php echo $row['monto']; ?></td>
                <td><?php echo $row['metodo_de_pao']; ?></td>
                <td>
                    <a href="editar_pagos.php?id=<?php echo $row['id_pao']; ?>">Editar</a>
                    <a href="pagos.php?delete_id=<?php echo $row['id_pao']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este pago?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
