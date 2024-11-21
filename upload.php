<?php
// Incluir el archivo de conexión
include 'connection.php';

// Inicializar una variable para mostrar mensajes
$message = '';

// Verificar si se han enviado archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userImage'])) {
    // Verificar si el archivo se sube correctamente
    if (is_uploaded_file($_FILES['userImage']['tmp_name'])) {
        // Cargar la imagen en la base de datos
        $imgData = file_get_contents($_FILES['userImage']['tmp_name']);
        $seo_id_seo = 1; 
        $tatuador_id_tar = 1;

        // Preparar la consulta
        $sql = "INSERT INTO tatuaje (foto_del_diseño, seo_id_seo, tar_id_tar) VALUES (?, ?, ?)";
        $statement = $conn->prepare($sql);
        $statement->bind_param('bii', $null, $seo_id_seo, $tatuador_id_tar);

        // Enviar los datos BLOB manualmente
        $statement->send_long_data(0, $imgData); // Índice 0 porque es el primer parámetro
        
        // Ejecutar la consulta
        if ($statement->execute()) {
            // Mostrar mensaje de éxito
            $message = "Imagen subida exitosamente.";
        } else {
            // Mostrar mensaje de error
            $message = "Error al subir la imagen: " . $statement->error;
        }
        $statement->close();
        // Redirigir después de procesar el formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Si no hay archivo o hubo un problema con el archivo
        $message = "Lo siento, hubo un error al subir tu archivo.";
    }
}

// Cerrar la conexión al final del archivo
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Listar Imágenes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .image-gallery img {
            width: 150px; /* Tamaño uniforme */
            height: 150px; /* Tamaño uniforme */
            object-fit: cover; /* Mantener proporción y rellenar */
            margin: 5px; /* Espaciado entre imágenes */
        }
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Subir Imagen</h2>
        
        <!-- Mostrar mensaje si hay -->
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form name="frmImage" enctype="multipart/form-data" action="" method="post">
            <div class="form-group">
                <label>Selecciona una imagen porfa:</label>
                <input name="userImage" type="file" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Subir Imagen</button>
        </form>
        
        <!-- Galería de Imágenes -->
        <div class="image-gallery">
            <?php
            // Conexión para listar imágenes
            include 'connection.php';
            $sql = "SELECT foto_del_diseño FROM tatuaje";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imgData = base64_encode($row['foto_del_diseño']);
                    echo "<img src='data:image/jpeg;base64,$imgData' alt='Imagen'>";
                }
            } else {
                echo "<p>No hay imágenes para mostrar.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
