<?php
// Datos de la conexión
$host = "localhost";
$user = "root"; // Usuario por defecto en XAMPP
$password = ""; // Por defecto en XAMPP, no tiene contraseña
$database = "krakenbd1";

// Crear la conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar si hay errores de conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>
