<?php
// Incluir el archivo de conexión
include 'connection.php';

// Inicializar una variable para mostrar mensajes
$message = '';

// Obtener la cantidad de imágenes en la tabla
$sql = "SELECT COUNT(*) FROM tatuaje"; // Asegúrate de que 'tatuaje' es el nombre correcto de tu tabla
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$totalImages = $row[0];
echo 'Total de registros: '. $totalImages;

// Validar el límite de archivos permitidos en la base de datos
if ($totalImages < 15) { // Cambia el número si deseas permitir más
    // Verificar si se han enviado archivos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userImage'])) {
        // Comprobar si se subió un archivo
        if (is_uploaded_file($_FILES['userImage']['tmp_name'])) {
            // Obtener los datos de la imagen
            $imgData = file_get_contents($_FILES['userImage']['tmp_name']);
            $imgType = $_FILES['userImage']['type'];
            $imgSize = $_FILES['userImage']['size'];

            // ID del servicio relacionado (asegúrate de que este ID exista en la tabla 'servicio')
            $seo_id_seo = 1; // Cambia este valor al ID correspondiente de la tabla 'servicio'

            // ID del tatuador relacionado (asegúrate de que este ID exista en la tabla 'tatuador')
            $tatuador_id_tar = 1; // Cambia este valor al ID correspondiente de la tabla 'tatuador'

            // Preparar la consulta para insertar la imagen en la base de datos
            $sql = "INSERT INTO tatuaje (foto_del_diseño, seo_id_seo, tar_id_tar) VALUES (?, ?, ?)";
            $statement = $conn->prepare($sql);
            $statement->bind_param('bii', $imgData, $seo_id_seo, $tatuador_id_tar); // 'b' para BLOB, 'i' para INT
            
            // Ejecutar la consulta
            if ($statement->execute()) {
                $message = "Imagen subida exitosamente.";
            } else {
                $message = "Error al subir la imagen: " . $statement->error;
            }

            // Cerrar la declaración
            $statement->close();
        } else {
            $message = "Lo siento, hubo un error al subir tu archivo.";
        }
    }
} else {
    echo "<div style='text-align: center; padding: 30px 0 10px 0; font-size: 20px; color: #c0392b'>
    Se ha alcanzado el límite de archivos permitidos. No se permiten más subidas.</div>";
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen BLOB</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Subir Imagen</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <!-- Formulario para subir la imagen -->
        <form name="frmImage" enctype="multipart/form-data" action="" method="post">
            <div class="form-group">
                <label>Selecciona una imagen porfa:</label>
                <input name="userImage" type="file" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Subir Imagen</button>
        </form>
        
        <!-- Aquí podrías incluir una galería de imágenes si es necesario -->
        <div class="image-gallery">
            <?php // Aquí puedes incluir código para mostrar imágenes ya subidas ?>
        </div>
    </div>
</body>
</html>
