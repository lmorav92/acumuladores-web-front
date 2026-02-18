<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mauth');
        $this->load->library('session');
    }

public function login() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));

        log_message('info', '=== INICIO LOGIN ===');
        log_message('info', 'Usuario intentando login: ' . $username);

        // A. Verificar si el usuario está bloqueado
        if ($this->Mauth->verificar_bloqueo($username)) {
            log_message('warning', 'Usuario bloqueado: ' . $username);
            echo json_encode(['success' => false, 'message' => 'Usuario bloqueado temporalmente por demasiados intentos.']);
            return;
        }

        // B. Intentar validar credenciales
        $user = $this->Mauth->validar_usuario($username, $password);

        if ($user) {
            // DEBUG: Log para verificar qué datos vienen del modelo
            log_message('info', '✅ Login exitoso - Datos usuario: ' . json_encode($user));
            log_message('info', '✅ Login exitoso - UserRol recibido: ' . var_export($user->UserRol, true));
            log_message('info', '✅ Login exitoso - Tipo de UserRol: ' . gettype($user->UserRol));
            
            // 1. Crear sesión en CodeIgniter - ASEGURAR QUE EL ROL SE GUARDE CORRECTAMENTE
            $session_data = [
                'user_id' => $user->IdUsuario,
                'username' => $user->UserName,
                'rol' => trim($user->UserRol), // TRIM para eliminar espacios
                'id_cliente' => $user->TB_CLIENTE_IdCliente,
                'nombre_completo' => trim($user->NombreCliente . ' ' . $user->ApellidosCliente),
                'email' => $user->Email ?? '',
                'logged_in' => TRUE
            ];
            
            $this->session->set_userdata($session_data);
            
            // DEBUG: Verificar INMEDIATAMENTE que se guardó correctamente
            log_message('info', '✅ Sesión creada - Datos guardados: ' . json_encode($session_data));
            log_message('info', '✅ Sesión creada - Verificación de lectura rol: ' . var_export($this->session->userdata('rol'), true));
            log_message('info', '✅ Sesión creada - user_id: ' . $this->session->userdata('user_id'));
            
            // Verificación adicional
            $rol_guardado = $this->session->userdata('rol');
            if (empty($rol_guardado)) {
                log_message('error', '❌ CRÍTICO: El rol NO se guardó en la sesión!');
            } else {
                log_message('info', '✅ CONFIRMADO: Rol guardado correctamente: ' . $rol_guardado);
            }

            // 2. Llenar tabla tb_log_acceso
            $this->Mauth->registrar_log_acceso($user->IdUsuario, 'login');

            // 3. Llenar tabla tb_sesiones_activas
            $this->Mauth->crear_sesion_db($user->IdUsuario);

            log_message('info', '=== FIN LOGIN EXITOSO ===');
            
            echo json_encode(['success' => true, 'redirect' => base_url('pages/index')]);
        } else {
            // 4. Falló login: Llenar tb_login_intentos
            log_message('warning', '❌ Login fallido para usuario: ' . $username);
            $this->Mauth->registrar_intento_fallido($username);
            
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    }

    public function logout() {
        log_message('info', '=== INICIO LOGOUT ===');
        
        if ($this->session->userdata('user_id')) {
            $user_id = $this->session->userdata('user_id');
            
            log_message('info', 'Cerrando sesión para usuario: ' . $user_id);
            
            // 1. Registrar salida en log
            $this->Mauth->registrar_log_acceso($user_id, 'logout');
            
            // 2. Desactivar sesión en tabla tb_sesiones_activas
            $this->Mauth->cerrar_sesion_db(session_id());
        }

        $this->session->sess_destroy();
        
        log_message('info', '=== FIN LOGOUT ===');
        
        redirect('welcome');
    }
    
    /**
     * Método de debug para verificar sesión
     */
    public function verificar_sesion() {
        header('Content-Type: application/json');
        
        $session_data = $this->session->userdata();
        
        log_message('info', '=== VERIFICAR SESIÓN ===');
        log_message('info', 'Datos completos: ' . json_encode($session_data));
        
        echo json_encode([
            'session_data' => $session_data,
            'user_id' => $this->session->userdata('user_id'),
            'rol' => $this->session->userdata('rol'),
            'username' => $this->session->userdata('username'),
            'id_cliente' => $this->session->userdata('id_cliente'),
            'rol_type' => gettype($this->session->userdata('rol')),
            'rol_empty' => empty($this->session->userdata('rol'))
        ]);
    }
}
