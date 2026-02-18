<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mcategorias');

        // 🔒 Validar sesión
        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Sesión expirada'
                ]);
                exit;
            } else {
                redirect('welcome');
            }
        }
    }

    /**
     * ==========================================================
     * LISTAR CATEGORÍAS
     * ==========================================================
     */
    public function list() {
        header('Content-Type: application/json');

        try {
            $categorias = $this->Mcategorias->get_all_categorias();

            echo json_encode([
                'success' => true,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en categorias/list: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * LISTAR CATEGORÍAS CON PRODUCTOS
     * ==========================================================
     */
    public function listWithProducts() {
        header('Content-Type: application/json');

        try {
            $categorias = $this->Mcategorias->get_categorias_con_productos();

            echo json_encode([
                'success' => true,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en categorias/listWithProducts: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * OBTENER CATEGORÍA POR ID
     * ==========================================================
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $categoria = $this->Mcategorias->get_by_id($id);

        if ($categoria) {
            // Obtener estadísticas
            $estadisticas = $this->Mcategorias->get_estadisticas($id);
            $categoria['estadisticas'] = $estadisticas;
            
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
     * ==========================================================
     * CREAR / ACTUALIZAR CATEGORÍA
     * ==========================================================
     */
    public function save() {
        header('Content-Type: application/json');

        $idCategoria = $this->input->post('IdCategoria');
        $nombreCategoria = $this->input->post('NombreCategoria');

        // Validar que el nombre no esté vacío
        if (empty(trim($nombreCategoria))) {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de la categoría es obligatorio'
            ]);
            return;
        }

        // Verificar si ya existe una categoría con ese nombre
        if ($this->Mcategorias->existe_categoria($nombreCategoria, $idCategoria)) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe una categoría con ese nombre'
            ]);
            return;
        }

        $data = [
            'NombreCategoria' => $nombreCategoria,
            'DescripcionCategoria' => $this->input->post('DescripcionCategoria')
        ];

        try {
            if ($idCategoria) {
                // 🔄 UPDATE
                $res = $this->Mcategorias->update($idCategoria, $data);
                $mensaje = 'Categoría actualizada correctamente';
            } else {
                // ➕ INSERT
                $res = $this->Mcategorias->insert($data);
                $mensaje = 'Categoría creada correctamente';
            }

            echo json_encode([
                'success' => $res,
                'message' => $res ? $mensaje : 'Error al guardar la categoría'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error al guardar categoría: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * ELIMINAR CATEGORÍA
     * ==========================================================
     */
    public function delete($id) {
        header('Content-Type: application/json');

        try {
            // Contar productos de la categoría
            $count = $this->Mcategorias->contar_productos($id);

            if ($count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "No se puede eliminar. La categoría tiene {$count} producto(s) asociado(s)."
                ]);
                return;
            }

            $result = $this->Mcategorias->delete($id);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Categoría desactivada correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al desactivar la categoría'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Error al eliminar categoría: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * CAMBIAR ESTADO DE CATEGORÍA
     * ==========================================================
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        $id = $this->input->post('IdCategoria');
        $estado = $this->input->post('EstadoCategoria');

        if (!in_array($estado, ['ACTIVO', 'INACTIVO'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Estado inválido'
            ]);
            return;
        }

        try {
            $result = $this->Mcategorias->cambiar_estado($id, $estado);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Estado actualizado' : 'Error al cambiar estado'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * OBTENER CATEGORÍAS ACTIVAS (para select)
     * ==========================================================
     */
    public function getActivas() {
        header('Content-Type: application/json');

        try {
            $categorias = $this->Mcategorias->get_categorias_activas();

            echo json_encode([
                'success' => true,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
