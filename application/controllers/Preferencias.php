<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Preferencias extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mpreferencias');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    public function list() {
        header('Content-Type: application/json');
        
        if (!$this->db->table_exists('tb_usuario_preferencias')) {
            echo json_encode([
                'success' => false, 
                'message' => 'La tabla tb_usuario_preferencias no existe en la base de datos.'
            ]);
            return;
        }

        $preferencias = $this->Mpreferencias->get_all_preferencias();

        if ($preferencias === FALSE) {
            $db_error = $this->db->error(); 
            echo json_encode([
                'success' => false,
                'message' => 'Error en la consulta SQL',
                'error_detail' => $db_error['message']
            ]);
        } else {
            echo json_encode(['success' => true, 'preferencias' => $preferencias]);
        }
    }

    public function getUsuarios() {
        header('Content-Type: application/json');
        
        $usuarios = $this->Mpreferencias->get_usuarios_sin_preferencias();
        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
    }

    public function save() {
        $id_preferencia = $this->input->post('id_preferencia');
        
        $preferencia_data = [
            'TB_USUARIO_IdUsuario' => $this->input->post('id_usuario'),
            'TemaInterfaz' => $this->input->post('tema'),
            'IdiomaPreferido' => $this->input->post('idioma'),
            'NotificacionesEmail' => $this->input->post('notif_email') ? 1 : 0,
            'NotificacionesPush' => $this->input->post('notif_push') ? 1 : 0
        ];

        if ($id_preferencia) {
            // Update
            $preferencia_data['UpdatedDate'] = date('Y-m-d');
            $res = $this->Mpreferencias->update_preferencia($id_preferencia, $preferencia_data);
        } else {
            // Insert - Verificar que el usuario no tenga ya preferencias
            $existe = $this->Mpreferencias->preferencia_existe($this->input->post('id_usuario'));
            
            if ($existe) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'El usuario ya tiene preferencias configuradas.'
                ]);
                return;
            }
            
            $preferencia_data['CreatedDate'] = date('Y-m-d');
            $res = $this->Mpreferencias->insert_preferencia($preferencia_data);
        }

        echo json_encode(['success' => $res]);
    }

    public function delete($id) {
        $result = $this->Mpreferencias->delete_preferencia($id);
        echo json_encode(['success' => $result]);
    }

    public function getByUsuario($id_usuario) {
        header('Content-Type: application/json');
        
        $preferencia = $this->Mpreferencias->get_preferencia_by_usuario($id_usuario);
        echo json_encode(['success' => true, 'preferencia' => $preferencia]);
    }

    /**
     * ========================================================================
     * NUEVOS MÉTODOS PARA EL SISTEMA DE TEMAS
     * ========================================================================
     */

    /**
     * Obtener el tema actual del usuario en sesión
     */
    public function getTemaActual() {
        header('Content-Type: application/json');
        
        // Obtener el ID del usuario de la sesión (tu sesión usa 'user_id' no 'IdUsuario')
        $id_usuario = $this->session->userdata('user_id');
        
        // Log para debugging
        log_message('debug', 'getTemaActual - user_id de sesión: ' . $id_usuario);
        
        if (!$id_usuario) {
            echo json_encode([
                'success' => false, 
                'message' => 'Usuario no encontrado en sesión',
                'session_data' => $this->session->userdata()
            ]);
            return;
        }

        // Buscar las preferencias del usuario
        $preferencia = $this->Mpreferencias->get_preferencia_by_usuario($id_usuario);
        
        if ($preferencia && isset($preferencia['TemaInterfaz'])) {
            // Usuario tiene un tema guardado
            log_message('debug', 'Tema encontrado: ' . $preferencia['TemaInterfaz']);
            
            echo json_encode([
                'success' => true, 
                'tema' => $preferencia['TemaInterfaz']
            ]);
        } else {
            // Usuario no tiene preferencias, crear por defecto
            log_message('debug', 'Creando preferencias por defecto para usuario: ' . $id_usuario);
            
            $this->Mpreferencias->create_default_preferencias($id_usuario);
            
            echo json_encode([
                'success' => true, 
                'tema' => 'theme1',
                'created_default' => true
            ]);
        }
    }

    /**
     * Actualizar solo el tema del usuario en sesión
     */
    public function updateTema() {
        header('Content-Type: application/json');
        
        // Obtener el ID del usuario de la sesión (tu sesión usa 'user_id' no 'IdUsuario')
        $id_usuario = $this->session->userdata('user_id');
        $tema = $this->input->post('tema');
        
        // Log para debugging
        log_message('debug', 'updateTema - user_id: ' . $id_usuario . ', Tema: ' . $tema);
        
        if (!$id_usuario) {
            echo json_encode([
                'success' => false, 
                'message' => 'Usuario no encontrado en sesión'
            ]);
            return;
        }

        if (!$tema) {
            echo json_encode([
                'success' => false, 
                'message' => 'Tema no especificado'
            ]);
            return;
        }

        // Verificar si el usuario ya tiene preferencias
        $preferencia_existe = $this->Mpreferencias->get_preferencia_by_usuario($id_usuario);
        
        if ($preferencia_existe) {
            // Actualizar solo el tema
            $data = [
                'TemaInterfaz' => $tema,
                'UpdatedDate' => date('Y-m-d')
            ];
            
            $result = $this->Mpreferencias->update_preferencia($preferencia_existe['IdPreferencia'], $data);
            
            log_message('debug', 'Tema actualizado para usuario: ' . $id_usuario);
        } else {
            // Crear preferencias por defecto con el tema seleccionado
            $data = [
                'TB_USUARIO_IdUsuario' => $id_usuario,
                'TemaInterfaz' => $tema,
                'IdiomaPreferido' => 'es',
                'NotificacionesEmail' => 1,
                'NotificacionesPush' => 1,
                'CreatedDate' => date('Y-m-d')
            ];
            
            $result = $this->Mpreferencias->insert_preferencia($data);
            
            log_message('debug', 'Preferencias creadas con tema para usuario: ' . $id_usuario);
        }

        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Tema actualizado correctamente',
                'tema' => $tema
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar el tema'
            ]);
        }
    }
}
