<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendario extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mturnos');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    /**
     * Vista principal de gestión de turnos
     */
    public function index() {
        $this->load->view('turnos');
    }

    /**
     * Obtener resumen de turnos por mes para el calendario
     */
    public function resumen_mes() {
        header('Content-Type: application/json');
        
        try {
            $fecha_inicio = $this->input->get('fecha_inicio');
            $fecha_fin = $this->input->get('fecha_fin');
            
            if (!$fecha_inicio || !$fecha_fin) {
                echo json_encode(['success' => false, 'message' => 'Fechas no proporcionadas']);
                return;
            }
            
            $resumen = $this->Mturnos->get_resumen_mes($fecha_inicio, $fecha_fin);
            
            echo json_encode([
                'success' => true,
                'dias' => $resumen
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en resumen_mes: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener resumen',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener turnos disponibles de un día específico
     */
    public function turnos_dia() {
        header('Content-Type: application/json');
        
        try {
            $fecha = $this->input->get('fecha');
            
            if (!$fecha) {
                echo json_encode(['success' => false, 'message' => 'Fecha no proporcionada']);
                return;
            }
            
            $turnos = $this->Mturnos->get_turnos_dia($fecha);
            
            echo json_encode([
                'success' => true,
                'turnos' => $turnos
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en turnos_dia: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener turnos',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Reservar un turno
     */
    public function reservar() {
        header('Content-Type: application/json');
        
        try {
            $id_cliente = $this->input->post('id_cliente');
            $fecha_turno = $this->input->post('fecha_turno');
            $numero_turno = $this->input->post('numero_turno');
            $hora_inicio = $this->input->post('hora_inicio');
            $hora_fin = $this->input->post('hora_fin');
            $horario_descripcion = $this->input->post('horario_descripcion');
            
            // Validar datos requeridos
            if (!$id_cliente || !$fecha_turno || !$numero_turno) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
                return;
            }
            
            // Verificar que el turno no esté ocupado
            if ($this->Mturnos->turno_ocupado($fecha_turno, $numero_turno)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Este turno ya está reservado'
                ]);
                return;
            }
            
            // Datos del turno
            $turno_data = [
                'TB_CLIENTE_IdCliente' => $id_cliente,
                'FechaTurno' => $fecha_turno,
                'NumeroTurno' => $numero_turno,
                'HorarioTurno' => $horario_descripcion,
                'HoraInicio' => $hora_inicio,
                'HoraFin' => $hora_fin,
                'EstadoTurno' => 'reservado',
                'CreatedDateTurno' => date('Y-m-d'),
                'CreatedUserTurno' => $this->session->userdata('user_id')
            ];
            
            $resultado = $this->Mturnos->crear_turno($turno_data);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Turno reservado exitosamente'
                ]);
            } else {
                $error = $this->db->error();
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al reservar turno: ' . $error['message']
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Error en reservar: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al reservar turno',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalles de un turno - CON DEBUGGING MEJORADO
     */
    public function detalle($id_turno) {
        header('Content-Type: application/json');
        
        try {
            log_message('info', 'Solicitando detalle del turno: ' . $id_turno);
            
            if (!$id_turno) {
                log_message('error', 'ID de turno no proporcionado');
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }
            
            // Validar que sea numérico
            if (!is_numeric($id_turno)) {
                log_message('error', 'ID de turno no es numérico: ' . $id_turno);
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                return;
            }
            
            $turno = $this->Mturnos->get_turno_detalle($id_turno);
            
            if ($turno) {
                log_message('info', 'Turno encontrado: ' . json_encode($turno));
                echo json_encode([
                    'success' => true,
                    'turno' => $turno
                ]);
            } else {
                log_message('error', 'Turno no encontrado con ID: ' . $id_turno);
                
                // Verificar error de base de datos
                $db_error = $this->db->error();
                if ($db_error['code'] != 0) {
                    log_message('error', 'Error de DB: ' . $db_error['message']);
                }
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Turno no encontrado'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Error en detalle: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalles',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cambiar estado de un turno
     */
    public function cambiar_estado() {
        header('Content-Type: application/json');
        
        try {
            $id_turno = $this->input->post('id_turno');
            $nuevo_estado = $this->input->post('nuevo_estado');
            
            if (!$id_turno || !$nuevo_estado) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);
                return;
            }
            
            // Validar estado
            $estados_validos = ['reservado', 'en_espera', 'atendiendo', 'finalizado', 'cancelado'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
                return;
            }
            
            $resultado = $this->Mturnos->cambiar_estado($id_turno, $nuevo_estado);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Estado actualizado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cambiar estado'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Error en cambiar_estado: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cambiar estado',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener lista de clientes para el select
     */
    public function lista_clientes() {
        header('Content-Type: application/json');
        
        try {
            $clientes = $this->Mturnos->get_clientes_activos();
            
            echo json_encode([
                'success' => true,
                'clientes' => $clientes
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en lista_clientes: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cancelar un turno
     */
    public function cancelar($id_turno) {
        header('Content-Type: application/json');
        
        try {
            if (!$id_turno) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }
            
            $resultado = $this->Mturnos->cambiar_estado($id_turno, 'cancelado');
            
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Turno cancelado' : 'Error al cancelar'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en cancelar: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cancelar turno',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar un turno (solo administradores)
     */
    public function eliminar($id_turno) {
        header('Content-Type: application/json');
        
        try {
            // Verificar permisos de administrador
            if ($this->session->userdata('user_role') !== 'Administrador') {
                echo json_encode([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar turnos'
                ]);
                return;
            }
            
            if (!$id_turno) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }
            
            $resultado = $this->Mturnos->eliminar_turno($id_turno);
            
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Turno eliminado' : 'Error al eliminar'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en eliminar: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar turno',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar estados automáticamente
     */
    public function actualizar_estados() {
        header('Content-Type: application/json');
        
        try {
            $resultado = $this->Mturnos->actualizar_estados_automaticos();
            
            echo json_encode([
                'success' => true,
                'message' => 'Estados actualizados',
                'actualizados' => $resultado
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en actualizar_estados: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar estados',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener turnos de un cliente específico
     */
    public function turnos_cliente($id_cliente) {
        header('Content-Type: application/json');
        
        try {
            if (!$id_cliente) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                return;
            }
            
            $turnos = $this->Mturnos->get_turnos_cliente($id_cliente);
            
            echo json_encode([
                'success' => true,
                'turnos' => $turnos
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en turnos_cliente: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener turnos del cliente',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de turnos
     */
    public function estadisticas() {
        header('Content-Type: application/json');
        
        try {
            $fecha_inicio = $this->input->get('fecha_inicio');
            $fecha_fin = $this->input->get('fecha_fin');
            
            if (!$fecha_inicio) $fecha_inicio = date('Y-m-01');
            if (!$fecha_fin) $fecha_fin = date('Y-m-t');
            
            $stats = $this->Mturnos->get_estadisticas($fecha_inicio, $fecha_fin);
            
            echo json_encode([
                'success' => true,
                'estadisticas' => $stats
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en estadisticas: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint de test para debugging
     */
    public function test() {
        header('Content-Type: application/json');
        
        try {
            // Test básico
            $tests = [
                'db_connected' => $this->db->conn_id ? true : false,
                'model_loaded' => class_exists('Mturnos'),
                'tabla_turnos' => $this->db->table_exists('tb_turno'),
                'tabla_clientes' => $this->db->table_exists('tb_cliente'),
                'count_turnos' => 0,
                'metodo_detalle_existe' => method_exists($this->Mturnos, 'get_turno_detalle')
            ];
            
            if ($tests['tabla_turnos']) {
                $tests['count_turnos'] = $this->db->count_all('tb_turno');
                
                // Obtener un turno de ejemplo
                $this->db->limit(1);
                $turno_ejemplo = $this->db->get('tb_turno')->row_array();
                $tests['turno_ejemplo'] = $turno_ejemplo;
                
                // Probar el método detalle si hay turnos
                if ($turno_ejemplo) {
                    $tests['test_detalle'] = $this->Mturnos->get_turno_detalle($turno_ejemplo['IdTurno']);
                }
            }
            
            echo json_encode([
                'success' => true,
                'tests' => $tests
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
