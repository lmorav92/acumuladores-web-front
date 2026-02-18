<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mestadoturnos extends CI_Model {

    public function get_all_estados() {
        $this->db->select('
            et.IdEstadoTurno,
            et.TB_TURNO_IdTurno as IdTurno,
            et.DescripcionEstadoTurno,
            et.CreatedDateEstadoTurno,
            et.CreatedUserEstadoTurno,
            t.FechaTurno,
            t.EstadoTurno,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) AS NombreCompleto
        ');
        $this->db->from('tb_estado_turno et');
        $this->db->join('tb_turno t', 'et.TB_TURNO_IdTurno = t.IdTurno', 'left');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->order_by('et.IdEstadoTurno', 'DESC');
        
        $query = $this->db->get();
        
        if (!$query) {
            return FALSE;
        }
        
        return $query->result_array();
    }

    public function get_turnos_disponibles() {
        $this->db->select('
            t.IdTurno,
            t.FechaTurno,
            t.EstadoTurno,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) AS NombreCompleto
        ');
        $this->db->from('tb_turno t');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->order_by('t.FechaTurno', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_estado_by_id($id) {
        $this->db->select('*');
        $this->db->where('IdEstadoTurno', $id);
        $query = $this->db->get('tb_estado_turno');
        
        return $query->row_array();
    }

    public function insert_estado($estado_data) {
        return $this->db->insert('tb_estado_turno', $estado_data);
    }

    public function update_estado($id_estado, $estado_data) {
        $this->db->where('IdEstadoTurno', $id_estado);
        return $this->db->update('tb_estado_turno', $estado_data);
    }

    public function delete_estado($id_estado) {
        $this->db->where('IdEstadoTurno', $id_estado);
        return $this->db->delete('tb_estado_turno');
    }

    public function get_estados_by_turno($id_turno) {
        $this->db->select('*');
        $this->db->where('TB_TURNO_IdTurno', $id_turno);
        $this->db->order_by('CreatedDateEstadoTurno', 'DESC');
        $query = $this->db->get('tb_estado_turno');
        
        return $query->result_array();
    }
}
