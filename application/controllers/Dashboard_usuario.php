<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador Dashboard_usuario
 * Maneja el dashboard y estadísticas del usuario normal
 */
class Dashboard_usuario extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->database();
        
        // Verificar que el usuario esté logueado
        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('success' => false, 'message' => 'No autenticado')))
                    ->_display();
                exit;
            }
            redirect('welcome');
        }
    }
    
    /**
     * Obtener el ID del cliente del usuario logueado
     */
    private function get_id_cliente() {
        // Primero intentar obtener de la sesión
        $id_cliente = $this->session->userdata('id_cliente');
        
        log_message('info', 'get_id_cliente - id_cliente de sesión: ' . var_export($id_cliente, true));
        
        // Si no está en sesión, buscar en la base de datos
        if (!$id_cliente) {
            $user_id = $this->session->userdata('user_id');
            
            log_message('info', 'get_id_cliente - user_id: ' . $user_id);
            
            if ($user_id) {
                // Buscar el cliente asociado al usuario
                $this->db->select('TB_CLIENTE_IdCliente');
                $this->db->where('IdUsuario', $user_id);
                $query = $this->db->get('tb_usuario');
                
                if ($query->num_rows() > 0) {
                    $usuario = $query->row();
                    $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                    
                    log_message('info', 'get_id_cliente - encontrado en BD: ' . $id_cliente);
                    
                    // Guardar en sesión para futuras peticiones
                    $this->session->set_userdata('id_cliente', $id_cliente);
                }
            }
        }
        
        log_message('info', 'get_id_cliente - retornando: ' . var_export($id_cliente, true));
        
        return $id_cliente;
    }
    
    /**
     * Obtener estadísticas del usuario
     */
    public function mis_estadisticas() {
        header('Content-Type: application/json');
        
        try {
            $id_cliente = $this->get_id_cliente();
            
            if (!$id_cliente) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'ID de cliente no encontrado',
                    'debug' => array(
                        'session_data' => $this->session->userdata(),
                        'user_id' => $this->session->userdata('user_id')
                    )
                ));
                return;
            }
            
            // Total de turnos
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $total_turnos = $this->db->count_all_results('tb_turno');
            
            // Turnos pendientes (reservado, en_espera, atendiendo)
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $this->db->where_in('EstadoTurno', array('reservado', 'en_espera', 'atendiendo'));
            $turnos_pendientes = $this->db->count_all_results('tb_turno');
            
            // Turnos completados
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $this->db->where('EstadoTurno', 'finalizado');
            $turnos_completados = $this->db->count_all_results('tb_turno');
            
            // Turnos cancelados
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $this->db->where('EstadoTurno', 'cancelado');
            $turnos_cancelados = $this->db->count_all_results('tb_turno');
            
            echo json_encode(array(
                'success' => true,
                'stats' => array(
                    'total_turnos' => $total_turnos,
                    'turnos_pendientes' => $turnos_pendientes,
                    'turnos_completados' => $turnos_completados,
                    'turnos_cancelados' => $turnos_cancelados
                ),
                'debug' => array(
                    'id_cliente' => $id_cliente
                )
            ));
        } catch (Exception $e) {
            log_message('error', 'Error en mis_estadisticas: ' . $e->getMessage());
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Obtener próximo turno del usuario
     * CORREGIDO: Solo muestra turnos futuros (fecha > hoy O fecha = hoy Y hora > ahora)
     */
    public function proximo_turno() {
        header('Content-Type: application/json');
        
        try {
            $id_cliente = $this->get_id_cliente();
            
            if (!$id_cliente) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'ID de cliente no encontrado'
                ));
                return;
            }
            
            $fecha_actual = date('Y-m-d');
            $hora_actual = date('H:i:s');
            
            log_message('info', "proximo_turno - Buscando para cliente: $id_cliente, fecha: $fecha_actual, hora: $hora_actual");
            
            // Buscar el próximo turno
            // Opción 1: Turnos de fechas futuras
            // Opción 2: Turnos de hoy pero con hora de inicio > hora actual
            $this->db->select('IdTurno, FechaTurno, HorarioTurno, NumeroTurno, HoraInicio, HoraFin, EstadoTurno');
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $this->db->where_in('EstadoTurno', array('reservado', 'en_espera', 'atendiendo'));
            
            // Condición: (fecha > hoy) O (fecha = hoy Y hora inicio > hora actual)
            $this->db->group_start();
                $this->db->where('FechaTurno >', $fecha_actual);
                $this->db->or_group_start();
                    $this->db->where('FechaTurno', $fecha_actual);
                    $this->db->where('HoraInicio >', $hora_actual);
                $this->db->group_end();
            $this->db->group_end();
            
            $this->db->order_by('FechaTurno', 'ASC');
            $this->db->order_by('NumeroTurno', 'ASC');
            $this->db->limit(1);
            
            $query = $this->db->get('tb_turno');
            $turno = $query->row_array();
            
            log_message('info', 'proximo_turno - Resultado: ' . json_encode($turno));
            
            if ($turno) {
                echo json_encode(array(
                    'success' => true,
                    'turno' => $turno
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'No hay turnos próximos'
                ));
            }
        } catch (Exception $e) {
            log_message('error', 'Error en proximo_turno: ' . $e->getMessage());
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al obtener próximo turno',
                'error' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Obtener mis turnos recientes
     */
    public function mis_turnos() {
        header('Content-Type: application/json');
        
        try {
            $id_cliente = $this->get_id_cliente();
            
            if (!$id_cliente) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'ID de cliente no encontrado'
                ));
                return;
            }
            
            log_message('info', "mis_turnos - Buscando turnos para cliente: $id_cliente");
            
            // Obtener últimos 10 turnos
            $this->db->select('IdTurno, FechaTurno, HorarioTurno, NumeroTurno, HoraInicio, HoraFin, EstadoTurno, CreatedDateTurno');
            $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
            $this->db->order_by('FechaTurno', 'DESC');
            $this->db->order_by('NumeroTurno', 'DESC');
            $this->db->limit(10);
            
            $query = $this->db->get('tb_turno');
            $turnos = $query->result_array();
            
            log_message('info', "mis_turnos - Turnos encontrados: " . count($turnos));
            
            echo json_encode(array(
                'success' => true,
                'turnos' => $turnos,
                'debug' => array(
                    'id_cliente' => $id_cliente,
                    'total' => count($turnos)
                )
            ));
        } catch (Exception $e) {
            log_message('error', 'Error en mis_turnos: ' . $e->getMessage());
            echo json_encode(array(
                'success' => false,
                'message' => 'Error al obtener turnos',
                'error' => $e->getMessage()
            ));
        }
    }
}
