<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mperfil');
        $this->load->model('Mauth');
        
        // Verificar sesión
        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
                exit;
            }
            redirect('welcome');
        }
    }

    /**
     * Vista principal del perfil - Para SPA
     */
    public function index() {
        $user_id = $this->session->userdata('user_id');
		
		log_message('info', 'Perfil::index() - Iniciando...');
        
        // info: Log información de sesión
        log_message('info', 'Perfil::index() - ID Usuario de sesión: ' . $user_id);
        log_message('info', 'Datos de sesión completos: ' . print_r($this->session->all_userdata(), true));
        
        if (!$user_id) {
            log_message('error', 'Perfil::index() - No hay ID de usuario en sesión');
            show_error('Sesión no válida', 401);
            return;
        }
        
        // Obtener información del usuario y cliente
        $usuario = $this->Mperfil->get_usuario_completo($user_id);
        
        log_message('info', 'Perfil::index() - Usuario obtenido: ' . print_r($usuario, true));
        
        if (!$usuario) {
            log_message('error', 'Perfil::index() - Usuario no encontrado para ID: ' . $user_id);
            
            $error_html = '<div class="container-fluid mt-3">
                <div class="alert alert-danger">
                    <h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Error</h4>
                    <p>No se pudo cargar la información del usuario.</p>
                    <hr>
                    <p class="mb-0">ID de usuario: ' . $user_id . '</p>
                    <p class="mb-0">Por favor, intenta cerrar sesión y volver a iniciar sesión.</p>
                </div>
            </div>';
            
            echo $error_html;
            return;
        }
        
        // Obtener preferencias del usuario
        $preferencias = $this->Mperfil->get_preferencias_usuario($user_id);
        
        log_message('info', 'Perfil::index() - Preferencias obtenidas: ' . print_r($preferencias, true));
        
        // Si no tiene preferencias, crear las predeterminadas
        if (!$preferencias) {
            log_message('info', 'Perfil::index() - Creando preferencias por defecto para usuario: ' . $user_id);
            $this->Mperfil->crear_preferencias_default($user_id);
            $preferencias = $this->Mperfil->get_preferencias_usuario($user_id);
        }
        
        // Obtener estadísticas del usuario
        $id_cliente = isset($usuario['id_cliente']) ? $usuario['id_cliente'] : null;
        $estadisticas = $this->obtener_estadisticas_usuario($id_cliente);
        
        log_message('info', 'Perfil::index() - Estadísticas obtenidas: ' . print_r($estadisticas, true));
        
        // Preparar datos para la vista
        $data = array(
            'usuario' => $usuario,
            'preferencias' => $preferencias,
            'estadisticas' => $estadisticas,
            'page_title' => 'Mi Perfil'
        );
        
        // Cargar solo la vista del contenido (sin header/footer/sidebar)
        $this->load->view('pages/perfil', $data);
    }

    /**
     * Actualizar información del usuario
     */
    public function actualizar_usuario() {
        // Verificar que sea una petición AJAX POST
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        $id_cliente = $this->input->post('idCliente');

        // Validar datos
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'required|trim');
        $this->form_validation->set_rules('carnet', 'Carnet', 'required|trim|max_length[11]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('telefono', 'Teléfono', 'trim|max_length[20]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array(
                'success' => false,
                'message' => validation_errors()
            ));
            return;
        }

        // Verificar que el carnet no esté en uso por otro cliente
        if ($this->Mperfil->carnet_existe($this->input->post('carnet'), $id_cliente)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'El carnet ya está registrado por otro usuario'
            ));
            return;
        }

        // Actualizar datos del cliente
        $data_cliente = array(
            'NombreCliente' => $this->input->post('nombre'),
            'ApellidosCliente' => $this->input->post('apellidos'),
            'CarnetCliente' => $this->input->post('carnet'),
            'Email' => $this->input->post('email'),
            'Telefono' => $this->input->post('telefono'),
            'DireccionCliente' => $this->input->post('direccion')
        );

        if ($this->Mperfil->actualizar_cliente($id_cliente, $data_cliente)) {
            // Actualizar datos en sesión
            $usuario_actualizado = $this->Mperfil->get_usuario_completo($user_id);
            $this->session->set_userdata('nombre', $usuario_actualizado['nombre_completo']);
            
            echo json_encode(array(
                'success' => true,
                'message' => 'Información actualizada correctamente',
                'data' => $usuario_actualizado
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al actualizar la información'
            ));
        }
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function actualizar_preferencias() {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');

        $data = array(
            'TB_USUARIO_IdUsuario' => $user_id,
            'TemaInterfaz' => $this->input->post('temaInterfaz'),
            'IdiomaPreferido' => $this->input->post('idiomaPreferido'),
            'NotificacionesEmail' => $this->input->post('notificacionesEmail') ? 1 : 0,
            'NotificacionesPush' => $this->input->post('notificacionesPush') ? 1 : 0,
            'UpdatedDate' => date('Y-m-d')
        );

        if ($this->Mperfil->actualizar_preferencias($user_id, $data)) {
            $this->session->set_userdata('tema', $data['TemaInterfaz']);
            
            echo json_encode(array(
                'success' => true,
                'message' => 'Preferencias actualizadas correctamente'
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al actualizar las preferencias'
            ));
        }
    }

    /**
     * Actualizar avatar del usuario
     */
    public function actualizar_avatar() {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        $usuario = $this->Mperfil->get_usuario_completo($user_id);
        
        if (!$usuario) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Usuario no encontrado'
            ));
            return;
        }

        $avatar_url = $this->input->post('avatar_url');

        if (empty($avatar_url)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'URL de avatar no proporcionada'
            ));
            return;
        }

        $data = array('Avatar' => $avatar_url);

        if ($this->Mperfil->actualizar_cliente($usuario['id_cliente'], $data)) {
            echo json_encode(array(
                'success' => true,
                'message' => 'Avatar actualizado correctamente',
                'avatar_url' => $avatar_url
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al actualizar el avatar'
            ));
        }
    }

    /**
     * Obtener información del usuario
     */
    public function get_usuario_info() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        log_message('info', 'get_usuario_info - user_id: ' . $user_id);
        
        $usuario = $this->Mperfil->get_usuario_completo($user_id);
        log_message('info', 'get_usuario_info - usuario: ' . print_r($usuario, true));

        if ($usuario) {
            // Obtener preferencias
            $preferencias = $this->Mperfil->get_preferencias_usuario($user_id);
            
            // Si no tiene preferencias, crear las predeterminadas
            if (!$preferencias) {
                $this->Mperfil->crear_preferencias_default($user_id);
                $preferencias = $this->Mperfil->get_preferencias_usuario($user_id);
            }
            
            // Obtener estadísticas
            $id_cliente = isset($usuario['id_cliente']) ? $usuario['id_cliente'] : null;
            $estadisticas = $this->obtener_estadisticas_usuario($id_cliente);
            
            echo json_encode(array(
                'success' => true,
                'data' => $usuario,
                'preferencias' => $preferencias,
                'estadisticas' => $estadisticas
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Usuario no encontrado'
            ));
        }
    }

    /**
     * Obtener sesiones activas
     */
    public function sesiones_activas() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        $sesiones = $this->Mperfil->get_sesiones_activas($user_id);

        echo json_encode(array(
            'success' => true,
            'data' => $sesiones
        ));
    }

    /**
     * Obtener historial de acceso
     */
    public function historial_acceso() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 20;
        $historial = $this->Mperfil->get_historial_usuario($user_id, $limit);

        echo json_encode(array(
            'success' => true,
            'data' => $historial
        ));
    }

    /**
     * Cerrar sesión específica
     */
    public function cerrar_sesion() {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $id_sesion = $this->input->post('id_sesion');

        if ($this->Mperfil->cerrar_sesion($id_sesion)) {
            echo json_encode(array(
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al cerrar la sesión'
            ));
        }
    }

    /**
     * Obtener estadísticas del usuario
     */
    private function obtener_estadisticas_usuario($id_cliente) {
        if (!$id_cliente) {
            log_message('warning', 'obtener_estadisticas_usuario() - ID Cliente no proporcionado');
            return array(
                'total_turnos' => 0,
                'turnos_completados' => 0,
                'turnos_pendientes' => 0,
                'turnos_cancelados' => 0,
                'ultimo_turno' => null
            );
        }
        
        $total_turnos = $this->Mperfil->count_turnos_cliente($id_cliente);
        $turnos_completados = $this->Mperfil->count_turnos_cliente_estado($id_cliente, 'finalizado');
        $turnos_pendientes = $this->Mperfil->count_turnos_cliente_estado($id_cliente, 'reservado') +
                            $this->Mperfil->count_turnos_cliente_estado($id_cliente, 'en_espera') +
                            $this->Mperfil->count_turnos_cliente_estado($id_cliente, 'atendiendo');
        $turnos_cancelados = $this->Mperfil->count_turnos_cliente_estado($id_cliente, 'cancelado');
        $ultimo_turno = $this->Mperfil->get_ultimo_turno_cliente($id_cliente);

        return array(
            'total_turnos' => $total_turnos,
            'turnos_completados' => $turnos_completados,
            'turnos_pendientes' => $turnos_pendientes,
            'turnos_cancelados' => $turnos_cancelados,
            'ultimo_turno' => $ultimo_turno ? $ultimo_turno['FechaTurno'] : null
        );
    }
}
