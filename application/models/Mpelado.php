<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mpelado extends CI_Model {

    /**
     * LISTAR TODOS LOS SERVICIOS (PELADOS)
     */
    public function get_all() {
        $this->db->select('
            p.IdPelado,
            p.TB_CATEGORIA_PELADO_IdCategoriaPelado as IdCategoriaPelado,
            p.NombrePelado,
            p.DescripcionPelado,
            p.UrlFoto,
            p.PrecioPelado,
            c.NombrePelado as NombreCategoria,
            c.DescripcionPelado as DescripcionCategoria
        ');
        $this->db->from('TB_PELADO p');
        $this->db->join('TB_CATEGORIA_PELADO c', 'p.TB_CATEGORIA_PELADO_IdCategoriaPelado = c.IdCategoriaPelado', 'left');
        $this->db->order_by('p.IdPelado', 'DESC');
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * OBTENER SERVICIO POR ID
     */
    public function get_by_id($id) {
        $this->db->select('
            p.IdPelado,
            p.TB_CATEGORIA_PELADO_IdCategoriaPelado as IdCategoriaPelado,
            p.NombrePelado,
            p.DescripcionPelado,
            p.UrlFoto,
            p.PrecioPelado
        ');
        $this->db->from('TB_PELADO p');
        $this->db->where('p.IdPelado', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }

    /**
     * INSERTAR SERVICIO
     */
    public function insert($data) {
        return $this->db->insert('TB_PELADO', $data);
    }

    /**
     * ACTUALIZAR SERVICIO
     */
    public function update($id, $data) {
        $this->db->where('IdPelado', $id);
        return $this->db->update('TB_PELADO', $data);
    }

    /**
     * ELIMINAR SERVICIO
     */
    public function delete($id) {
        $this->db->where('IdPelado', $id);
        return $this->db->delete('TB_PELADO');
    }

    /**
     * SERVICIOS POR CATEGORÍA
     */
    public function get_by_categoria($idCategoria) {
        $this->db->select('
            p.IdPelado,
            p.NombrePelado,
            p.DescripcionPelado,
            p.UrlFoto,
            p.PrecioPelado
        ');
        $this->db->from('TB_PELADO p');
        $this->db->where('p.TB_CATEGORIA_PELADO_IdCategoriaPelado', $idCategoria);
        $this->db->order_by('p.NombrePelado', 'ASC');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
}
