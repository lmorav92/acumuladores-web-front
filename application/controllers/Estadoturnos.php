<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estadoturnos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mestadoturnos');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    public function list() {
        header('Content-Type: application/json');
        
        if (!$this->db->table_exists('tb_estado_turno')) {
            echo json_encode([
                'success' => false, 
                'message' => 'La tabla tb_estado_turno no existe en la base de datos.'
            ]);
            return;
        }

        $estados = $this->Mestadoturnos->get_all_estados();

        if ($estados === FALSE) {
            $db_error = $this->db->error(); 
            echo json_encode([
                'success' => false,
                'message' => 'Error en la consulta SQL',
                'error_detail' => $db_error['message']
            ]);
        } else {
            echo json_encode(['success' => true, 'estados' => $estados]);
        }
    }

    public function getTurnos() {
        header('Content-Type: application/json');
        
        $turnos = $this->Mestadoturnos->get_turnos_disponibles();
        echo json_encode(['success' => true, 'turnos' => $turnos]);
    }

    public function save() {
        $id_estado = $this->input->post('id_estado_turno');
        
        $estado_data = [
            'TB_TURNO_IdTurno' => $this->input->post('id_turno'),
            'DescripcionEstadoTurno' => $this->input->post('descripcion')
        ];

        if ($id_estado) {
            // Update
            $res = $this->Mestadoturnos->update_estado($id_estado, $estado_data);
        } else {
            // Insert
            $estado_data['CreatedDateEstadoTurno'] = date('Y-m-d');
            $estado_data['CreatedUserEstadoTurno'] = $this->session->userdata('user_id');
            $res = $this->Mestadoturnos->insert_estado($estado_data);
        }

        echo json_encode(['success' => $res]);
    }

    public function delete($id) {
        $result = $this->Mestadoturnos->delete_estado($id);
        echo json_encode(['success' => $result]);
    }
}
