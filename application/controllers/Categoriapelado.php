<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoriapelado extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mcategoriapelado');

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
     * LISTAR CATEGORÍAS
     */
    public function list() {
        header('Content-Type: application/json');

        $categorias = $this->Mcategoriapelado->get_all();

        echo json_encode([
            'success' => true,
            'categorias' => $categorias
        ]);
    }

    /**
     * OBTENER CATEGORÍA POR ID
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $categoria = $this->Mcategoriapelado->get_by_id($id);

        if ($categoria) {
            echo json_encode([
                'success' => true,
                'categoria' => $categoria
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]);
        }
    }

    /**
     * CREAR / ACTUALIZAR CATEGORÍA
     */
    public function save() {
        header('Content-Type: application/json');

        $idCategoria = $this->input->post('IdCategoriaPelado');

        $data = [
            'NombrePelado' => $this->input->post('NombrePelado'),
            'DescripcionPelado' => $this->input->post('DescripcionPelado')
        ];

        if ($idCategoria) {
            // UPDATE
            $res = $this->Mcategoriapelado->update($idCategoria, $data);
            $mensaje = 'Categoría actualizada correctamente';
        } else {
            // INSERT
            $res = $this->Mcategoriapelado->insert($data);
            $mensaje = 'Categoría creada correctamente';
        }

        echo json_encode([
            'success' => $res,
            'message' => $res ? $mensaje : 'Error al guardar la categoría'
        ]);
    }

    /**
     * ELIMINAR CATEGORÍA
     */
    public function delete($id) {
        header('Content-Type: application/json');

        $result = $this->Mcategoriapelado->delete($id);

        echo json_encode([
            'success' => $result
        ]);
    }
}
