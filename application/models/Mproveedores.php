<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mproveedores extends CI_Model {

    public function get_all_proveedores() {
        $this->db->select('
            IdProveedor,
            NombreProveedor,
            RUC,
            Direccion,
            Telefono,
            Email,
            Contacto,
            EstadoProveedor,
            CreatedDate,
            UpdatedDate
        ');
        $this->db->order_by('IdProveedor', 'DESC');
        $query = $this->db->get('TB_PROVEEDOR');

        if (!$query) {
            return FALSE;
        }

        return $query->result_array();
    }

    public function get_proveedor_by_id($id) {
        $this->db->where('IdProveedor', $id);
        $query = $this->db->get('TB_PROVEEDOR');
        return $query->row_array();
    }

    public function insert_proveedor($proveedor_data) {
        return $this->db->insert('TB_PROVEEDOR', $proveedor_data);
    }

    public function update_proveedor($id_proveedor, $proveedor_data) {
        $this->db->where('IdProveedor', $id_proveedor);
        return $this->db->update('TB_PROVEEDOR', $proveedor_data);
    }

    public function delete_proveedor($id_proveedor) {
        $this->db->where('IdProveedor', $id_proveedor);
        return $this->db->delete('TB_PROVEEDOR');
    }

    public function proveedor_exists($ruc) {
        $this->db->where('RUC', $ruc);
        $query = $this->db->get('TB_PROVEEDOR');
        return $query->num_rows() > 0;
    }
}
