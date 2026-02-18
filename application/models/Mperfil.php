<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mperfil extends CI_Model {

    /**
     * Obtener información completa del usuario
     */
    public function get_usuario_completo($id_usuario) {
        // Convertir a integer por seguridad
        $id_usuario = intval($id_usuario);
        
        $this->db->select('u.IdUsuario as id_usuario, 
                          u.UserName as username, 
                          u.UserRol as rol_original, 
                          c.IdCliente as id_cliente,
                          c.NombreCliente as nombre, 
                          c.ApellidosCliente as apellidos,
                          CONCAT(c.NombreCliente, " ", c.ApellidosCliente) as nombre_completo,
                          c.CarnetCliente as carnet, 
                          c.Email as email, 
                          c.Telefono as telefono, 
                          c.DireccionCliente as direccion, 
                          c.Avatar as avatar');
        $this->db->from('tb_usuario u');
        $this->db->join('tb_cliente c', 'u.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->where('u.IdUsuario', $id_usuario);
        
        $query = $this->db->get();
        
        log_message('debug', 'Query get_usuario_completo: ' . $this->db->last_query());
        
        $result = $query->row_array();
        
        log_message('debug', 'Resultado get_usuario_completo: ' . print_r($result, true));
        
        return $result;
    }

    /**
     * Actualizar información del cliente
     */
    public function actualizar_cliente($id_cliente, $data) {
        // Convertir a integer
        $id_cliente = intval($id_cliente);
        
        $this->db->where('IdCliente', $id_cliente);
        $result = $this->db->update('tb_cliente', $data);
        
        log_message('debug', 'Update query: ' . $this->db->last_query());
        
        return $result;
    }

    /**
     * Verificar si un carnet ya existe (excepto el del usuario actual)
     */
    public function carnet_existe($carnet, $id_cliente_excluir = null) {
        $this->db->where('CarnetCliente', $carnet);
        if ($id_cliente_excluir) {
            // Convertir a integer
            $id_cliente_excluir = intval($id_cliente_excluir);
            $this->db->where('IdCliente !=', $id_cliente_excluir);
        }
        $query = $this->db->get('tb_cliente');
        return $query->num_rows() > 0;
    }

    /**
     * Obtener preferencias del usuario
     */
    public function get_preferencias_usuario($id_usuario) {
        // Convertir a integer
        $id_usuario = intval($id_usuario);
        
        $this->db->select('IdPreferencia as id_preferencia, 
                          TemaInterfaz as tema_interfaz,
                          IdiomaPreferido as idioma_preferido, 
                          NotificacionesEmail as notificaciones_email,
                          NotificacionesPush as notificaciones_push');
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        $query = $this->db->get('tb_usuario_preferencias');
        
        $result = $query->row_array();
        
        log_message('debug', 'Query get_preferencias_usuario: ' . $this->db->last_query());
        log_message('debug', 'Resultado preferencias: ' . print_r($result, true));
        
        return $result;
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function actualizar_preferencias($id_usuario, $data) {
        // Convertir a integer
        $id_usuario = intval($id_usuario);
        
        $existe = $this->get_preferencias_usuario($id_usuario);
        
        if ($existe) {
            $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
            return $this->db->update('tb_usuario_preferencias', $data);
        } else {
            return $this->db->insert('tb_usuario_preferencias', $data);
        }
    }

    /**
     * Crear preferencias por defecto
     */
    public function crear_preferencias_default($id_usuario) {
        // Convertir a integer
        $id_usuario = intval($id_usuario);
        
        $data = array(
            'TB_USUARIO_IdUsuario' => $id_usuario,
            'TemaInterfaz' => 'theme1',
            'IdiomaPreferido' => 'es',
            'NotificacionesEmail' => 1,
            'NotificacionesPush' => 1,
            'CreatedDate' => date('Y-m-d')
        );
        
        log_message('debug', 'Creando preferencias default para usuario: ' . $id_usuario);
        
        return $this->db->insert('tb_usuario_preferencias', $data);
    }

    /**
     * Contar turnos del cliente
     */
    public function count_turnos_cliente($id_cliente) {
        if (!$id_cliente) {
            return 0;
        }
        
        // Convertir a integer - ESTA ES LA FIX PRINCIPAL
        $id_cliente = intval($id_cliente);
        
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $count = $this->db->count_all_results('tb_turno');
        
        log_message('debug', 'Count turnos cliente: ' . $count . ' para id_cliente: ' . $id_cliente);
        
        return $count;
    }

    /**
     * Contar turnos por estado
     */
    public function count_turnos_cliente_estado($id_cliente, $estado) {
        if (!$id_cliente) {
            return 0;
        }
        
        // Convertir a integer - ESTA ES LA FIX PRINCIPAL
        $id_cliente = intval($id_cliente);
        
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $this->db->where('EstadoTurno', $estado);
        $count = $this->db->count_all_results('tb_turno');
        
        log_message('debug', 'Count turnos estado ' . $estado . ': ' . $count);
        
        return $count;
    }

    /**
     * Obtener último turno del cliente
     */
    public function get_ultimo_turno_cliente($id_cliente) {
        if (!$id_cliente) {
            return null;
        }
        
        // Convertir a integer - ESTA ES LA FIX PRINCIPAL
        $id_cliente = intval($id_cliente);
        
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $this->db->order_by('FechaTurno', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tb_turno');
        
        log_message('debug', 'Query ultimo turno: ' . $this->db->last_query());
        
        return $query->row_array();
    }

    /**
     * Obtener sesiones activas del usuario
     * *** ESTA ES LA FUNCIÓN PRINCIPAL QUE ESTABA FALLANDO ***
     */
    public function get_sesiones_activas($id_usuario) {
        if (!$id_usuario) {
            return array();
        }
        
        // *** CONVERSIÓN A INTEGER - FIX CRÍTICO ***
        $id_usuario = intval($id_usuario);
        
        log_message('debug', '========== get_sesiones_activas ==========');
        log_message('debug', 'ID Usuario (después de intval): ' . $id_usuario);
        log_message('debug', 'Tipo: ' . gettype($id_usuario));
        
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        $this->db->where('Activa', 1);
        $this->db->order_by('UltimaActividad', 'DESC');
        $query = $this->db->get('tb_sesiones_activas');
        
        $result = $query->result_array();
        
        log_message('debug', 'Query ejecutada: ' . $this->db->last_query());
        log_message('debug', 'Sesiones encontradas: ' . count($result));
        
        if (count($result) > 0) {
            log_message('debug', 'Primera sesión: ' . print_r($result[0], true));
        }
        
        return $result;
    }

    /**
     * Cerrar sesión específica
     */
    public function cerrar_sesion($id_sesion) {
        if (!$id_sesion) {
            return false;
        }
        
        // Convertir a integer
        $id_sesion = intval($id_sesion);
        
        $this->db->where('IdSesion', $id_sesion);
        return $this->db->update('tb_sesiones_activas', array('Activa' => 0));
    }

    /**
     * Obtener historial de accesos del usuario
     * *** ESTA TAMBIÉN ESTABA FALLANDO ***
     */
    public function get_historial_usuario($id_usuario, $limit = 20) {
        if (!$id_usuario) {
            return array();
        }
        
        // *** CONVERSIÓN A INTEGER - FIX CRÍTICO ***
        $id_usuario = intval($id_usuario);
        $limit = intval($limit);
        
        log_message('debug', '========== get_historial_usuario ==========');
        log_message('debug', 'ID Usuario (después de intval): ' . $id_usuario);
        
        $this->db->where('TB_USUARIO_IdUsuario', $id_usuario);
        $this->db->order_by('FechaHoraAcceso', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('tb_log_acceso');
        
        $result = $query->result_array();
        
        log_message('debug', 'Query ejecutada: ' . $this->db->last_query());
        log_message('debug', 'Logs encontrados: ' . count($result));
        
        return $result;
    }
}
