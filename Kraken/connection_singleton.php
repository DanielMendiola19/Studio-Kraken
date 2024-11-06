<?php
class Database {
    private static $instance = null; // Instancia única de la conexión
    private $connection;

    private $host = "localhost";
    private $user = "root"; // Usuario por defecto en XAMPP
    private $password = ""; // Sin contraseña por defecto en XAMPP
    private $database = "krakenbd1";

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database);

        // Verificar errores de conexión
        if ($this->connection->connect_error) {
            die("Error en la conexión: " . $this->connection->connect_error);
        }
    }

    // Método estático para obtener la única instancia de la conexión
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Método para obtener el objeto de conexión
    public function getConnection() {
        return $this->connection;
    }
}
?>
