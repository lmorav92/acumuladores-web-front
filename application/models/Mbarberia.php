<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbarberia extends CI_Model {

    /**
     * LISTAR TODAS LAS BARBERÍAS
     */
    public function get_all() {
        $this->db->select('*');
        $this->db->from('TB_BARBERIA');
        $this->db->order_by('IdBarberia', 'DESC');
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * OBTENER BARBERÍA POR ID
     */
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('TB_BARBERIA');
        $this->db->where('IdBarberia', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }

    /**
     * INSERTAR BARBERÍA
     */
    public function insert($data) {
        return $this->db->insert('TB_BARBERIA', $data);
    }

    /**
     * ACTUALIZAR BARBERÍA
     */
    public function update($id, $data) {
        $this->db->where('IdBarberia', $id);
        return $this->db->update('TB_BARBERIA', $data);
    }

    /**
     * ELIMINAR BARBERÍA
     */
    public function delete($id) {
        $this->db->where('IdBarberia', $id);
        return $this->db->delete('TB_BARBERIA');
    }
}
