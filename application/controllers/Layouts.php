<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador Layouts
 * Maneja la vista principal del dashboard (main.php)
 */
class Layouts extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Cargar helpers y librerías
        $this->load->helper('url');
        $this->load->library('session');
        
        // Verificar que el usuario esté logueado
        if (!$this->session->userdata('logged_in')) {
            redirect('welcome');
        }
    }
    
    /**
     * Vista principal del dashboard
     */
    public function main() {
        // Pasar datos del usuario a la vista
        $data['user'] = array(
            'nombre' => $this->session->userdata('nombre'),
            'usuario' => $this->session->userdata('user'),
            'role' => $this->session->userdata('role'),
            'rol_original' => $this->session->userdata('rol_original'),
            'avatar' => $this->session->userdata('avatar'),
            'carnet' => $this->session->userdata('carnet'),
            'email' => $this->session->userdata('email')
        );
        
        // Cargar la vista principal
        $this->load->view('layouts/main', $data);
    }
    
    /**
     * Cargar contenido dinámico (para SPA)
     */
    public function load_content($page = 'dashboard') {
        // Verificar que el usuario esté logueado
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(array('error' => 'No autenticado'));
            return;
        }
        
        // Cargar la página solicitada
        $view_path = 'pages/' . $page;
        
        // Verificar si la vista existe
        if (file_exists(APPPATH . 'views/' . $view_path . '.php')) {
            $this->load->view($view_path);
        } else {
            // Página no encontrada
            $this->load->view('errors/404_page');
        }
    }

}
