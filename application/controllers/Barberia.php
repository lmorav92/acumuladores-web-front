<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barberia extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mbarberia');

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

    /**
     * LISTAR BARBERÍAS
     */
    public function list() {
        header('Content-Type: application/json');

        $barberias = $this->Mbarberia->get_all();

        echo json_encode([
            'success' => true,
            'barberias' => $barberias
        ]);
    }

    /**
     * OBTENER BARBERÍA POR ID
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $barberia = $this->Mbarberia->get_by_id($id);

        if ($barberia) {
            echo json_encode([
                'success' => true,
                'barberia' => $barberia
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Barbería no encontrada'
            ]);
        }
    }

    /**
     * CREAR / ACTUALIZAR BARBERÍA
     */
    public function save() {
        header('Content-Type: application/json');

        $idBarberia = $this->input->post('IdBarberia');

        $data = [
            'NombreBarberia' => $this->input->post('NombreBarberia'),
            'Direccion' => $this->input->post('Direccion')
        ];

        if ($idBarberia) {
            // UPDATE
            $res = $this->Mbarberia->update($idBarberia, $data);
            $mensaje = 'Barbería actualizada correctamente';
        } else {
            // INSERT
            $res = $this->Mbarberia->insert($data);
            $mensaje = 'Barbería creada correctamente';
        }

        echo json_encode([
            'success' => $res,
            'message' => $res ? $mensaje : 'Error al guardar la barbería'
        ]);
    }

    /**
     * ELIMINAR BARBERÍA
     */
    public function delete($id) {
        header('Content-Type: application/json');

        $result = $this->Mbarberia->delete($id);

        echo json_encode([
            'success' => $result
        ]);
    }
}
