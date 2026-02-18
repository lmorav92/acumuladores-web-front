<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelado extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mpelado');

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
     * LISTAR SERVICIOS
     */
    public function list() {
        header('Content-Type: application/json');

        if (!$this->db->table_exists('TB_PELADO')) {
            echo json_encode(['success' => false, 'message' => 'La tabla no existe']);
            return;
        }

        $servicios = $this->Mpelado->get_all();

        if ($servicios === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en la consulta SQL'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'servicios' => $servicios
            ]);
        }
    }

    /**
     * OBTENER SERVICIO POR ID
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $servicio = $this->Mpelado->get_by_id($id);

        if ($servicio) {
            echo json_encode([
                'success' => true,
                'servicio' => $servicio
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Servicio no encontrado'
            ]);
        }
    }

    /**
     * CREAR / ACTUALIZAR SERVICIO
     */
    public function save() {
        header('Content-Type: application/json');

        $idPelado = $this->input->post('IdPelado');
        $urlFotoActual = $this->input->post('UrlFotoActual');

        // Manejar subida de imagen
        $nombreFoto = $urlFotoActual;
        
        if (isset($_FILES['foto_servicio']) && $_FILES['foto_servicio']['error'] == 0) {
            $config['upload_path'] = './ui/assets/img/servicios/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = true;

            // Crear directorio si no existe
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('foto_servicio')) {
                $uploadData = $this->upload->data();
                $nombreFoto = $uploadData['file_name'];

                // Eliminar foto anterior si existe
                if ($urlFotoActual && file_exists('./ui/assets/img/servicios/' . $urlFotoActual)) {
                    unlink('./ui/assets/img/servicios/' . $urlFotoActual);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $this->upload->display_errors('', '')
                ]);
                return;
            }
        }

        $data = [
            'TB_CATEGORIA_PELADO_IdCategoriaPelado' => $this->input->post('IdCategoriaPelado'),
            'NombrePelado' => $this->input->post('NombrePelado'),
            'DescripcionPelado' => $this->input->post('DescripcionPelado'),
            'PrecioPelado' => $this->input->post('PrecioPelado'),
            'UrlFoto' => $nombreFoto
        ];

        if ($idPelado) {
            // UPDATE
            $res = $this->Mpelado->update($idPelado, $data);
            $mensaje = 'Servicio actualizado correctamente';
        } else {
            // INSERT
            $res = $this->Mpelado->insert($data);
            $mensaje = 'Servicio creado correctamente';
        }

        echo json_encode([
            'success' => $res,
            'message' => $res ? $mensaje : 'Error al guardar el servicio'
        ]);
    }

    /**
     * ELIMINAR SERVICIO
     */
    public function delete($id) {
        header('Content-Type: application/json');

        // Obtener info del servicio para eliminar la foto
        $servicio = $this->Mpelado->get_by_id($id);
        
        $result = $this->Mpelado->delete($id);

        // Si se eliminó correctamente, eliminar la foto
        if ($result && $servicio && $servicio['UrlFoto']) {
            $rutaFoto = './ui/assets/img/servicios/' . $servicio['UrlFoto'];
            if (file_exists($rutaFoto)) {
                unlink($rutaFoto);
            }
        }

        echo json_encode([
            'success' => $result
        ]);
    }

    /**
     * LISTAR SERVICIOS POR CATEGORÍA
     */
    public function getByCategoria($idCategoria) {
        header('Content-Type: application/json');

        $servicios = $this->Mpelado->get_by_categoria($idCategoria);

        echo json_encode([
            'success' => true,
            'servicios' => $servicios
        ]);
    }
}
