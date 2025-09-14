<?php
//Archivo de clase para sanitizar entradas de usuario(si pueden mejorar)

class Sanitizador
{
    public static function limpiarNombre($nombre)
    {
        $nombre = trim($nombre);
        $nombre = strip_tags($nombre);
        $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        return $nombre;
    }

    public static function limpiarApellido($apellido)
    {
        $apellido = trim($apellido);
        $apellido = strip_tags($apellido);
        $apellido = htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8');
        return $apellido;
    }

    public static function limpiarCorreo($correo)
    {
        echo "DEBUG: Recibido para correo: '$correo'<br>";  // 🔍 DEBUG
    $correo = trim($correo);
    echo "DEBUG: Después de trim: '$correo'<br>";       // 🔍 DEBUG
    return $correo;
    }

    // Si en el futuro deseas validar el sexo, también puedes agregarlo aquí
    public static function validarSexo($sexo)
    {
        return in_array($sexo, ['M', 'F']) ? $sexo : null;
    }
    
}
