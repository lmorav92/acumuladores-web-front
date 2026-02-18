<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mclientes extends CI_Model {

    public function get_all_clientes() {
        $this->db->select('
            IdCliente,
            NombreCliente,
            ApellidosCliente,
            CONCAT(NombreCliente, " ", ApellidosCliente) AS NombreCompleto,
            CarnetCliente,
            DireccionCliente,
            Avatar,
            Email,
            Telefono,
            CreatedDateCliente,
            CreatedUserCliente
        ');
        $this->db->order_by('IdCliente', 'DESC');
        $query = $this->db->get('tb_cliente');
        
        if (!$query) {
            return FALSE;
        }
        
        return $query->result_array();
    }

    public function get_cliente_by_id($id) {
        $this->db->select('*');
        $this->db->where('IdCliente', $id);
        $query = $this->db->get('tb_cliente');
        
        return $query->row_array();
    }

    public function insert_cliente($cliente_data) {
        return $this->db->insert('tb_cliente', $cliente_data);
    }

    public function update_cliente($id_cliente, $cliente_data) {
        $this->db->where('IdCliente', $id_cliente);
        return $this->db->update('tb_cliente', $cliente_data);
    }

    public function delete_cliente($id_cliente) {
        $this->db->where('IdCliente', $id_cliente);
        return $this->db->delete('tb_cliente');
    }

    public function cliente_exists($carnet) {
        $this->db->where('CarnetCliente', $carnet);
        $query = $this->db->get('tb_cliente');
        return $query->num_rows() > 0;
    }
}
