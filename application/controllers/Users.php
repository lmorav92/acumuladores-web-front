<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

public function __construct() {
        parent::__construct();
        // Cargamos la base de datos explícitamente por si no está en autoload
        $this->load->database();
		 $this->load->library('session'); // 🔴 ESTO FALTA
        $this->load->model('Musers');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

public function list() {
    header('Content-Type: application/json');
    
    // 1. Verificamos si la tabla/vista existe antes de pedir datos
    if (!$this->db->table_exists('v_usuarios_completo')) {
        echo json_encode([
            'success' => false, 
            'message' => 'La vista v_usuarios_completo no existe en la base de datos.'
        ]);
        return;
    }

    $usuarios = $this->Musers->get_all_users();

    if ($usuarios === FALSE) {
        // 2. Si falla, capturamos el error exacto de MySQL
        $db_error = $this->db->error(); 
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta SQL',
            'error_detail' => $db_error['message']
        ]);
    } else {
        echo json_encode(['usuarios' => $usuarios]);
    }
}

    public function save() {
        $id_usuario = $this->input->post('id_usuario');
        
        $cliente = [
            'NombreCliente' => $this->input->post('nombre'),
            'ApellidosCliente' => $this->input->post('apellidos'),
            'CarnetCliente' => $this->input->post('carnet'),
            'Email' => $this->input->post('email')
        ];

        $usuario = [
            'UserName' => $this->input->post('username'),
            'UserRol' => $this->input->post('rol'),
            'UserEstado' => 'Activo'
        ];

        if ($id_usuario) {
            // Update
            if (!empty($this->input->post('password'))) {
                $usuario['UserPassword'] = md5($this->input->post('password'));
            }
            $res = $this->Musers->update_user($id_usuario, $this->input->post('id_cliente'), $cliente, $usuario);
        } else {
            // Insert
            $usuario['UserPassword'] = md5($this->input->post('password'));
            $usuario['CreatedDate'] = date('Y-m-d');
            $res = $this->Musers->insert_user($cliente, $usuario);
        }

        echo json_encode(['success' => $res]);
    }

    public function delete($id) {
        echo json_encode(['success' => $this->Musers->delete_user($id)]);
    }
}
