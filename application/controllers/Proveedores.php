<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mproveedores');

        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    public function list() {
        header('Content-Type: application/json');

        if (!$this->db->table_exists('TB_PROVEEDOR')) {
            echo json_encode([
                'success' => false,
                'message' => 'La tabla TB_PROVEEDOR no existe en la base de datos.'
            ]);
            return;
        }

        $proveedores = $this->Mproveedores->get_all_proveedores();

        if ($proveedores === FALSE) {
            $db_error = $this->db->error();
            echo json_encode([
                'success'      => false,
                'message'      => 'Error en la consulta SQL',
                'error_detail' => $db_error['message']
            ]);
        } else {
            echo json_encode(['success' => true, 'proveedores' => $proveedores]);
        }
    }

    public function save() {
        header('Content-Type: application/json');

        $id_proveedor = $this->input->post('id_proveedor');

        $proveedor_data = [
            'NombreProveedor'  => $this->input->post('nombre'),
            'RUC'              => $this->input->post('ruc'),
            'Direccion'        => $this->input->post('direccion'),
            'Telefono'         => $this->input->post('telefono'),
            'Email'            => $this->input->post('email'),
            'Contacto'         => $this->input->post('contacto'),
            'EstadoProveedor'  => $this->input->post('estado') ?: 'ACTIVO',
        ];

        if ($id_proveedor) {
            // Actualizar
            $res = $this->Mproveedores->update_proveedor($id_proveedor, $proveedor_data);
        } else {
            // Insertar — CreatedDate se llena automáticamente con CURRENT_TIMESTAMP en la BD
            $res = $this->Mproveedores->insert_proveedor($proveedor_data);
        }

        echo json_encode(['success' => $res]);
    }

    public function delete($id) {
        header('Content-Type: application/json');

        $result = $this->Mproveedores->delete_proveedor($id);
        echo json_encode(['success' => $result]);
    }
}
