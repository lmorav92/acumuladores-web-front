<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de Login - CORREGIDO
 * Adaptado a la estructura REAL de base de datos acumuladores_db
 * Tabla: TB_USUARIO (sin relación con TB_CLIENTE)
 */
class Mlogin extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Verificar credenciales del usuario
     * @param string $usuario Usuario (UserName)
     * @param string $password Password encriptado en MD5
     * @return array|false Datos del usuario si es correcto, false en caso contrario
     */
    public function Ingresar($usuario, $password) {
        try {
            log_message('info', 'Mlogin::Ingresar - Buscando usuario: ' . $usuario);
            
            // CONSULTA CORREGIDA - TB_USUARIO tiene todos los campos directamente
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
            $this->db->where('UserName', $usuario);
            $this->db->where('UserPassword', $password);
            $this->db->where('UserEstado', 'ACTIVO'); // CORREGIDO: usar ACTIVO en mayúsculas
            
            $query = $this->db->get();
            
            log_message('info', 'Mlogin::Ingresar - Query ejecutado, rows: ' . $query->num_rows());
            
            if ($query->num_rows() == 1) {
                $user = $query->row_array();
                
                log_message('info', 'Mlogin::Ingresar - Usuario encontrado: ' . json_encode($user));
                
                // Separar nombre completo en nombre y apellidos
                $nombre_parts = $this->separar_nombre($user['NombreCompleto']);
                
                // Mapear rol a formato esperado por el sistema
                $rol_mapeado = $this->mapear_rol($user['UserRol']);
                
                // Retornar datos del usuario en formato estándar
                $resultado = array(
                    'id' => $user['IdUsuario'],
                    'usuario' => $user['UserName'],
                    'nombre' => $user['NombreCompleto'] ? $user['NombreCompleto'] : 'Usuario',
                    'nombre_solo' => $nombre_parts['nombre'],
                    'apellidos' => $nombre_parts['apellidos'],
                    'email' => $user['Email'] ? $user['Email'] : ($user['UserName'] . '@sistema.com'),
                    'telefono' => $user['Telefono'] ? $user['Telefono'] : '',
                    'carnet' => '', // No existe en esta estructura
                    'direccion' => '', // No existe en esta estructura
                    'rol' => $rol_mapeado,
                    'rol_original' => $user['UserRol'],
                    'estado' => $user['UserEstado'],
                    'id_cliente' => null, // No existe relación con cliente
                    'avatar' => $this->obtener_avatar($user)
                );
                
                log_message('info', 'Mlogin::Ingresar - Login exitoso. Rol: ' . $rol_mapeado);
                
                // Actualizar último acceso
                $this->actualizar_ultimo_acceso($user['IdUsuario']);
                
                return $resultado;
            }
            
            log_message('warning', 'Mlogin::Ingresar - Credenciales incorrectas para: ' . $usuario);
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Mlogin::Ingresar - ERROR: ' . $e->getMessage());
            log_message('error', 'Mlogin::Ingresar - Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Mapear roles de la BD a roles del sistema
     * BD: ADMIN, VENDEDOR, ALMACEN, CLIENTE
     * Sistema: Administrador, Empleado, Cliente
     */
    private function mapear_rol($rol_bd) {
        $rol_bd = trim(strtoupper($rol_bd));
        
        switch ($rol_bd) {
            case 'ADMIN':
                return 'Administrador';
            
            case 'VENDEDOR':
            case 'ALMACEN':
                return 'Empleado';
            
            case 'CLIENTE':
                return 'Cliente';
            
            default:
                log_message('warning', 'Rol desconocido: ' . $rol_bd . ' - Asignando Cliente por defecto');
                return 'Cliente';
        }
    }
    
    /**
     * Separar nombre completo en nombre y apellidos
     */
    private function separar_nombre($nombre_completo) {
        if (empty($nombre_completo)) {
            return array('nombre' => 'Usuario', 'apellidos' => '');
        }
        
        $partes = explode(' ', trim($nombre_completo), 2);
        
        return array(
            'nombre' => $partes[0],
            'apellidos' => isset($partes[1]) ? $partes[1] : ''
        );
    }
    
    /**
     * Obtener avatar del usuario
     */
    private function obtener_avatar($user) {
        if (!empty($user['Avatar'])) {
            // Si tiene avatar guardado, retornar la URL
            return base_url('uploads/avatars/' . $user['Avatar']);
        } else {
            // Generar avatar con UI Avatars
            $nombre = $user['NombreCompleto'] ? $user['NombreCompleto'] : $user['UserName'];
            return 'https://ui-avatars.com/api/?name=' . urlencode($nombre) . '&background=random&color=fff&size=200';
        }
    }
    
    /**
     * Actualizar último acceso del usuario
     */
    private function actualizar_ultimo_acceso($id_usuario) {
        try {
            $data = array(
                'UltimoAcceso' => date('Y-m-d H:i:s'),
                'IntentosLogin' => 0 // Resetear intentos fallidos
            );
            
            $this->db->where('IdUsuario', $id_usuario);
            $this->db->update('TB_USUARIO', $data);
            
            log_message('info', 'Último acceso actualizado para usuario: ' . $id_usuario);
            
        } catch (Exception $e) {
            log_message('error', 'Error al actualizar último acceso: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener información completa del usuario por ID
     * @param int $user_id ID del usuario (IdUsuario)
     * @return array|false Datos del usuario
     */
    public function get_user_by_id($user_id) {
        try {
            $this->db->select('*');
            $this->db->from('TB_USUARIO');
            $this->db->where('IdUsuario', $user_id);
            
            $query = $this->db->get();
            
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error en get_user_by_id: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si existe un usuario
     * @param string $usuario UserName
     * @return bool
     */
    public function usuario_existe($usuario) {
        try {
            $this->db->where('UserName', $usuario);
            $query = $this->db->get('TB_USUARIO');
            
            return $query->num_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error en usuario_existe: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si existe un email
     * @param string $email
     * @return bool
     */
    public function email_existe($email) {
        try {
            $this->db->where('Email', $email);
            $query = $this->db->get('TB_USUARIO');
            
            return $query->num_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error en email_existe: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los usuarios activos
     * @return array Lista de usuarios
     */
    public function get_usuarios_activos() {
        try {
            $this->db->select('*');
            $this->db->from('TB_USUARIO');
            $this->db->where('UserEstado', 'ACTIVO');
            $this->db->order_by('NombreCompleto', 'ASC');
            
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Error en get_usuarios_activos: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Cambiar contraseña de usuario
     * @param int $user_id ID del usuario
     * @param string $nueva_password Nueva contraseña (ya en MD5)
     * @return bool
     */
    public function cambiar_password($user_id, $nueva_password) {
        try {
            $data = array(
                'UserPassword' => $nueva_password,
                'UpdatedDate' => date('Y-m-d H:i:s')
            );
            
            $this->db->where('IdUsuario', $user_id);
            return $this->db->update('TB_USUARIO', $data);
        } catch (Exception $e) {
            log_message('error', 'Error en cambiar_password: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar estado del usuario
     * @param int $user_id ID del usuario
     * @param string $estado 'ACTIVO', 'INACTIVO' o 'BLOQUEADO'
     * @return bool
     */
    public function actualizar_estado($user_id, $estado) {
        try {
            $estados_validos = array('ACTIVO', 'INACTIVO', 'BLOQUEADO');
            
            if (!in_array($estado, $estados_validos)) {
                log_message('error', 'Estado inválido: ' . $estado);
                return false;
            }
            
            $data = array(
                'UserEstado' => $estado,
                'UpdatedDate' => date('Y-m-d H:i:s')
            );
            
            $this->db->where('IdUsuario', $user_id);
            return $this->db->update('TB_USUARIO', $data);
        } catch (Exception $e) {
            log_message('error', 'Error en actualizar_estado: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear nuevo usuario
     * @param array $datos_usuario Datos del usuario
     * @return int|false ID del usuario creado o false
     */
    public function crear_usuario($datos_usuario) {
        try {
            // Validar que el username no exista
            if ($this->usuario_existe($datos_usuario['username'])) {
                log_message('error', 'El username ya existe: ' . $datos_usuario['username']);
                return false;
            }
            
            // Validar email si se proporciona
            if (!empty($datos_usuario['email']) && $this->email_existe($datos_usuario['email'])) {
                log_message('error', 'El email ya existe: ' . $datos_usuario['email']);
                return false;
            }
            
            $usuario_data = array(
                'UserName' => $datos_usuario['username'],
                'UserPassword' => md5($datos_usuario['password']),
                'UserRol' => isset($datos_usuario['rol']) ? $datos_usuario['rol'] : 'VENDEDOR',
                'NombreCompleto' => isset($datos_usuario['nombre_completo']) ? $datos_usuario['nombre_completo'] : '',
                'Email' => isset($datos_usuario['email']) ? $datos_usuario['email'] : null,
                'Telefono' => isset($datos_usuario['telefono']) ? $datos_usuario['telefono'] : null,
                'UserEstado' => 'ACTIVO',
                'IntentosLogin' => 0,
                'CreatedDate' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('TB_USUARIO', $usuario_data);
            $id_usuario = $this->db->insert_id();
            
            if ($id_usuario) {
                log_message('info', 'Usuario creado exitosamente: ' . $datos_usuario['username'] . ' (ID: ' . $id_usuario . ')');
                return $id_usuario;
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error en crear_usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Incrementar intentos de login fallidos
     */
    public function incrementar_intentos($username) {
        try {
            $this->db->set('IntentosLogin', 'IntentosLogin + 1', FALSE);
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO');
            
            // Si llega a 5 intentos, bloquear usuario
            $this->db->select('IntentosLogin');
            $this->db->where('UserName', $username);
            $query = $this->db->get('TB_USUARIO');
            
            if ($query->num_rows() > 0) {
                $row = $query->row();
                if ($row->IntentosLogin >= 5) {
                    $this->actualizar_estado_por_username($username, 'BLOQUEADO');
                    log_message('warning', 'Usuario bloqueado por intentos fallidos: ' . $username);
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error en incrementar_intentos: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar estado por username
     */
    private function actualizar_estado_por_username($username, $estado) {
        try {
            $this->db->where('UserName', $username);
            $this->db->update('TB_USUARIO', array('UserEstado' => $estado));
        } catch (Exception $e) {
            log_message('error', 'Error en actualizar_estado_por_username: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de usuarios
     * @return array Estadísticas
     */
    public function get_estadisticas_usuarios() {
        try {
            $stats = array();
            
            // Total usuarios
            $stats['total'] = $this->db->count_all('TB_USUARIO');
            
            // Usuarios activos
            $this->db->where('UserEstado', 'ACTIVO');
            $stats['activos'] = $this->db->count_all_results('TB_USUARIO');
            
            // Usuarios inactivos
            $this->db->where('UserEstado', 'INACTIVO');
            $stats['inactivos'] = $this->db->count_all_results('TB_USUARIO');
            
            // Usuarios bloqueados
            $this->db->where('UserEstado', 'BLOQUEADO');
            $stats['bloqueados'] = $this->db->count_all_results('TB_USUARIO');
            
            // Por rol
            $this->db->where('UserRol', 'ADMIN');
            $stats['admin'] = $this->db->count_all_results('TB_USUARIO');
            
            $this->db->where('UserRol', 'VENDEDOR');
            $stats['vendedor'] = $this->db->count_all_results('TB_USUARIO');
            
            $this->db->where('UserRol', 'ALMACEN');
            $stats['almacen'] = $this->db->count_all_results('TB_USUARIO');
            
            $this->db->where('UserRol', 'CLIENTE');
            $stats['cliente'] = $this->db->count_all_results('TB_USUARIO');
            
            return $stats;
            
        } catch (Exception $e) {
            log_message('error', 'Error en get_estadisticas_usuarios: ' . $e->getMessage());
            return array();
        }
    }
}
