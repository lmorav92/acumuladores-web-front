<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Musers extends CI_Model {

public function get_all_users() {
    $this->db->select('*');
    $query = $this->db->get('v_usuarios_completo');
    
    if (!$query) {
        return FALSE;
    }
    
    return $query->result_array();
}

    public function insert_user($cliente_data, $usuario_data) {
        $this->db->trans_start();
        $this->db->insert('tb_cliente', $cliente_data);
        $usuario_data['TB_CLIENTE_IdCliente'] = $this->db->insert_id();
        $this->db->insert('tb_usuario', $usuario_data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_user($id_usuario, $id_cliente, $cliente_data, $usuario_data) {
        $this->db->trans_start();
        $this->db->where('IdCliente', $id_cliente)->update('tb_cliente', $cliente_data);
        $this->db->where('IdUsuario', $id_usuario)->update('tb_usuario', $usuario_data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete_user($id_usuario) {
        // Obtenemos el ID de cliente antes de borrar para limpiar ambas tablas
        $user = $this->db->get_where('tb_usuario', ['IdUsuario' => $id_usuario])->row();
        if ($user) {
            $this->db->where('IdUsuario', $id_usuario)->delete('tb_usuario');
            $this->db->where('IdCliente', $user->TB_CLIENTE_IdCliente)->delete('tb_cliente');
            return true;
        }
        return false;
    }
}
