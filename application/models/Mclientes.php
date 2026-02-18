<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mclientes extends CI_Model {

    public function get_all_clientes() {
        $this->db->select('
            IdCliente,
            TipoCliente,
            NombreCliente,
            ApellidosCliente,
            CONCAT(NombreCliente, " ", IFNULL(ApellidosCliente, "")) AS NombreCompleto,
            RUC_DNI,
            RazonSocial,
            DireccionCliente,
            Avatar,
            Email,
            Telefono,
            FechaNacimiento,
            EstadoCliente,
            CreatedDate,
            CreatedUser
        ');
        $this->db->order_by('IdCliente', 'DESC');
        $query = $this->db->get('TB_CLIENTE');

        if (!$query) {
            return FALSE;
        }

        return $query->result_array();
    }

    public function get_cliente_by_id($id) {
        $this->db->where('IdCliente', $id);
        $query = $this->db->get('TB_CLIENTE');
        return $query->row_array();
    }

    public function insert_cliente($cliente_data) {
        return $this->db->insert('TB_CLIENTE', $cliente_data);
    }

    public function update_cliente($id_cliente, $cliente_data) {
        $this->db->where('IdCliente', $id_cliente);
        return $this->db->update('TB_CLIENTE', $cliente_data);
    }

    public function delete_cliente($id_cliente) {
        $this->db->where('IdCliente', $id_cliente);
        return $this->db->delete('TB_CLIENTE');
    }

    public function cliente_exists($ruc_dni) {
        $this->db->where('RUC_DNI', $ruc_dni);
        $query = $this->db->get('TB_CLIENTE');
        return $query->num_rows() > 0;
    }
}
