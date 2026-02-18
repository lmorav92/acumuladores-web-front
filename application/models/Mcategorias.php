<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcategorias extends CI_Model {

    /**
     * ==========================================================
     * LISTAR TODAS LAS CATEGORÍAS
     * ==========================================================
     */
    public function get_all_categorias() {
        $this->db->select('
            IdCategoria,
            NombreCategoria,
            DescripcionCategoria,
            ImagenCategoria,
            EstadoCategoria
        ');
        $this->db->from('TB_CATEGORIA_PRODUCTO');
        $this->db->order_by('NombreCategoria', 'ASC');

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * ==========================================================
     * OBTENER CATEGORÍA POR ID
     * ==========================================================
     */
    public function get_by_id($id) {
        $this->db->select('
            IdCategoria,
            NombreCategoria,
            DescripcionCategoria,
            ImagenCategoria,
            EstadoCategoria
        ');
        $this->db->from('TB_CATEGORIA_PRODUCTO');
        $this->db->where('IdCategoria', $id);

        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * ==========================================================
     * INSERTAR CATEGORÍA
     * ==========================================================
     */
    public function insert($data) {
        // Agregar campos por defecto
        if (!isset($data['EstadoCategoria'])) {
            $data['EstadoCategoria'] = 'ACTIVO';
        }
        
        return $this->db->insert('TB_CATEGORIA_PRODUCTO', $data);
    }

    /**
     * ==========================================================
     * ACTUALIZAR CATEGORÍA
     * ==========================================================
     */
    public function update($id, $data) {
        $this->db->where('IdCategoria', $id);
        return $this->db->update('TB_CATEGORIA_PRODUCTO', $data);
    }

    /**
     * ==========================================================
     * ELIMINAR CATEGORÍA
     * ==========================================================
     */
    public function delete($id) {
        // Verificar si hay productos con esta categoría
        $this->db->where('TB_CATEGORIA_PRODUCTO_IdCategoria', $id);
        $this->db->where('EstadoProducto !=', 'DESCONTINUADO');
        $count = $this->db->count_all_results('TB_PRODUCTO');
        
        if ($count > 0) {
            return false; // No se puede eliminar si tiene productos activos
        }
        
        // En lugar de eliminar, cambiar estado a INACTIVO
        return $this->cambiar_estado($id, 'INACTIVO');
    }

    /**
     * ==========================================================
     * ELIMINAR PERMANENTEMENTE
     * ==========================================================
     */
    public function delete_permanently($id) {
        $this->db->where('IdCategoria', $id);
        return $this->db->delete('TB_CATEGORIA_PRODUCTO');
    }

    /**
     * ==========================================================
     * CONTAR PRODUCTOS POR CATEGORÍA
     * ==========================================================
     */
    public function contar_productos($idCategoria) {
        $this->db->where('TB_CATEGORIA_PRODUCTO_IdCategoria', $idCategoria);
        $this->db->where('EstadoProducto !=', 'DESCONTINUADO');
        return $this->db->count_all_results('TB_PRODUCTO');
    }

    /**
     * ==========================================================
     * OBTENER CATEGORÍAS ACTIVAS
     * ==========================================================
     */
    public function get_categorias_activas() {
        $this->db->select('
            IdCategoria,
            NombreCategoria,
            DescripcionCategoria,
            ImagenCategoria,
            EstadoCategoria
        ');
        $this->db->from('TB_CATEGORIA_PRODUCTO');
        $this->db->where('EstadoCategoria', 'ACTIVO');
        $this->db->order_by('NombreCategoria', 'ASC');

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        
        return [];
    }

    /**
     * ==========================================================
     * OBTENER CATEGORÍAS CON CANTIDAD DE PRODUCTOS
     * ==========================================================
     */
    public function get_categorias_con_productos() {
        $this->db->select('
            c.IdCategoria,
            c.NombreCategoria,
            c.DescripcionCategoria,
            c.ImagenCategoria,
            c.EstadoCategoria,
            COUNT(CASE WHEN p.EstadoProducto != "DESCONTINUADO" THEN p.IdProducto END) as CantidadProductos,
            SUM(CASE WHEN p.EstadoProducto = "DISPONIBLE" THEN 1 ELSE 0 END) as ProductosDisponibles,
            SUM(CASE WHEN p.EstadoProducto = "AGOTADO" THEN 1 ELSE 0 END) as ProductosAgotados
        ');
        $this->db->from('TB_CATEGORIA_PRODUCTO c');
        $this->db->join('TB_PRODUCTO p', 'p.TB_CATEGORIA_PRODUCTO_IdCategoria = c.IdCategoria', 'left');
        $this->db->group_by('c.IdCategoria');
        $this->db->order_by('c.NombreCategoria', 'ASC');

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * ==========================================================
     * VERIFICAR SI EXISTE CATEGORÍA POR NOMBRE
     * ==========================================================
     */
    public function existe_categoria($nombre, $excluir_id = null) {
        $this->db->where('NombreCategoria', $nombre);
        
        if ($excluir_id !== null) {
            $this->db->where('IdCategoria !=', $excluir_id);
        }
        
        $query = $this->db->get('TB_CATEGORIA_PRODUCTO');
        return $query->num_rows() > 0;
    }

    /**
     * ==========================================================
     * CAMBIAR ESTADO DE CATEGORÍA
     * ==========================================================
     */
    public function cambiar_estado($id, $estado) {
        $data = array(
            'EstadoCategoria' => $estado
        );
        
        $this->db->where('IdCategoria', $id);
        return $this->db->update('TB_CATEGORIA_PRODUCTO', $data);
    }

    /**
     * ==========================================================
     * OBTENER ESTADÍSTICAS DE CATEGORÍA
     * ==========================================================
     */
    public function get_estadisticas($idCategoria) {
        $this->db->select('
            COUNT(p.IdProducto) as TotalProductos,
            SUM(CASE WHEN p.EstadoProducto = "DISPONIBLE" THEN 1 ELSE 0 END) as Disponibles,
            SUM(CASE WHEN p.EstadoProducto = "AGOTADO" THEN 1 ELSE 0 END) as Agotados,
            SUM(p.Stock) as StockTotal,
            SUM(p.Stock * p.PrecioVenta) as ValorInventario
        ');
        $this->db->from('TB_PRODUCTO p');
        $this->db->where('p.TB_CATEGORIA_PRODUCTO_IdCategoria', $idCategoria);
        $this->db->where('p.EstadoProducto !=', 'DESCONTINUADO');

        $query = $this->db->get();
        return $query->row_array();
    }
}
