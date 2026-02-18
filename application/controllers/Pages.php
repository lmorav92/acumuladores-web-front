<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador Pages
 * Maneja las páginas del sistema y carga dinámica de contenido
 */
class Pages extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Cargar helpers y librerías necesarios
        $this->load->helper('url');
        $this->load->library('session');
        
        // Verificar que el usuario esté logueado
        if (!$this->session->userdata('logged_in')) {
            // Si es petición AJAX, devolver error JSON
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('error' => 'No autenticado')))
                    ->_display();
                exit;
            }
            // Si no es AJAX, redirigir
            redirect('welcome');
        }
    }
    
    /**
     * Página principal / Dashboard
     */
    public function index() {
        // Cargar la vista principal con datos del usuario
        $data['user'] = array(
            'nombre' => $this->session->userdata('nombre'),
            'usuario' => $this->session->userdata('user'),
            'role' => $this->session->userdata('role'),
            'rol_original' => $this->session->userdata('rol_original'),
            'avatar' => $this->session->userdata('avatar'),
            'carnet' => $this->session->userdata('carnet'),
            'email' => $this->session->userdata('email'),
            'id_cliente' => $this->session->userdata('id_cliente')
        );
        
        $this->load->view('layouts/main', $data);
    }
    
    /**
     * Cargar página dinámica
     * IMPORTANTE: Este método NO debe interceptar las llamadas API del Dashboard
     */
    public function load($page = null) {
        // Log para debugging
        log_message('debug', 'Pages::load() llamado con página: ' . $page);
        
        if (!$page) {
            $page = 'dashboard';
        }
        
        // Verificar que el usuario esté logueado
        if (!$this->session->userdata('logged_in')) {
            // Si es AJAX, retornar error
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('error' => 'Sesión expirada')))
                    ->_display();
                exit;
            }
            
            echo json_encode(array('error' => 'No autenticado'));
            return;
        }
        
        // Sanitizar el nombre de la página
        $page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
        
        // Cargar la vista solicitada
        $view_path = 'pages/' . $page;
        
        log_message('debug', 'Intentando cargar vista: ' . $view_path);
        
        // Verificar si la vista existe
        if (file_exists(APPPATH . 'views/' . $view_path . '.php')) {
            $data['user'] = array(
                'nombre' => $this->session->userdata('nombre'),
                'usuario' => $this->session->userdata('user'),
                'role' => $this->session->userdata('role'),
                'rol_original' => $this->session->userdata('rol_original')
            );
            
            log_message('debug', 'Vista encontrada, cargando...');
            
            // Cargar la vista
            $this->load->view($view_path, $data);
        } else {
            // Página no encontrada
            log_message('error', 'Vista no encontrada: ' . $view_path);
            
            // Mostrar mensaje de error
            $this->output
                ->set_status_header(404)
                ->set_output(
                    '<div class="container-fluid mt-3">' .
                    '<div class="alert alert-warning">' .
                    '<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Página No Encontrada</h4>' .
                    '<p>La página "<strong>' . htmlspecialchars($page) . '</strong>" no existe.</p>' .
                    '<hr>' .
                    '<p class="mb-0">Ruta buscada: <code>application/views/pages/' . htmlspecialchars($page) . '.php</code></p>' .
                    '</div>' .
                    '<div class="text-center">' .
                    '<a href="' . base_url('pages/index') . '" class="btn btn-primary">Volver al Dashboard</a>' .
                    '</div>' .
                    '</div>'
                );
        }
    }
    
    /**
     * Página de dashboard (método directo)
     */
    public function dashboard() {
        $data['titulo'] = 'Dashboard';
        $data['user'] = array(
            'nombre' => $this->session->userdata('nombre'),
            'usuario' => $this->session->userdata('user'),
            'role' => $this->session->userdata('role'),
            'rol_original' => $this->session->userdata('rol_original')
        );
        
        $this->load->view('pages/dashboard', $data);
    }
    
    /**
     * Usuarios (solo para administradores)
     */
    public function usuarios() {
        // Solo administradores
        if ($this->session->userdata('role') !== 'administrador') {
            $this->output
                ->set_status_header(403)
                ->set_output(
                    '<div class="alert alert-danger">No tienes permisos para acceder a esta página</div>'
                );
            return;
        }
        
        $data['titulo'] = 'Gestión de Usuarios';
        $this->load->view('pages/usuarios', $data);
    }
    
    /**
     * Turnos
     */
    public function turnos() {
        $data['titulo'] = 'Gestión de Turnos';
        $this->load->view('pages/turnos', $data);
    }
    
    /**
     * Reportes
     */
    public function reportes() {
        $data['titulo'] = 'Reportes';
        $this->load->view('pages/reportes', $data);
    }
    
    /**
     * Configuración (solo para administradores)
     */
    public function configuracion() {
        // Solo administradores
        if ($this->session->userdata('role') !== 'administrador') {
            show_error('No tienes permisos para acceder a esta página', 403);
            return;
        }
        
        $data['titulo'] = 'Configuración';
        $this->load->view('pages/configuracion', $data);
    }

}
