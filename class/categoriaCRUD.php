<?php
require_once __DIR__ . '/conexion.php';

class CategoriaCrud {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    //Listar todas las categorías
    public function listar() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categoria ORDER BY nombre ASC");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    //Obtener una categoría por ID
    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categoria WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
}
