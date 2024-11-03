<?php
session_start();
include 'connection.php';

// Inicializar un mensaje de sesión para mostrar al usuario
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
}

// Lógica para editar un pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pago = $_POST['id_pago'];
    $cita_id = $_POST['cita_id'];
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];

    // Actualiza el pago en la base de datos
    $stmt = $conn->prepare("UPDATE pago SET cia_id_cia = ?, monto = ?, metodo_de_pao = ? WHERE id_pao = ?");
    $stmt->bind_param("idsi", $cita_id, $monto, $metodo_pago, $id_pago);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Pago actualizado exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar pago: " . $stmt->error;
    }
    $stmt->close();

    // Redirigir a la página de pagos después de la actualización
    header("Location: pagos.php");
    exit();
}

// Lógica para obtener los datos del pago a editar
if (isset($_GET['id'])) {
    $id_pago = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM pago WHERE id_pao = ?");
    $stmt->bind_param("i", $id_pago);
    $stmt->execute();
    $result = $stmt->get_result();
    $pago = $result->fetch_assoc();

    if (!$pago) {
        $_SESSION['mensaje'] = "Error: Pago no encontrado.";
        header("Location: pagos.php");
        exit();
    }
    $stmt->close();
} else {
    // Redirigir si no se proporciona un ID
    header("Location: pagos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pago - Panel de Administración</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body style='background-color: #b5dee9;'>
<div class="form-container">
    <h2>Editar Pago</h2>
    <a href="pagos.php" class="btn">Volver a la Lista de Pagos</a>

    <!-- Mostrar mensaje de sesión -->
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert">
            <?php echo $_SESSION['mensaje']; ?>
            <?php unset($_SESSION['mensaje']); // Limpiar mensaje después de mostrarlo ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para editar pago -->
    <form method="POST">
        <input type="hidden" name="id_pago" value="<?php echo $pago['id_pao']; ?>">
        <input type="number" name="cita_id" value="<?php echo $pago['cia_id_cia']; ?>" placeholder="ID Cita" required>
        <input type="number" name="monto" value="<?php echo $pago['monto']; ?>" placeholder="Monto" required>
        <input type="text" name="metodo_pago" value="<?php echo $pago['metodo_de_pao']; ?>" placeholder="Método de Pago" required>
        <button type="submit">Actualizar Pago</button>
    </form>
</div>
</body>
</html>
