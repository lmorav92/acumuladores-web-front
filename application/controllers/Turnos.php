<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turnos extends CI_Controller {

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
     * NUEVO: Obtener lista de barberos
     */
    public function lista_barberos() {
        header('Content-Type: application/json');
        
        try {
            $barberos = $this->Mturnos->get_barberos_activos();
            
            echo json_encode([
                'success' => true,
                'barberos' => $barberos
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en lista_barberos: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener barberos',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * NUEVO: Obtener lista de servicios
     */
    public function lista_servicios() {
        header('Content-Type: application/json');
        
        try {
            $servicios = $this->Mturnos->get_servicios_disponibles();
            
            echo json_encode([
                'success' => true,
                'servicios' => $servicios
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en lista_servicios: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener servicios',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Reservar turno - ACTUALIZADO con barbero y servicio
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
            $id_barbero = $this->input->post('id_barbero'); // NUEVO
            $id_servicio = $this->input->post('id_servicio'); // NUEVO
            
            // Validar datos requeridos
            if (!$id_cliente || !$fecha_turno || !$numero_turno || !$id_barbero || !$id_servicio) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos. Debe seleccionar cliente, barbero y servicio.'
                ]);
                return;
            }
            
            // Validar fecha no pasada
            $fecha_hoy = date('Y-m-d');
            if ($fecha_turno < $fecha_hoy) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pueden reservar turnos en fechas pasadas'
                ]);
                return;
            }
            
            // Verificar disponibilidad
            if ($this->Mturnos->turno_ocupado($fecha_turno, $numero_turno)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Este turno ya está reservado'
                ]);
                return;
            }
            
            // Datos del turno con barbero y servicio
            $turno_data = [
                'TB_CLIENTE_IdCliente' => $id_cliente,
                'FechaTurno' => $fecha_turno,
                'NumeroTurno' => $numero_turno,
                'HorarioTurno' => $horario_descripcion,
                'HoraInicio' => $hora_inicio,
                'HoraFin' => $hora_fin,
                'EstadoTurno' => 'reservado',
                'TB_BARBERO_IdBarbero' => $id_barbero, // NUEVO
                'TB_PELADO_IdPelado' => $id_servicio,  // NUEVO
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
     * Cancelar un turno
     */
    public function cancelar($id_turno) {
        header('Content-Type: application/json');
        
        try {
            if (!$id_turno) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID no proporcionado'
                ]);
                return;
            }
            
            $resultado = $this->Mturnos->cambiar_estado($id_turno, 'cancelado');
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Turno cancelado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cancelar turno'
                ]);
            }
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
     * Liberar un turno cancelado para volver a estar disponible
     */
    public function liberar($id_turno) {
        header('Content-Type: application/json');
        
        try {
            if (!$id_turno) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID no proporcionado'
                ]);
                return;
            }
            
            // Eliminar el turno para liberarlo
            $this->db->where('IdTurno', $id_turno);
            $resultado = $this->db->delete('tb_turno');
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Turno liberado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al liberar turno'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Error en liberar: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al liberar turno',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Debug del estado de la base de datos
     */
    public function debug_turnos() {
        header('Content-Type: application/json');
        
        try {
            // 1. Verificar conexión a BD
            $tests = [];
            $tests['db_connected'] = $this->db->conn_id ? true : false;
            
            // 2. Ver tablas
            $tests['tables'] = $this->db->list_tables();
            
            // 3. Ver estructura de tb_turno
            if (in_array('tb_turno', $tests['tables'])) {
                $tests['tb_turno_fields'] = $this->db->list_fields('tb_turno');
                
                // Contar turnos
                $tests['total_turnos'] = $this->db->count_all('tb_turno');
                
                // Ver un turno de ejemplo
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

	/**
 * Lista de turnos del usuario actual (para la vista mis_turnos)
 * CORREGIDO: Ahora busca por cliente, no por usuario creador
 */
public function mis_turnos_lista() {
    header('Content-Type: application/json');
    
    try {
        // Obtener id_cliente de la sesión
        $id_cliente = $this->session->userdata('id_cliente');
        
        log_message('info', '=== INICIO mis_turnos_lista ===');
        log_message('info', 'id_cliente: ' . var_export($id_cliente, true));
        
        // Si no está en sesión, buscar por user_id
        if (!$id_cliente) {
            $user_id = $this->session->userdata('user_id');
            log_message('info', 'id_cliente no en sesión, buscando por user_id: ' . $user_id);
            
            // Buscar id_cliente del usuario
            $this->db->select('TB_CLIENTE_IdCliente');
            $this->db->where('IdUsuario', $user_id);
            $query = $this->db->get('tb_usuario');
            
            if ($query->num_rows() > 0) {
                $usuario = $query->row();
                $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                // Guardar en sesión
                $this->session->set_userdata('id_cliente', $id_cliente);
                log_message('info', 'id_cliente encontrado: ' . $id_cliente);
            }
        }
        
        if (!$id_cliente) {
            log_message('error', 'No se pudo obtener id_cliente');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo identificar el cliente',
                'debug' => [
                    'session' => $this->session->userdata()
                ]
            ]);
            return;
        }
        
        $estado = $this->input->get('estado');
        $fecha_desde = $this->input->get('fecha_desde');
        $fecha_hasta = $this->input->get('fecha_hasta');
        
        // Usar el nuevo método que busca por cliente
        $turnos = $this->Mturnos->get_turnos_cliente($id_cliente, $estado, $fecha_desde, $fecha_hasta);
        
        log_message('info', 'Turnos encontrados: ' . count($turnos));
        log_message('info', '=== FIN mis_turnos_lista ===');
        
        echo json_encode([
            'success' => true,
            'turnos' => $turnos,
            'debug' => [
                'id_cliente' => $id_cliente,
                'total' => count($turnos)
            ]
        ]);
    } catch (Exception $e) {
        log_message('error', 'Error en mis_turnos_lista: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener turnos',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Lista de historial de turnos
 * CORREGIDO: Usuarios normales solo ven sus turnos
 */
public function historial_lista() {
    header('Content-Type: application/json');
    
    try {
        log_message('info', '=== INICIO historial_lista ===');
        
        // Verificar si es administrador
        $es_admin = $this->es_administrador();
        
        $periodo = $this->input->get('periodo');
        $fecha_desde = $this->input->get('fecha_desde');
        $fecha_hasta = $this->input->get('fecha_hasta');
        $estado = $this->input->get('estado');
        $cliente = $this->input->get('cliente');
        
        // Calcular fechas según período
        if ($periodo && $periodo !== 'personalizado') {
            list($fecha_desde, $fecha_hasta) = $this->calcular_periodo($periodo);
        }
        
        // Si NO es admin, forzar búsqueda solo por su cliente
        if (!$es_admin) {
            $id_cliente = $this->session->userdata('id_cliente');
            
            // Si no está en sesión, buscar
            if (!$id_cliente) {
                $user_id = $this->session->userdata('user_id');
                $this->db->select('TB_CLIENTE_IdCliente');
                $this->db->where('IdUsuario', $user_id);
                $query = $this->db->get('tb_usuario');
                
                if ($query->num_rows() > 0) {
                    $usuario = $query->row();
                    $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                    $this->session->set_userdata('id_cliente', $id_cliente);
                }
            }
            
            log_message('info', 'Usuario normal - Filtrando por id_cliente: ' . $id_cliente);
            
            // Obtener historial solo del cliente
            $turnos = $this->Mturnos->get_historial_cliente($id_cliente, $fecha_desde, $fecha_hasta, $estado);
        } else {
            log_message('info', 'Admin - Obteniendo historial completo');
            
            // Admin ve todo
            $turnos = $this->Mturnos->get_historial($fecha_desde, $fecha_hasta, $estado, $cliente);
        }
        
        log_message('info', 'Turnos en historial: ' . count($turnos));
        log_message('info', '=== FIN historial_lista ===');
        
        echo json_encode([
            'success' => true,
            'turnos' => $turnos,
            'debug' => [
                'es_admin' => $es_admin,
                'total' => count($turnos)
            ]
        ]);
    } catch (Exception $e) {
        log_message('error', 'Error en historial_lista: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener historial',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Calcular período de fechas
 */
private function calcular_periodo($periodo) {
    $fecha_fin = date('Y-m-d');
    
    switch ($periodo) {
        case 'mes_actual':
            $fecha_inicio = date('Y-m-01');
            break;
        case 'mes_anterior':
            $fecha_inicio = date('Y-m-01', strtotime('-1 month'));
            $fecha_fin = date('Y-m-t', strtotime('-1 month'));
            break;
        case 'trimestre':
            $fecha_inicio = date('Y-m-d', strtotime('-3 months'));
            break;
        case 'semestre':
            $fecha_inicio = date('Y-m-d', strtotime('-6 months'));
            break;
        case 'anio':
            $fecha_inicio = date('Y-01-01');
            break;
        default:
            $fecha_inicio = date('Y-m-01');
    }
    
    return [$fecha_inicio, $fecha_fin];
}

/**
 * MÉTODO CORREGIDO: Verificar si el usuario es administrador
 * IMPORTANTE: Usa 'role' (no 'rol') porque así está en la sesión
 */
private function es_administrador() {
    // CRÍTICO: Usar 'role' (inglés) no 'rol' (español)
    $rol = $this->session->userdata('role');
    
    // Log de debugging
    log_message('info', 'Verificando rol - Valor: ' . var_export($rol, true) . ', Tipo: ' . gettype($rol));
    
    // Si no hay rol, retornar false
    if (empty($rol)) {
        log_message('warning', 'Rol vacío o null');
        return false;
    }
    
    // Normalizar el rol
    $rol_normalizado = strtolower(trim($rol));
    
    // Verificar contra múltiples variantes
    $es_admin = in_array($rol_normalizado, ['administrador', 'admin', '1']);
    
    log_message('info', 'Es administrador: ' . ($es_admin ? 'SI' : 'NO'));
    
    return $es_admin;
}

/**
 * MÉTODO CORREGIDO: Obtener lista de clientes para el select
 */
public function lista_clientes() {
    header('Content-Type: application/json');
    
    try {
        // Obtener datos de sesión
        $user_id = $this->session->userdata('user_id');
        $rol = $this->session->userdata('role'); // CAMBIO: usar 'role' no 'rol'
        
        // DEBUG: Log completo de sesión
        log_message('info', '=== INICIO lista_clientes ===');
        log_message('info', 'Session completa: ' . json_encode($this->session->userdata()));
        log_message('info', 'User ID: ' . var_export($user_id, true));
        log_message('info', 'Role: ' . var_export($rol, true));
        log_message('info', 'Tipo de Role: ' . gettype($rol));
        
        // Verificar si hay sesión
        if (!$user_id) {
            log_message('error', 'No hay user_id en sesión');
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no válida',
                'debug' => [
                    'session_data' => $this->session->userdata()
                ]
            ]);
            return;
        }
        
        // Usar el método auxiliar para determinar si es admin
        $es_admin = $this->es_administrador();
        
        log_message('info', 'Resultado es_administrador(): ' . ($es_admin ? 'TRUE' : 'FALSE'));
        
        if ($es_admin) {
            // Admin: obtener TODOS los clientes activos
            log_message('info', 'Cargando TODOS los clientes (Admin)');
            $clientes = $this->Mturnos->get_clientes_activos();
            log_message('info', 'Admin - Total clientes cargados: ' . count($clientes));
        } else {
            // Usuario normal: obtener SOLO su cliente
            log_message('info', 'Cargando cliente del usuario (No Admin)');
            $clientes = $this->Mturnos->get_cliente_usuario($user_id);
            log_message('info', 'Usuario normal - Clientes cargados: ' . count($clientes));
        }
        
        log_message('info', '=== FIN lista_clientes ===');
        
        echo json_encode([
            'success' => true,
            'clientes' => $clientes,
            'es_admin' => $es_admin,
            'rol' => $rol,
            'debug' => [
                'user_id' => $user_id,
                'rol_original' => $rol,
                'rol_type' => gettype($rol),
                'es_admin_calculado' => $es_admin,
                'total_clientes' => count($clientes)
            ]
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
        
        // Usar el método auxiliar
        $es_admin = $this->es_administrador();
        $user_id = $this->session->userdata('user_id');
        
        if ($es_admin) {
            // Admin ve todos los turnos
            $turnos = $this->Mturnos->get_turnos_dia($fecha);
        } else {
            // Usuario normal ve solo sus turnos
            $turnos = $this->Mturnos->get_turnos_dia_usuario($fecha, $user_id);
        }
        
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
 * Obtener estadísticas del historial
 */
public function estadisticas() {
    header('Content-Type: application/json');
    
    try {
        log_message('info', '=== INICIO estadisticas ===');
        
        // Verificar si es administrador
        $es_admin = $this->es_administrador();
        
        $periodo = $this->input->get('periodo');
        $fecha_desde = $this->input->get('fecha_desde');
        $fecha_hasta = $this->input->get('fecha_hasta');
        
        // Calcular fechas según período
        if ($periodo && $periodo !== 'personalizado') {
            list($fecha_desde, $fecha_hasta) = $this->calcular_periodo($periodo);
        }
        
        // Si no hay fechas, usar el mes actual por defecto
        if (!$fecha_desde || !$fecha_hasta) {
            $fecha_desde = date('Y-m-01');
            $fecha_hasta = date('Y-m-t');
        }
        
        // Si NO es admin, estadísticas solo del cliente
        if (!$es_admin) {
            $id_cliente = $this->session->userdata('id_cliente');
            
            // Si no está en sesión, buscar
            if (!$id_cliente) {
                $user_id = $this->session->userdata('user_id');
                $this->db->select('TB_CLIENTE_IdCliente');
                $this->db->where('IdUsuario', $user_id);
                $query = $this->db->get('tb_usuario');
                
                if ($query->num_rows() > 0) {
                    $usuario = $query->row();
                    $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                }
            }
            
            // Obtener estadísticas del cliente
            $estadisticas = $this->Mturnos->get_estadisticas_cliente($id_cliente, $fecha_desde, $fecha_hasta);
        } else {
            // Admin ve estadísticas globales
            $estadisticas = $this->Mturnos->get_estadisticas_globales($fecha_desde, $fecha_hasta);
        }
        
        log_message('info', 'Estadísticas calculadas: ' . print_r($estadisticas, true));
        log_message('info', '=== FIN estadisticas ===');
        
        echo json_encode([
            'success' => true,
            'estadisticas' => $estadisticas
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
 * Exportar historial a Excel
 */
public function exportar_excel() {
    try {
        log_message('info', '=== INICIO exportar_excel ===');
        
        // Cargar librería Excel
        $this->load->library('excel');
        
        // Verificar si es administrador
        $es_admin = $this->es_administrador();
        
        // Obtener filtros
        $periodo = $this->input->get('periodo');
        $fecha_desde = $this->input->get('fecha_desde');
        $fecha_hasta = $this->input->get('fecha_hasta');
        $estado = $this->input->get('estado');
        $cliente = $this->input->get('cliente');
        
        // Calcular fechas según período
        if ($periodo && $periodo !== 'personalizado') {
            list($fecha_desde, $fecha_hasta) = $this->calcular_periodo($periodo);
        }
        
        // Obtener datos
        if (!$es_admin) {
            $id_cliente = $this->session->userdata('id_cliente');
            if (!$id_cliente) {
                $user_id = $this->session->userdata('user_id');
                $this->db->select('TB_CLIENTE_IdCliente');
                $this->db->where('IdUsuario', $user_id);
                $query = $this->db->get('tb_usuario');
                if ($query->num_rows() > 0) {
                    $usuario = $query->row();
                    $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                }
            }
            $turnos = $this->Mturnos->get_historial_cliente($id_cliente, $fecha_desde, $fecha_hasta, $estado);
        } else {
            $turnos = $this->Mturnos->get_historial($fecha_desde, $fecha_hasta, $estado, $cliente);
        }
        
        // Preparar datos para Excel
        $data = [
            ['#', 'Fecha', 'Hora', 'N° Turno', 'Cliente', 'Carnet', 'Estado', 'Duración']
        ];
        
        $index = 1;
        foreach ($turnos as $t) {
            $data[] = [
                $index++,
                $t['FechaTurno'],
                $t['HorarioTurno'] ?? 'N/A',
                $t['NumeroTurno'],
                $t['NombreCompleto'],
                $t['CarnetCliente'] ?? '',
                $t['EstadoDescripcion'] ?? $t['EstadoTurno'],
                $t['Duracion'] ?? 'N/A'
            ];
        }
        
        // Crear Excel
        $this->excel->create();
        $this->excel->setTitle('Historial de Turnos');
        
        // Agregar datos
        $this->excel->fromArray($data, 'A1');
        
        // Estilizar encabezados
        $this->excel->styleHeader('A1:H1');
        
        // Aplicar bordes
        $lastRow = count($data);
        $this->excel->setBorders('A1:H' . $lastRow);
        
        // Auto-ajustar columnas
        $this->excel->autoSizeColumns('A', 'H');
        
        // Generar nombre de archivo
        $filename = 'Historial_Turnos_' . date('Y-m-d_His') . '.xlsx';
        
        // Descargar
        $this->excel->download($filename);
        
        log_message('info', '=== FIN exportar_excel ===');
    } catch (Exception $e) {
        log_message('error', 'Error en exportar_excel: ' . $e->getMessage());
        show_error('Error al generar archivo Excel: ' . $e->getMessage());
    }
}

/**
 * Exportar historial a PDF
 */
public function exportar_pdf() {
    try {
        log_message('info', '=== INICIO exportar_pdf ===');
        
        // Cargar librería PDF
        $this->load->library('pdf');
        
        // Verificar si es administrador
        $es_admin = $this->es_administrador();
        
        // Obtener filtros
        $periodo = $this->input->get('periodo');
        $fecha_desde = $this->input->get('fecha_desde');
        $fecha_hasta = $this->input->get('fecha_hasta');
        $estado = $this->input->get('estado');
        $cliente = $this->input->get('cliente');
        
        // Calcular fechas según período
        if ($periodo && $periodo !== 'personalizado') {
            list($fecha_desde, $fecha_hasta) = $this->calcular_periodo($periodo);
        }
        
        // Obtener datos
        if (!$es_admin) {
            $id_cliente = $this->session->userdata('id_cliente');
            if (!$id_cliente) {
                $user_id = $this->session->userdata('user_id');
                $this->db->select('TB_CLIENTE_IdCliente');
                $this->db->where('IdUsuario', $user_id);
                $query = $this->db->get('tb_usuario');
                if ($query->num_rows() > 0) {
                    $usuario = $query->row();
                    $id_cliente = $usuario->TB_CLIENTE_IdCliente;
                }
            }
            $turnos = $this->Mturnos->get_historial_cliente($id_cliente, $fecha_desde, $fecha_hasta, $estado);
        } else {
            $turnos = $this->Mturnos->get_historial($fecha_desde, $fecha_hasta, $estado, $cliente);
        }
        
        // Configurar PDF
        $this->pdf->AddPage();
        
        // Título
        $this->pdf->addSectionTitle('Historial de Turnos');
        
        // Información del reporte
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->Cell(0, 5, 'Período: ' . ($fecha_desde ?? 'N/A') . ' - ' . ($fecha_hasta ?? 'N/A'), 0, 1);
        $this->pdf->Cell(0, 5, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1);
        $this->pdf->Ln(5);
        
        // Preparar datos para tabla
        $headers = ['#', 'Fecha', 'Turno', 'Cliente', 'Estado'];
        $data = [];
        
        $index = 1;
        foreach ($turnos as $t) {
            $data[] = [
                $index++,
                $t['FechaTurno'],
                '#' . $t['NumeroTurno'],
                $t['NombreCompleto'],
                $t['EstadoDescripcion'] ?? $t['EstadoTurno']
            ];
        }
        
        // Crear tabla
        $widths = [10, 30, 20, 80, 40]; // Anchos personalizados
        $this->pdf->createTable($headers, $data, $widths);
        
        // Resumen
        $this->pdf->Ln(10);
        $this->pdf->addSectionTitle('Resumen');
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(0, 6, 'Total de registros: ' . count($turnos), 0, 1);
        
        // Generar nombre de archivo
        $filename = 'Historial_Turnos_' . date('Y-m-d_His') . '.pdf';
        
        // Descargar
        $this->pdf->download($filename);
        
        log_message('info', '=== FIN exportar_pdf ===');
    } catch (Exception $e) {
        log_message('error', 'Error en exportar_pdf: ' . $e->getMessage());
        show_error('Error al generar archivo PDF: ' . $e->getMessage());
    }
}
	
}
