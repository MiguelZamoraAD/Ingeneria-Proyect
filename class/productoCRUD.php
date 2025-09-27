<?php
// class/CRUDcls/productoCrud.php
require_once __DIR__ . '/../class/conexion.php';
require_once __DIR__ . '/../class/sanitiza.php';

class ProductoCrud {
    private $db;
    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    public function crear($datos) {
        try {
            $sql = "INSERT INTO public.producto
                (nombre, descripcion, precio, cantidad, artista_id, categoria_id, imagen_url, creado_en)
                VALUES (:nombre,:descripcion,:precio,:cantidad,:artista,:categoria,:imagen_url, now())
                RETURNING id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre'      => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':precio'      => $datos['precio'],
                ':cantidad'    => $datos['cantidad'],
                ':artista'     => $datos['artista_id'] !== 'none' ? $datos['artista_id'] : null,
                ':categoria'   => $datos['categoria_id'] ?? null,
                ':imagen_url'  => $datos['imagen_url'] ?? null 
            ]);
            return ['ok'=>true,'msg'=>'Producto creado','id'=>$stmt->fetchColumn()];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['ok'=>false,'msg'=>'Error al crear: '.$e->getMessage()];
        }
    }

    public function editar($id,$datos) {
        try {
            $sql = "UPDATE public.producto
                       SET nombre=:nombre, descripcion=:descripcion,
                           precio=:precio, cantidad=:cantidad,
                           artista_id=:artista, categoria_id=:categoria,
                           imagen_url=:imagen_url,
                           actualizado_en=now()
                     WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id'          => $id,
                ':nombre'      => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':precio'      => $datos['precio'],
                ':cantidad'    => $datos['cantidad'],
                ':artista'     => $datos['artista_id'] !== 'none' ? $datos['artista_id'] : null,
                ':categoria'   => $datos['categoria_id'] ?? null,
                ':imagen_url'  => $datos['imagen_url'] ?? null
            ]);
            return ['ok'=>true,'msg'=>'Producto actualizado'];
        } catch (Exception $e) {
            return ['ok'=>false,'msg'=>'Error al editar: '.$e->getMessage()];
        }
    }

    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM public.producto WHERE id = :id");
            $stmt->execute([':id'=>$id]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            return $prod
                ? ['ok'=>true,'producto'=>$prod,'msg'=>'Ok']
                : ['ok'=>false,'msg'=>'Producto no encontrado'];
        } catch (Exception $e) {
            return ['ok'=>false,'msg'=>$e->getMessage()];
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM public.producto WHERE id = :id");
            $stmt->execute([':id'=>$id]);
            return ['ok'=>true,'msg'=>'Producto eliminado'];
        } catch (Exception $e) {
            return ['ok'=>false,'msg'=>'Error al eliminar: '.$e->getMessage()];
        }
    }

    public function listar($limite, $pagina, $busqueda='') {
        $offset = ($pagina-1)*$limite;
        try {
            $where = '';
            $params = [':lim'=>$limite, ':off'=>$offset];
            if ($busqueda !== '') {
                $where = "WHERE nombre ILIKE :busqueda OR descripcion ILIKE :busqueda";
                $params[':busqueda'] = "%$busqueda%";
            }
            $sql = "SELECT * FROM public.producto $where
                    ORDER BY creado_en DESC
                    LIMIT :lim OFFSET :off";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k=>$v) {
                $stmt->bindValue($k, $v, ($k==':lim'||$k==':off')?PDO::PARAM_INT:PDO::PARAM_STR);
            }
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = $this->db->prepare("SELECT count(*) FROM public.producto ".($where? $where:''));
            if ($busqueda !== '') $count->execute([':busqueda'=>"%$busqueda%"]);
            else $count->execute();
            $total = $count->fetchColumn();

            return ['data'=>$data,'total'=>$total,'pagina'=>$pagina,'limite'=>$limite];
        } catch (Exception $e) {
            return ['data'=>[],'total'=>0,'msg'=>$e->getMessage()];
        }
    }
}
