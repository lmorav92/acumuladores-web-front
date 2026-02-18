<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mproductos extends CI_Model {

    /**
     * ==========================================================
     * LISTAR TODOS LOS PRODUCTOS
     * ==========================================================
     */
    public function get_all_productos() {
        $this->db->select('
            p.IdProducto,
            p.CodigoProducto,
            p.TB_CATEGORIA_PRODUCTO_IdCategoria as IdCategoria,
            p.NombreProducto,
            p.DescripcionProducto,
            p.Voltaje,
            p.Amperaje,
            p.TipoAcumulador,
            p.Aplicacion,
            p.Marca,
            p.Modelo,
            p.Garantia,
            p.Stock,
            p.StockMinimo,
            p.StockMaximo,
            p.PrecioCosto,
            p.PrecioVenta,
            p.PorcentajeGanancia,
            p.ImagenProducto,
            p.CodigoBarras,
            p.Peso,
            p.Dimensiones,
            p.EstadoProducto,
            c.NombreCategoria,
            c.DescripcionCategoria,
            prov.NombreProveedor
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->join('TB_CATEGORIA_PRODUCTO c', 'p.TB_CATEGORIA_PRODUCTO_IdCategoria = c.IdCategoria', 'left');
        $this->db->join('TB_PROVEEDOR prov', 'p.TB_PROVEEDOR_IdProveedor = prov.IdProveedor', 'left');
        $this->db->order_by('p.CreatedDate', 'DESC');

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * ==========================================================
     * OBTENER PRODUCTO POR ID
     * ==========================================================
     */
    public function get_by_id($id) {
        $this->db->select('
            p.IdProducto,
            p.CodigoProducto,
            p.TB_CATEGORIA_PRODUCTO_IdCategoria as IdCategoria,
            p.TB_PROVEEDOR_IdProveedor as IdProveedor,
            p.NombreProducto,
            p.DescripcionProducto,
            p.Voltaje,
            p.Amperaje,
            p.TipoAcumulador,
            p.Aplicacion,
            p.Marca,
            p.Modelo,
            p.Garantia,
            p.Stock,
            p.StockMinimo,
            p.StockMaximo,
            p.PrecioCosto,
            p.PrecioVenta,
            p.ImagenProducto,
            p.CodigoBarras,
            p.Peso,
            p.Dimensiones,
            p.EstadoProducto
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->where('p.IdProducto', $id);

        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * ==========================================================
     * INSERTAR PRODUCTO
     * ==========================================================
     */
    public function insert($data) {
        // Agregar usuario que crea
        if (!isset($data['CreatedUser'])) {
            $data['CreatedUser'] = $this->session->userdata('user_id') ?? 1;
        }
        $data['UpdatedUser'] = $data['CreatedUser'];
        
        return $this->db->insert('TB_PRODUCTO', $data);
    }

    /**
     * ==========================================================
     * ACTUALIZAR PRODUCTO
     * ==========================================================
     */
    public function update($id, $data) {
        // Agregar usuario que actualiza
        $data['UpdatedUser'] = $this->session->userdata('user_id') ?? 1;
        
        $this->db->where('IdProducto', $id);
        return $this->db->update('TB_PRODUCTO', $data);
    }

    /**
     * ==========================================================
     * ELIMINAR PRODUCTO (Cambiar a DESCONTINUADO)
     * ==========================================================
     */
    public function delete($id) {
        // En lugar de eliminar, cambiar estado a DESCONTINUADO
        $this->db->where('IdProducto', $id);
        return $this->db->update('TB_PRODUCTO', [
            'EstadoProducto' => 'DESCONTINUADO',
            'UpdatedUser' => $this->session->userdata('user_id') ?? 1
        ]);
    }

    /**
     * ==========================================================
     * ELIMINAR PERMANENTEMENTE
     * ==========================================================
     */
    public function delete_permanently($id) {
        $this->db->where('IdProducto', $id);
        return $this->db->delete('TB_PRODUCTO');
    }

    /**
     * ==========================================================
     * PRODUCTOS POR CATEGORÍA
     * ==========================================================
     */
    public function get_by_categoria($idCategoria) {
        $this->db->select('
            p.IdProducto,
            p.CodigoProducto,
            p.NombreProducto,
            p.DescripcionProducto,
            p.Marca,
            p.Modelo,
            p.Voltaje,
            p.Amperaje,
            p.PrecioVenta,
            p.ImagenProducto,
            p.EstadoProducto,
            p.Stock
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->where('p.TB_CATEGORIA_PRODUCTO_IdCategoria', $idCategoria);
        $this->db->where('p.EstadoProducto !=', 'DESCONTINUADO');
        $this->db->order_by('p.NombreProducto', 'ASC');

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * ==========================================================
     * ACTUALIZAR SOLO STOCK Y ESTADO
     * ==========================================================
     */
    public function actualizar_stock($idProducto, $stock) {
        $estado = 'DISPONIBLE';
        if ($stock <= 0) {
            $estado = 'AGOTADO';
        }
        
        $this->db->where('IdProducto', $idProducto);
        return $this->db->update('TB_PRODUCTO', [
            'Stock' => $stock,
            'EstadoProducto' => $estado,
            'UpdatedUser' => $this->session->userdata('user_id') ?? 1
        ]);
    }

    /**
     * ==========================================================
     * BUSCAR PRODUCTOS
     * ==========================================================
     */
    public function search($term) {
        $this->db->select('
            p.IdProducto,
            p.CodigoProducto,
            p.NombreProducto,
            p.Marca,
            p.Modelo,
            p.PrecioVenta,
            p.Stock,
            p.EstadoProducto,
            c.NombreCategoria
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->join('TB_CATEGORIA_PRODUCTO c', 'p.TB_CATEGORIA_PRODUCTO_IdCategoria = c.IdCategoria', 'left');
        $this->db->group_start();
            $this->db->like('p.NombreProducto', $term);
            $this->db->or_like('p.CodigoProducto', $term);
            $this->db->or_like('p.Marca', $term);
            $this->db->or_like('p.Modelo', $term);
        $this->db->group_end();
        $this->db->where('p.EstadoProducto !=', 'DESCONTINUADO');
        $this->db->limit(20);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PRODUCTOS CON STOCK BAJO
     * ==========================================================
     */
    public function get_stock_bajo() {
        $this->db->select('
            p.IdProducto,
            p.CodigoProducto,
            p.NombreProducto,
            p.Marca,
            p.Stock,
            p.StockMinimo,
            c.NombreCategoria
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->join('TB_CATEGORIA_PRODUCTO c', 'p.TB_CATEGORIA_PRODUCTO_IdCategoria = c.IdCategoria', 'left');
        $this->db->where('p.Stock <=', 'p.StockMinimo', FALSE);
        $this->db->where('p.EstadoProducto !=', 'DESCONTINUADO');
        $this->db->order_by('p.Stock', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * GENERAR CÓDIGO AUTOMÁTICO
     * ==========================================================
     */
    public function generar_codigo($idCategoria) {
        // Obtener prefijo de categoría
        $this->db->select('NombreCategoria');
        $this->db->where('IdCategoria', $idCategoria);
        $categoria = $this->db->get('TB_CATEGORIA_PRODUCTO')->row_array();
        
        if (!$categoria) {
            return 'BAT-' . str_pad($idCategoria, 3, '0', STR_PAD_LEFT) . '-001';
        }
        
        // Generar prefijo basado en categoría
        $prefijos = [
            'Automotriz' => 'AUTO',
            'Motos' => 'MOTO',
            'Camiones' => 'CAM',
            'Marina' => 'MAR',
            'Solar' => 'SOL',
            'UPS' => 'UPS',
            'Tracción' => 'TRA'
        ];
        
        $prefijo = 'BAT';
        foreach ($prefijos as $key => $value) {
            if (stripos($categoria['NombreCategoria'], $key) !== false) {
                $prefijo = $value;
                break;
            }
        }
        
        // Obtener último número
        $this->db->select('CodigoProducto');
        $this->db->like('CodigoProducto', 'BAT-' . $prefijo, 'after');
        $this->db->order_by('IdProducto', 'DESC');
        $this->db->limit(1);
        $ultimo = $this->db->get('TB_PRODUCTO')->row_array();
        
        $numero = 1;
        if ($ultimo) {
            preg_match('/\d+$/', $ultimo['CodigoProducto'], $matches);
            if (isset($matches[0])) {
                $numero = intval($matches[0]) + 1;
            }
        }
        
        return 'BAT-' . $prefijo . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
