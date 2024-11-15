<!-- <?php
include 'connection.php';
session_start();

// Inicializar la variable de intentos fallidos si no existe
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Inicializar la variable de tiempo de bloqueo si no existe
if (!isset($_SESSION['lock_time'])) {
    $_SESSION['lock_time'] = 0;
}

// Si el usuario ya está logueado y es administrador, redirigir al dashboard
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Comprobar si el tiempo de bloqueo ha pasado
if ($_SESSION['login_attempts'] >= 3 && time() - $_SESSION['lock_time'] < 300) { // 300 segundos = 5 minutos
    echo "<p style='color:red;'>Has superado el número máximo de intentos. Por favor, inténtalo nuevamente más tarde.</p>";
    exit();
}

// Fase 1: Validación de email y contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['verify'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']); // Hashea la contraseña (aunque se recomienda usar password_hash en producción)

    // Validar las credenciales del usuario
    $query = "SELECT * FROM usuarios WHERE email_usuario = '$email' AND password_usuario = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['nombre_usuario'] = $row['nombre_usuario'];
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['email'] = $row['email_usuario'];
        $_SESSION['login_attempts'] = 0; // Reiniciar los intentos al iniciar sesión correctamente

        // Generar el código de verificación y almacenarlo
        $codigo_verificacion = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $update_query = "UPDATE usuarios SET codigo_verificacion = '$codigo_verificacion' WHERE id_usuario = ".$row['id_usuario'];
        $conn->query($update_query);

        // Verificar si se encuentra el chat_id
        $chat_id = $row['chat_id']; // Asegúrate de tener el chat_id en la base de datos
        if ($chat_id) {
            $bot_token = "7651645109:AAEXKT7ZKlQPBoSra9NGDqH7eC4aKstK0rs"; // Token real de tu bot
            $message = "Tu código de verificación es: $codigo_verificacion";
            $url = "https://api.telegram.org/bot$bot_token/sendMessage";

            $data = [
                'chat_id' => $chat_id,
                'text' => $message
            ];

            // Enviar la solicitud POST
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);

            // Verifica si la solicitud fue exitosa
            if ($response === false) {
                echo "<p style='color:red;'>Error al enviar el código a Telegram.</p>";
            }
        } else {
            echo "<p style='color:red;'>Chat ID no encontrado.</p>";
        }

        // Redirigir al formulario de verificación de código
        header("Location: verify_code.php");
        exit();
    } else {
        // Incrementar los intentos fallidos
        $_SESSION['login_attempts']++;

        // Verificar si se superaron los 3 intentos
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lock_time'] = time(); // Establecer el tiempo de bloqueo
            $_SESSION['login_attempts'] = 0; // Reiniciar los intentos
            echo "<p style='color:red;'>Has superado el número máximo de intentos. Por favor, inténtalo nuevamente más tarde.</p>";
            exit();
        } else {
            $remaining_attempts = 3 - $_SESSION['login_attempts'];
            echo "<p style='color:red;'>Credenciales incorrectas. Intentos restantes: $remaining_attempts</p>";
        }
    }
}
?>

<!-- Página de Login -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Kraken</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilos personalizados para el formulario */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60vh; /* Ajusta la altura para centrar en la pantalla */
        }
        .form-box {
            background-color: rgba(0, 0, 0, 0.8); /* Fondo semi-transparente */
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
        }
        .form-box h2 {
            margin-bottom: 20px;
        }
        .form-box label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .form-box input[type="email"],
        .form-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
        }
        .form-box button {
            width: 100%;
            padding: 10px;
            background-color: #ff6600; /* Color llamativo para el botón */
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .form-box button:hover {
            background-color: #cc5200; /* Cambio de color al pasar el mouse */
        }
    </style>
</head>
<body style='color: white'>
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
        <div class="form-box">
            <h2>Iniciar Sesión - Administrador</h2>
            <form action="login.php" method="POST">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</main>

<footer>
    <p>© 2024 Kraken. Todos los derechos reservados.</p>
</footer>
</body>
</html> -->
