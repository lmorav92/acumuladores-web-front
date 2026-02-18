<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carrito extends CI_Controller {

	    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mcarrito');

        // Validar sesión
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Sesión expirada'
            ]);
            exit;
        }
    }
    public function agregar() {
 
        $idProducto = $this->input->post('idProducto');

		log_message('info', 'Agregar al carrito - idProducto recibido: ' . $idProducto);
        
        $idCliente  = $_SESSION['id_cliente'];
        $idUsuario  = $_SESSION['user_id'];
       

        $total = $this->Mcarrito->agregarProducto(
            $idCliente,
            $idUsuario,
            $idProducto
        );

        echo json_encode([
            'ok' => true,
            'total' => $total
        ]);
    }

    public function index() {
        $this->load->model('Mcarrito');
        $data['items'] = $this->Mcarrito->obtenerCarrito($_SESSION['id_cliente']);
        $this->load->view('carrito', $data);
    }
}
