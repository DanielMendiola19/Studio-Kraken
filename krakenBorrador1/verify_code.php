<?php
include 'connection.php';
session_start();

// No verificamos si el usuario está logueado
// Eliminar la condición de verificación de sesión

$id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;

// Paso 1: Generar y enviar el código de verificación al presionar el botón "Enviar código de verificación"
if (isset($_POST['send_code'])) {
    $codigo_verificacion = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Si el usuario está logueado, actualizamos el código de verificación
    if ($id_usuario) {
        $update_query = "UPDATE usuarios SET codigo_verificacion = '$codigo_verificacion' WHERE id_usuario = $id_usuario";
        $conn->query($update_query);
    }

    $bot_token = "7651645109:AAEXKT7ZKlQPBoSra9NGDqH7eC4aKstK0rs";  
    $chat_id = "1386361952"; 
    $message = "Tu código de verificación es: $codigo_verificacion";
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        echo "<p style='color:red;'>Error al enviar el código a Telegram.</p>";
    } else {
        echo "<p style='color:green;'>Código de verificación enviado a tu Telegram.</p>";
    }
}

// Paso 2: Verificar el código ingresado por el usuario
// Paso 2: Verificar el código ingresado por el usuario
if (isset($_POST['verify'])) {
    $codigo = $_POST['codigo'];

    $query = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario AND codigo_verificacion = '$codigo'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<p style='color:red;'>Código de verificación incorrecto.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            color: white;
            font-family: Arial, sans-serif;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
            background-color: #333;
        }
        nav ul li {
            display: inline;
            margin: 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #444;
            border-radius: 5px;
        }
        nav ul li a:hover {
            background-color: #666;
        }
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #fff;
        }
        label {
            color: #fff;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <div id="banner"> 
        <img class="logoBanner" src="recursos/logo.jpg" alt="logoKraken">
    </div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="galeria.php">Galería</a></li>
        <li><a href="#">Tatuadores</a></li>
        <li><a href="#">Servicios</a></li>
        <li><a href="#">Contacto</a></li>
        <li><a href="login.php">Acceso Admin</a></li>
    </ul>
</nav>

<main>
    <div class="form-container">
        <h2>Ingresar Código de Verificación</h2>

        <!-- Botón para enviar el código de verificación -->
        <form action="verify_code.php" method="POST">
            <button type="submit" name="send_code">Enviar código de verificación</button>
        </form>

        <!-- Formulario para verificar el código -->
        <form action="verify_code.php" method="POST">
            <label for="codigo">Código de Verificación:</label>
            <input type="text" id="codigo" name="codigo" required>
            <button type="submit" name="verify">Verificar</button>
        </form>
    </div>
</main>

<footer>
    <p>© 2024 Kraken. Todos los derechos reservados.</p>
</footer>

</body>
</html>
