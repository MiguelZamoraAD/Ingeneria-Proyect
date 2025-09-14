<?php    
class Conexion {
    private $conexion;

    public function __construct() {
        // Configuración del Pooler de sesiones con IPv4
        $sql_host = "aws-1-us-east-2.pooler.supabase.com";
        $sql_port = "5432                                                                                                                                                                                                                                                                                                               ";
        $sql_name = "postgres";
        $sql_user = "postgres.recghdynvcvyzdrtmouj";
        $sql_pass = "v8QoOtnGAQhHGgNc";  // Tu contraseña real

        $dsn = "pgsql:host=$sql_host;port=$sql_port;dbname=$sql_name";

        try {
            $this->conexion = new PDO($dsn, $sql_user, $sql_pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
