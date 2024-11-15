<?php
// Incluir el archivo de conexión
include 'connection.php';

// Inicializar variable
$id_tat = isset($_GET['id_tat']) ? $_GET['id_tat'] : null;

if ($id_tat !== null) {
    // Aquí puedes hacer la consulta a la base de datos para obtener la imagen
    $stmt = $conn->prepare("SELECT foto_del_diseño FROM tatuaje WHERE id_tat = ?");
    $stmt->bind_param("i", $id_tat);
    $stmt->execute();
    $stmt->bind_result($foto_del_diseño);
    $stmt->fetch();

    // Verifica si se encontró la imagen
    if ($foto_del_diseño) {
        // Configura los encabezados para mostrar la imagen
        header("Content-type: image/jpeg");
        echo $foto_del_diseño; // Muestra la imagen
    } else {
        echo "No se encontró la imagen.";
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "ID de tatuaje no proporcionado.";
}

// Cerrar la conexión
$conn->close();
?>
