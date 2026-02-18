<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de Autenticación - CORREGIDO
 * Adaptado a la estructura REAL de acumuladores_db
 */
class Mauth extends CI_Model {

    // 1. Verificar si el usuario está bloqueado por intentos fallidos
    public function verificar_bloqueo($username) {
        try {
            // Verificar si el usuario existe y está bloqueado
            $this->db->select('UserEstado, IntentosLogin');
            $this->db->from('TB_USUARIO');
            $this->db->where('UserName', $username);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $user = $query->row();
                
                // Si está bloqueado, retornar true
                if ($user->UserEstado == 'BLOQUEADO') {
                    log_message('warning', "Usuario bloqueado: $username");
                    return true;
                }
                
                // Si tiene 5 o más intentos, bloquear
                if ($user->IntentosLogin >= 5) {
                    $this->bloquear_usuario($username);
                    log_message('warning', "Usuario auto-bloqueado por intentos: $username");
                    return true;
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error en verificar_bloqueo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bloquear usuario
     */
    private function bloquear_usuario($username) {
        try {
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO', array(
                'UserEstado' => 'BLOQUEADO',
                'UpdatedDate' => date('Y-m-d H:i:s')
            ));
        } catch (Exception $e) {
            log_message('error', 'Error en bloquear_usuario: ' . $e->getMessage());
        }
    }

    // 2. Registrar intento fallido
    public function registrar_intento_fallido($username) {
        try {
            // Incrementar contador de intentos en TB_USUARIO
            $this->db->set('IntentosLogin', 'IntentosLogin + 1', FALSE);
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO');
            
            log_message('info', "Intento fallido registrado para: $username");
            
            // Si existe tabla de intentos, también registrar allí
            if ($this->db->table_exists('tb_login_intentos')) {
                $data = array(
                    'UserNameIntento' => $username,
                    'FechaHoraIntento' => date('Y-m-d H:i:s'),
                    'IpIntento' => $this->input->ip_address(),
                    'Bloqueado' => 0
                );
                
                $this->db->insert('tb_login_intentos', $data);
            }
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en registrar_intento_fallido: ' . $e->getMessage());
            return false;
        }
    }

    // 3. Registrar acceso (Login/Logout)
    public function registrar_log_acceso($id_usuario, $tipo) {
        try {
            // Solo registrar si existe la tabla
            if (!$this->db->table_exists('tb_log_acceso')) {
                log_message('info', 'Tabla tb_log_acceso no existe, saltando registro');
                return true;
            }
            
            $data = array(
                'TB_USUARIO_IdUsuario' => $id_usuario,
                'IpAcceso' => $this->input->ip_address(),
                'NavegadorAcceso' => $this->input->user_agent(),
                'TipoAcceso' => $tipo,
                'FechaHoraAcceso' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('tb_log_acceso', $data);
            
            log_message('info', "Log de acceso registrado - Usuario: $id_usuario, Tipo: $tipo");
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en registrar_log_acceso: ' . $e->getMessage());
            return false;
        }
    }

    // 4. Gestionar Sesiones Activas
    public function crear_sesion_db($id_usuario) {
        try {
            // Solo crear si existe la tabla
            if (!$this->db->table_exists('tb_sesiones_activas')) {
                log_message('info', 'Tabla tb_sesiones_activas no existe, saltando creación');
                return true;
            }
            
            $data = array(
                'TB_USUARIO_IdUsuario' => $id_usuario,
                'SessionId' => session_id(),
                'FechaInicio' => date('Y-m-d H:i:s'),
                'UltimaActividad' => date('Y-m-d H:i:s'),
                'IpSesion' => $this->input->ip_address(),
                'NavegadorSesion' => $this->input->user_agent(),
                'Activa' => 1
            );
            
            $this->db->insert('tb_sesiones_activas', $data);
            
            log_message('info', "Sesión activa creada - Usuario: $id_usuario, Session: " . session_id());
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en crear_sesion_db: ' . $e->getMessage());
            return false;
        }
    }

    public function cerrar_sesion_db($session_id) {
        try {
            // Solo cerrar si existe la tabla
            if (!$this->db->table_exists('tb_sesiones_activas')) {
                log_message('info', 'Tabla tb_sesiones_activas no existe, saltando cierre');
                return true;
            }
            
            $this->db->where('SessionId', $session_id);
            $this->db->update('tb_sesiones_activas', array(
                'Activa' => 0,
                'FechaFin' => date('Y-m-d H:i:s')
            ));
            
            log_message('info', "Sesión cerrada - Session: $session_id");
            
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en cerrar_sesion_db: ' . $e->getMessage());
            return false;
        }
    }

    // 5. Validar usuario - CORREGIDO para estructura real
    public function validar_usuario($user, $pass) {
        try {
            $this->db->select('
                IdUsuario,
                UserName,
                UserRol,
                NombreCompleto,
                Email,
                Telefono,
                Avatar,
                UserEstado
            ');
            $this->db->from('TB_USUARIO');
            $this->db->where('UserName', $user);
            $this->db->where('UserPassword', $pass);
            $this->db->where('UserEstado', 'ACTIVO');
            
            $query = $this->db->get();
            
            if ($query->num_rows() == 1) {
                $result = $query->row();
                
                log_message('info', 'validar_usuario - Usuario encontrado: ' . $user);
                log_message('info', 'validar_usuario - UserRol: ' . var_export($result->UserRol, true));
                
                return $result;
            }
            
            log_message('warning', 'validar_usuario - Usuario no encontrado o credenciales incorrectas: ' . $user);
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error en validar_usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Limpiar intentos de login antiguos (mantenimiento)
     */
    public function limpiar_intentos_antiguos() {
        try {
            // Si existe la tabla de intentos
            if ($this->db->table_exists('tb_login_intentos')) {
                $this->db->where('FechaHoraIntento <', date('Y-m-d H:i:s', strtotime('-24 hours')));
                $this->db->delete('tb_login_intentos');
                
                $affected = $this->db->affected_rows();
                log_message('info', "Intentos antiguos eliminados: $affected");
                
                return $affected;
            }
            
            return 0;
        } catch (Exception $e) {
            log_message('error', 'Error en limpiar_intentos_antiguos: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Desbloquear un usuario manualmente
     */
    public function desbloquear_usuario($username) {
        try {
            // Resetear intentos y activar usuario
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO', array(
                'UserEstado' => 'ACTIVO',
                'IntentosLogin' => 0,
                'UpdatedDate' => date('Y-m-d H:i:s')
            ));
            
            // Si existe tabla de intentos, limpiar
            if ($this->db->table_exists('tb_login_intentos')) {
                $this->db->where('UserNameIntento', $username);
                $this->db->where('FechaHoraIntento >=', date('Y-m-d H:i:s', strtotime('-1 hour')));
                $this->db->delete('tb_login_intentos');
            }
            
            log_message('info', "Usuario desbloqueado: $username");
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en desbloquear_usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Resetear intentos fallidos después de login exitoso
     */
    public function resetear_intentos($username) {
        try {
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO', array(
                'IntentosLogin' => 0,
                'UpdatedDate' => date('Y-m-d H:i:s')
            ));
            
            log_message('info', "Intentos reseteados para: $username");
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error en resetear_intentos: ' . $e->getMessage());
            return false;
        }
    }
}
