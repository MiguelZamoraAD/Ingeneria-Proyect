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
        $sql = "
            SELECT 
                p.*, 
                a.nombre AS artista_nombre,
                c.nombre AS categoria_nombre
            FROM public.producto p
            LEFT JOIN public.artista a ON p.artista_id = a.id
            LEFT JOIN public.categoria c ON p.categoria_id = c.id
            WHERE p.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            return ['ok' => true, 'producto' => $producto];
        } else {
            return ['ok' => false, 'msg' => 'Producto no encontrado'];
        }
    } catch (Exception $e) {
        return ['ok' => false, 'msg' => 'Error: ' . $e->getMessage()];
    }
}

    public function eliminar($id) {
    try {
        // 1. Obtener datos del producto
        $stmt = $this->db->prepare("SELECT imagen_url, codigos_barra_id FROM public.producto WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            return ['ok'=>false,'msg'=>'Producto no encontrado'];
        }

        // 2. Eliminar imagen en Supabase
        if (!empty($producto['imagen_url'])) {
            $urlParts = explode('/', $producto['imagen_url']);
            $fileName = end($urlParts); // nombre del archivo

            $supabaseUrl = "https://recghdynvcvyzdrtmouj.supabase.co/storage/v1/object/Imagen/{$fileName}";
            $supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlY2doZHludmN2eXpkcnRtb3VqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTc1NTU4MzcsImV4cCI6MjA3MzEzMTgzN30.l7O6l_P3k0TinXjRbj9v6EN0x6iXzLxcuQEUqVtyfdE";

            $ch = curl_init($supabaseUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $supabaseKey",
                "apikey: $supabaseKey"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // (opcional: para entorno local si sigue dando SSL error)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            /*file_put_contents(
                'log_supabase_delete.txt',
                "=== LOG SUPABASE DELETE ===\nIntento borrar: {$fileName}\nHTTP: {$httpCode}\nCurlError: {$curlError}\nRespuesta: {$response}\n\n",
                FILE_APPEND
            );*/

            if ($httpCode !== 200 && $httpCode !== 204) {
                return ['ok'=>false, 'msg'=>"Error HTTP $httpCode al eliminar imagen: $response"];
            }
        }



        // 3. Eliminar código de barras asociado
        if (!empty($producto['codigos_barra_id'])) {
            $stmt = $this->db->prepare("DELETE FROM public.codigo_barra WHERE id = :id");
            $stmt->execute([':id' => $producto['codigos_barra_id']]);
        }

        // 4. Finalmente eliminar el producto
        $stmt = $this->db->prepare("DELETE FROM public.producto WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return ['ok'=>true,'msg'=>'Producto eliminado correctamente'];

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

    public function listarConCategoria($limite, $pagina, $busqueda='') {
    $offset = ($pagina-1)*$limite;
    try {
        $where = '';
        $params = [':lim'=>$limite, ':off'=>$offset];
        if ($busqueda !== '') {
            $where = "WHERE p.nombre ILIKE :busqueda OR p.descripcion ILIKE :busqueda";
            $params[':busqueda'] = "%$busqueda%";
        }

        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM public.producto p
                LEFT JOIN public.categoria c ON p.categoria_id = c.id
                $where
                ORDER BY p.creado_en DESC
                LIMIT :lim OFFSET :off";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k=>$v) {
            $stmt->bindValue($k, $v, ($k==':lim'||$k==':off')?PDO::PARAM_INT:PDO::PARAM_STR);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Conteo total
        $count = $this->db->prepare("SELECT count(*) 
                                     FROM public.producto p
                                     LEFT JOIN public.categoria c ON p.categoria_id = c.id
                                     ".($where? $where:'')); 
        if ($busqueda !== '') $count->execute([':busqueda'=>"%$busqueda%"]);
        else $count->execute();
        $total = $count->fetchColumn();

        return ['data'=>$data,'total'=>$total,'pagina'=>$pagina,'limite'=>$limite];
    } catch (Exception $e) {
        return ['data'=>[],'total'=>0,'msg'=>$e->getMessage()];
    }
}

public function actualizarCantidad($idProducto, $cantidadVendida) {
        try {
            // Restar del stock actual
            $sql = "UPDATE public.producto
                    SET cantidad = cantidad - :cantidadVendida,
                        actualizado_en = NOW()
                    WHERE id = :idProducto";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cantidadVendida' => $cantidadVendida,
                ':idProducto' => $idProducto
            ]);

            // Verificar si se afectó alguna fila
            if ($stmt->rowCount() > 0) {
                return ['ok'=>true,'msg'=>'Stock actualizado correctamente'];
            } else {
                return ['ok'=>false,'msg'=>'No se actualizó el stock (producto no encontrado)'];
            }
        } catch (Exception $e) {
            return ['ok'=>false,'msg'=>'Error al actualizar stock: '.$e->getMessage()];
        }
    }
}
