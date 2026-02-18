<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mclientes');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    public function list() {
        header('Content-Type: application/json');
        
        if (!$this->db->table_exists('tb_cliente')) {
            echo json_encode([
                'success' => false, 
                'message' => 'La tabla tb_cliente no existe en la base de datos.'
            ]);
            return;
        }

        $clientes = $this->Mclientes->get_all_clientes();

        if ($clientes === FALSE) {
            $db_error = $this->db->error(); 
            echo json_encode([
                'success' => false,
                'message' => 'Error en la consulta SQL',
                'error_detail' => $db_error['message']
            ]);
        } else {
            echo json_encode(['success' => true, 'clientes' => $clientes]);
        }
    }

    public function save() {
        $id_cliente = $this->input->post('id_cliente');
        
        $cliente_data = [
            'NombreCliente' => $this->input->post('nombre'),
            'ApellidosCliente' => $this->input->post('apellidos'),
            'CarnetCliente' => $this->input->post('carnet'),
            'Email' => $this->input->post('email'),
            'Telefono' => $this->input->post('telefono'),
            'DireccionCliente' => $this->input->post('direccion'),
            'Avatar' => $this->input->post('avatar')
        ];

        if ($id_cliente) {
            // Update
            $res = $this->Mclientes->update_cliente($id_cliente, $cliente_data);
        } else {
            // Insert
            $cliente_data['CreatedDateCliente'] = date('Y-m-d');
            $cliente_data['CreatedUserCliente'] = $this->session->userdata('user_id');
            $res = $this->Mclientes->insert_cliente($cliente_data);
        }

        echo json_encode(['success' => $res]);
    }

    public function delete($id) {
        // Verificar si el cliente tiene usuarios asociados
        $has_users = $this->db->where('TB_CLIENTE_IdCliente', $id)->count_all_results('tb_usuario');
        
        if ($has_users > 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'No se puede eliminar. El cliente tiene usuarios asociados.'
            ]);
            return;
        }

        $result = $this->Mclientes->delete_cliente($id);
        echo json_encode(['success' => $result]);
    }
}
