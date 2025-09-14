<?php
require_once __DIR__ . '/../class/sanitiza.php';
require_once __DIR__ . '/../class/conexion.php';

class UsuarioLogin {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function registrar($datos) {
    try {
        $nombreCompleto = Sanitizador::limpiarNombre($datos['nombreCompleto']);
        $correo = Sanitizador::limpiarCorreo($datos['correo']);
        $pass1 = $datos['password'];
        $pass2 = $datos['confirmPassword'];
        $tipo = $datos['tipo'] ?? 'user';

        if (!$nombreCompleto || !$correo || !$pass1 || !$pass2) {
            return ['error' => 'Faltan campos obligatorios.'];
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Correo no válido.'];
        }

        if ($pass1 !== $pass2) {
            return ['error' => 'Las contraseñas no coinciden.'];
        }

        // Verificar duplicado
        $sql = "SELECT id FROM usuarios WHERE \"Correo\" = :Correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":Correo", $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['error' => "El usuario ya existe."];
        }

        $password_hash = password_hash($pass1, PASSWORD_DEFAULT);

        $insert = $this->conexion->prepare("
            INSERT INTO usuarios (\"NombreCompleto\", \"Correo\", \"Tipo\", \"HashMagic\")
            VALUES (:NombreCompleto, :Correo, :Tipo, :HashMagic)
        ");
        $insert->bindParam(":NombreCompleto", $nombreCompleto);
        $insert->bindParam(":Correo", $correo);
        $insert->bindParam(":Tipo", $tipo);
        $insert->bindParam(":HashMagic", $password_hash);
        $insert->execute();

         if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['autenticado'] = 'SI';
            $_SESSION['usuario'] = $correo;
            $_SESSION['tipo'] = $tipo;

            return [
                'exito' => true,
                'Correo' => $correo,
                'redirect' => '/inge/index.php' // redirigir a inicio
            ];
    } catch (PDOException $e) {
        return ['error' => "Error al insertar: " . $e->getMessage()];
    }
}


   public function existeCorreo($correo) {
        $sql = 'SELECT 1 FROM usuarios WHERE "Correo" = :correo';
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function estadoConexion() {
        try {
            $this->conexion->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function login($correo, $password) {
        try {
            $sql = 'SELECT "Correo", "HashMagic", "Tipo" FROM usuarios WHERE "Correo" = :correo';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return ['error' => 'Usuario no encontrado.'];
            }

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($password, $usuario['HashMagic'])) {
                return ['error' => 'Contraseña incorrecta.'];
            }

            // Iniciar sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['autenticado'] = 'SI';
            $_SESSION['usuario'] = $usuario['Correo'];
            $_SESSION['tipo'] = $usuario['Tipo'];

            return [
                'exito' => true,
                'Correo' => $usuario['Correo'],
                'redirect' => '/inge/index.php'
            ];

        } catch (PDOException $e) {
            return ['error' => "Error al iniciar sesión: " . $e->getMessage()];
        }
    }


}
