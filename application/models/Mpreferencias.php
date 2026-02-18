<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mpreferencias extends CI_Model {

    public function get_all_preferencias() {
        $this->db->select('
            p.IdPreferencia,
            p.TB_USUARIO_IdUsuario as IdUsuario,
            p.TemaInterfaz,
            p.IdiomaPreferido,
            p.NotificacionesEmail,
            p.NotificacionesPush,
            p.CreatedDate,
            p.UpdatedDate,
            u.UserName,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) AS NombreCompleto
        ');
        $this->db->from('tb_usuario_preferencias p');
        $this->db->join('tb_usuario u', 'p.TB_USUARIO_IdUsuario = u.IdUsuario', 'left');
        $this->db->join('tb_cliente c', 'u.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->order_by('p.IdPreferencia', 'DESC');
        
        $query = $this->db->get();
        
        if (!$query) {
            return FALSE;
        }
        
        return $query->result_array();
    }

    public function get_usuarios_sin_preferencias() {
        // Obtener todos los usuarios que NO tienen preferencias
        $this->db->select('
            u.IdUsuario,
            u.UserName,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) AS NombreCompleto
        ');
        $this->db->from('tb_usuario u');
        $this->db->join('tb_cliente c', 'u.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->where('u.UserEstado', 'Activo');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_preferencia_by_id($id) {
        $this->db->select('*');
        $this->db->where('IdPreferencia', $id);
        $query = $this->db->get('tb_usuario_preferencias');
        
        return $query->row_array();
    }

    public function get_preferencia_by_usuario($id_usuario) {
        $this->db->select('*');
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        $query = $this->db->get('tb_usuario_preferencias');
        
        return $query->row_array();
    }

    public function preferencia_existe($id_usuario) {
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        $query = $this->db->get('tb_usuario_preferencias');
        return $query->num_rows() > 0;
    }

    public function insert_preferencia($preferencia_data) {
        return $this->db->insert('tb_usuario_preferencias', $preferencia_data);
    }

    public function update_preferencia($id_preferencia, $preferencia_data) {
        $this->db->where('IdPreferencia', $id_preferencia);
        return $this->db->update('tb_usuario_preferencias', $preferencia_data);
    }

    public function delete_preferencia($id_preferencia) {
        $this->db->where('IdPreferencia', $id_preferencia);
        return $this->db->delete('tb_usuario_preferencias');
    }

    public function create_default_preferencias($id_usuario) {
        // Crear preferencias por defecto para un nuevo usuario
        $default_data = [
            'TB_USUARIO_IdUsuario' => $id_usuario,
            'TemaInterfaz' => 'theme1',
            'IdiomaPreferido' => 'es',
            'NotificacionesEmail' => 1,
            'NotificacionesPush' => 1,
            'CreatedDate' => date('Y-m-d')
        ];
        
        return $this->db->insert('tb_usuario_preferencias', $default_data);
    }

    /**
     * Actualizar solo el tema de un usuario
     */
    public function update_tema_usuario($id_usuario, $tema) {
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        return $this->db->update('tb_usuario_preferencias', [
            'TemaInterfaz' => $tema,
            'UpdatedDate' => date('Y-m-d')
        ]);
    }
}
