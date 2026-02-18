<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcategoriapelado extends CI_Model {

    /**
     * LISTAR TODAS LAS CATEGORÍAS DE PELADO
     */
    public function get_all() {
        $this->db->select('*');
        $this->db->from('TB_CATEGORIA_PELADO');
        $this->db->order_by('IdCategoriaPelado', 'DESC');
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * OBTENER CATEGORÍA POR ID
     */
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('TB_CATEGORIA_PELADO');
        $this->db->where('IdCategoriaPelado', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }

    /**
     * INSERTAR CATEGORÍA
     */
    public function insert($data) {
        return $this->db->insert('TB_CATEGORIA_PELADO', $data);
    }

    /**
     * ACTUALIZAR CATEGORÍA
     */
    public function update($id, $data) {
        $this->db->where('IdCategoriaPelado', $id);
        return $this->db->update('TB_CATEGORIA_PELADO', $data);
    }

    /**
     * ELIMINAR CATEGORÍA
     */
    public function delete($id) {
        $this->db->where('IdCategoriaPelado', $id);
        return $this->db->delete('TB_CATEGORIA_PELADO');
    }
}
