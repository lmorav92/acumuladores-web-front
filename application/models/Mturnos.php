<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mturnos extends CI_Model {


	/**
     * NUEVO: Obtener barberos activos
     */
    public function get_barberos_activos() {
        try {
            $this->db->select('
                IdBarbero,
                CONCAT(TRIM(COALESCE(NombreBarbero, "")), " ", TRIM(COALESCE(ApellidosBarbero, ""))) AS NombreCompleto,
                Especialidad,
                FotoUrl
            ');
            $this->db->from('tb_barbero');
            $this->db->where('EstadoBarbero', 'Activo');
            $this->db->order_by('NombreBarbero', 'ASC');
            
            $query = $this->db->get();
            $resultado = $query->result_array();
            
            log_message('info', 'get_barberos_activos - Total encontrados: ' . count($resultado));
            
            return $resultado;
        } catch (Exception $e) {
            log_message('error', 'Error en get_barberos_activos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * NUEVO: Obtener servicios/pelados disponibles
     */
    public function get_servicios_disponibles() {
        try {
            $this->db->select('
                p.IdPelado,
                p.NombrePelado,
                p.DescripcionPelado,
                p.PrecioPelado,
                p.UrlFoto,
                c.NombrePelado as CategoriaNombre
            ');
            $this->db->from('TB_PELADO p');
            $this->db->join('TB_CATEGORIA_PELADO c', 'p.TB_CATEGORIA_PELADO_IdCategoriaPelado = c.IdCategoriaPelado', 'left');
            $this->db->order_by('p.NombrePelado', 'ASC');
            
            $query = $this->db->get();
            $resultado = $query->result_array();
            
            log_message('info', 'get_servicios_disponibles - Total encontrados: ' . count($resultado));
            
            return $resultado;
        } catch (Exception $e) {
            log_message('error', 'Error en get_servicios_disponibles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener resumen de turnos por día en un rango de fechas
     * Para mostrar en el calendario mensual
     */
    public function get_resumen_mes($fecha_inicio, $fecha_fin) {
        try {
            // Generar todos los días del rango
            $start = new DateTime($fecha_inicio);
            $end = new DateTime($fecha_fin);
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($start, $interval, $end->modify('+1 day'));
            
            $resultado = [];
            
            foreach ($daterange as $date) {
                $fecha = $date->format('Y-m-d');
                
                // Contar turnos reservados
                $this->db->where('FechaTurno', $fecha);
                $this->db->where_in('EstadoTurno', ['reservado', 'en_espera', 'atendiendo']);
                $reservados = $this->db->count_all_results('tb_turno');
                
                $disponibles = 8 - $reservados; // 8 turnos por día
                
                $resultado[] = [
                    'Fecha' => $fecha,
                    'Reservados' => $reservados,
                    'Disponibles' => $disponibles >= 0 ? $disponibles : 0
                ];
            }
            
            return $resultado;
        } catch (Exception $e) {
            log_message('error', 'Error en get_resumen_mes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener los 8 turnos de un día con su disponibilidad
     */
    public function get_turnos_dia($fecha) {
        try {
            // Intentar usar el procedimiento almacenado
            if ($this->db->table_exists('tb_horarios_disponibles')) {
                $sql = "CALL sp_turnos_disponibles_dia(?)";
                $query = $this->db->query($sql, [$fecha]);

                if ($query === false) {
                    log_message('error', 'Error al ejecutar sp_turnos_disponibles_dia');
                    // Fallback al método alternativo
                    return $this->get_turnos_dia_alternativo($fecha);
                }

                $resultado = $query->result_array();

                // Limpieza obligatoria para CI3 + MySQL
                $query->free_result();

                // Limpia buffers internos del driver MySQL
                if (method_exists($this->db->conn_id, 'more_results')) {
                    while ($this->db->conn_id->more_results()) {
                        $this->db->conn_id->next_result();
                        if ($this->db->conn_id->store_result()) {
                            $this->db->conn_id->free_result();
                        }
                    }
                }

                return $resultado;
            } else {
                // Si no existe la tabla de horarios, usar método alternativo
                return $this->get_turnos_dia_alternativo($fecha);
            }
        } catch (Exception $e) {
            log_message('error', 'Error en get_turnos_dia: ' . $e->getMessage());
            return $this->get_turnos_dia_alternativo($fecha);
        }
    }

    /**
     * Método alternativo sin procedimiento almacenado
     */
    private function get_turnos_dia_alternativo($fecha) {
        $horarios = [
            1 => ['desc' => 'Turno 1 (8:00 AM - 9:00 AM)', 'inicio' => '08:00:00', 'fin' => '09:00:00'],
            2 => ['desc' => 'Turno 2 (9:00 AM - 10:00 AM)', 'inicio' => '09:00:00', 'fin' => '10:00:00'],
            3 => ['desc' => 'Turno 3 (10:00 AM - 11:00 AM)', 'inicio' => '10:00:00', 'fin' => '11:00:00'],
            4 => ['desc' => 'Turno 4 (11:00 AM - 12:00 PM)', 'inicio' => '11:00:00', 'fin' => '12:00:00'],
            5 => ['desc' => 'Turno 5 (1:00 PM - 2:00 PM)', 'inicio' => '13:00:00', 'fin' => '14:00:00'],
            6 => ['desc' => 'Turno 6 (2:00 PM - 3:00 PM)', 'inicio' => '14:00:00', 'fin' => '15:00:00'],
            7 => ['desc' => 'Turno 7 (3:00 PM - 4:00 PM)', 'inicio' => '15:00:00', 'fin' => '16:00:00'],
            8 => ['desc' => 'Turno 8 (4:00 PM - 5:00 PM)', 'inicio' => '16:00:00', 'fin' => '17:00:00']
        ];
        
        $resultado = [];
        
        foreach ($horarios as $numero => $horario) {
            // Verificar si el turno está ocupado
            $this->db->select('
                t.IdTurno,
                t.EstadoTurno,
                CONCAT(COALESCE(c.NombreCliente, ""), " ", COALESCE(c.ApellidosCliente, "")) as Cliente
            ');
            $this->db->from('tb_turno t');
            $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
            $this->db->where('t.FechaTurno', $fecha);
            $this->db->where('t.NumeroTurno', $numero);
            $this->db->where_not_in('t.EstadoTurno', ['cancelado']);
            $query = $this->db->get();
            
            $turno = $query->row_array();
            
            $resultado[] = [
                'NumeroTurno' => $numero,
                'HorarioDescripcion' => $horario['desc'],
                'HoraInicio' => $horario['inicio'],
                'HoraFin' => $horario['fin'],
                'IdTurno' => $turno ? $turno['IdTurno'] : 0,
                'Estado' => $turno ? $turno['EstadoTurno'] : 'disponible',
                'IdCliente' => 0,
                'Cliente' => $turno ? trim($turno['Cliente']) : '',
                'Disponible' => $turno ? 0 : 1
            ];
        }
        
        return $resultado;
    }

    /**
     * Verificar si un turno está ocupado
     */
    public function turno_ocupado($fecha, $numero_turno) {
        try {
            $this->db->where('FechaTurno', $fecha);
            $this->db->where('NumeroTurno', $numero_turno);
            $this->db->where_not_in('EstadoTurno', ['cancelado']);
            $count = $this->db->count_all_results('tb_turno');
            
            return $count > 0;
        } catch (Exception $e) {
            log_message('error', 'Error en turno_ocupado: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear un nuevo turno
     */
    public function crear_turno($turno_data) {
        try {
            return $this->db->insert('tb_turno', $turno_data);
        } catch (Exception $e) {
            log_message('error', 'Error en crear_turno: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener detalles completos de un turno - VERSIÓN CORREGIDA
     */
public function get_turno_detalle($id_turno) {
    try {
        // Asegurarse de que sea un entero válido
        $id_turno = intval($id_turno);
        if ($id_turno <= 0) {
            log_message('error', 'ID de turno inválido: ' . $id_turno);
            return null;
        }

        // Construir CASE para EstadoTurno sin que CI lo escape
        $case_sql = "
            CASE 
                WHEN t.EstadoTurno = 'reservado' THEN 'Reservado'
                WHEN t.EstadoTurno = 'en_espera' THEN 'En Espera'
                WHEN t.EstadoTurno = 'atendiendo' THEN 'Atendiendo'
                WHEN t.EstadoTurno = 'finalizado' THEN 'Finalizado'
                WHEN t.EstadoTurno = 'cancelado' THEN 'Cancelado'
                ELSE 'Desconocido'
            END AS EstadoDescripcion
        ";

        // Selección de columnas
        $this->db->select('
            t.IdTurno,
            t.TB_CLIENTE_IdCliente,
            t.FechaTurno,
            t.NumeroTurno,
            t.HorarioTurno,
            t.HoraInicio,
            t.HoraFin,
            t.EstadoTurno,
            t.CreatedDateTurno,
            t.CreatedUserTurno,
            COALESCE(c.NombreCliente, "") AS NombreCliente,
            COALESCE(c.ApellidosCliente, "") AS ApellidosCliente,
            CONCAT(COALESCE(c.NombreCliente, ""), " ", COALESCE(c.ApellidosCliente, "")) AS NombreCompleto,
            COALESCE(c.CarnetCliente, "") AS CarnetCliente,
            COALESCE(c.Telefono, "") AS Telefono,
            ' . $case_sql . '
        ', FALSE);

        $this->db->from('tb_turno t');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->where('t.IdTurno', $id_turno);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            log_message('info', 'Turno encontrado en get_turno_detalle: ' . json_encode($result));
            return $result;
        }

        log_message('warning', 'No se encontró turno con ID: ' . $id_turno);
        return null;

    } catch (Exception $e) {
        log_message('error', 'Error en get_turno_detalle: ' . $e->getMessage());
        return null;
    }
}

    /**
     * Cambiar estado de un turno
     */
    public function cambiar_estado($id_turno, $nuevo_estado) {
        try {
            $this->db->where('IdTurno', $id_turno);
            $this->db->update('tb_turno', ['EstadoTurno' => $nuevo_estado]);
            
            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error en cambiar_estado: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * NUEVO MÉTODO: Obtener todos los clientes activos (para administradores)
     */
    public function get_clientes_activos() {
        try {
            $this->db->select('
                c.IdCliente,
                CONCAT(TRIM(COALESCE(c.NombreCliente, "")), " ", TRIM(COALESCE(c.ApellidosCliente, ""))) AS NombreCompleto,
                c.CarnetCliente,
                u.UserRol
            ');
            $this->db->from('tb_cliente c');
            $this->db->join('tb_usuario u', 'c.IdCliente = u.TB_CLIENTE_IdCliente', 'left');
            $this->db->where('u.UserEstado', 'Activo');
            $this->db->order_by('c.NombreCliente', 'ASC');
            
            $query = $this->db->get();
            $resultado = $query->result_array();
            
            log_message('info', 'get_clientes_activos - Total encontrados: ' . count($resultado));
            
            return $resultado;
        } catch (Exception $e) {
            log_message('error', 'Error en get_clientes_activos: ' . $e->getMessage());
            return [];
        }
    }

/**
 * Obtener turnos del usuario (con filtros)
 */
public function get_turnos_usuario($user_id, $estado = null, $fecha_desde = null, $fecha_hasta = null) {
    $this->db->select('
        t.IdTurno,
        t.FechaTurno,
        t.NumeroTurno,
        t.HorarioTurno,
        t.EstadoTurno,
        t.HoraInicio,
        t.HoraFin,
        CONCAT(TRIM(COALESCE(c.NombreCliente, "")), " ", TRIM(COALESCE(c.ApellidosCliente, ""))) AS NombreCompleto,
        c.CarnetCliente,
        CASE 
            WHEN t.EstadoTurno = "reservado" THEN "Reservado"
            WHEN t.EstadoTurno = "en_espera" THEN "En Espera"
            WHEN t.EstadoTurno = "atendiendo" THEN "Atendiendo"
            WHEN t.EstadoTurno = "finalizado" THEN "Finalizado"
            WHEN t.EstadoTurno = "cancelado" THEN "Cancelado"
        END as EstadoDescripcion
    ');
    $this->db->from('tb_turno t');
    $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente');
    $this->db->where('t.CreatedUserTurno', $user_id);
    
    if ($estado) {
        $this->db->where('t.EstadoTurno', $estado);
    }
    
    if ($fecha_desde) {
        $this->db->where('t.FechaTurno >=', $fecha_desde);
    }
    
    if ($fecha_hasta) {
        $this->db->where('t.FechaTurno <=', $fecha_hasta);
    }
    
    $this->db->order_by('t.FechaTurno', 'DESC');
    $this->db->order_by('t.NumeroTurno', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * NUEVO: Obtener turnos por ID de cliente (no por usuario creador)
 * Este es el método correcto para "Mis Turnos" de usuarios normales
 */
 public function get_turnos_cliente($id_cliente, $estado = null, $fecha_desde = null, $fecha_hasta = null) {
        // Construir query manualmente para evitar problemas con CASE
        $sql = "SELECT 
            t.*,
            c.NombreCliente,
            c.ApellidosCliente,
            CONCAT(c.NombreCliente, ' ', c.ApellidosCliente) as NombreCompleto,
            c.CarnetCliente,
            CASE 
                WHEN t.EstadoTurno = 'reservado' THEN 'Reservado'
                WHEN t.EstadoTurno = 'en_espera' THEN 'En Espera'
                WHEN t.EstadoTurno = 'atendiendo' THEN 'Atendiendo'
                WHEN t.EstadoTurno = 'finalizado' THEN 'Finalizado'
                WHEN t.EstadoTurno = 'cancelado' THEN 'Cancelado'
                ELSE 'Desconocido'
            END as EstadoDescripcion
        FROM tb_turno t
        JOIN tb_cliente c ON t.TB_CLIENTE_IdCliente = c.IdCliente
        WHERE t.TB_CLIENTE_IdCliente = ?";
        
        $params = [$id_cliente];
        
        // Filtro por estado
        if ($estado && $estado != '') {
            $sql .= " AND t.EstadoTurno = ?";
            $params[] = $estado;
        }
        
        // Filtro por fechas
        if ($fecha_desde && $fecha_desde != '') {
            $sql .= " AND t.FechaTurno >= ?";
            $params[] = $fecha_desde;
        }
        if ($fecha_hasta && $fecha_hasta != '') {
            $sql .= " AND t.FechaTurno <= ?";
            $params[] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY t.FechaTurno DESC, t.NumeroTurno ASC";
        
        $query = $this->db->query($sql, $params);
        
        log_message('info', 'Query turnos cliente ejecutado');
        log_message('info', 'Turnos encontrados: ' . $query->num_rows());
        
        return $query->result_array();
    }

/**
 * Obtener historial de turnos
 */
public function get_historial($fecha_desde = null, $fecha_hasta = null, $estado = null, $cliente = null) {
    // Usar FALSE como segundo parámetro para evitar escape de SQL personalizado
    $this->db->select('
        t.IdTurno,
        t.FechaTurno,
        t.NumeroTurno,
        t.HorarioTurno,
        t.EstadoTurno,
        t.HoraInicio,
        t.HoraFin,
        CONCAT(TRIM(COALESCE(c.NombreCliente, "")), " ", TRIM(COALESCE(c.ApellidosCliente, ""))) AS NombreCompleto,
        c.CarnetCliente,
        CASE 
            WHEN t.EstadoTurno = "finalizado" THEN "Finalizado"
            WHEN t.EstadoTurno = "cancelado" THEN "Cancelado"
            ELSE t.EstadoTurno
        END as EstadoDescripcion,
        TIMEDIFF(t.HoraFin, t.HoraInicio) as Duracion
    ', FALSE); // ← IMPORTANTE: FALSE para no escapar
    $this->db->from('tb_turno t');
    $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente');
    $this->db->where_in('t.EstadoTurno', ['finalizado', 'cancelado']);
    
    if ($fecha_desde) {
        $this->db->where('t.FechaTurno >=', $fecha_desde);
    }
    
    if ($fecha_hasta) {
        $this->db->where('t.FechaTurno <=', $fecha_hasta);
    }
    
    if ($estado) {
        $this->db->where('t.EstadoTurno', $estado);
    }
    
    if ($cliente) {
        // Buscar en nombre o apellido
        $this->db->group_start();
        $this->db->like('c.NombreCliente', $cliente);
        $this->db->or_like('c.ApellidosCliente', $cliente);
        $this->db->group_end();
    }
    
    $this->db->order_by('t.FechaTurno', 'DESC');
    $this->db->order_by('t.NumeroTurno', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}

/**
 * NUEVO: Obtener historial de un cliente específico
 */
public function get_historial_cliente($id_cliente, $fecha_desde = null, $fecha_hasta = null, $estado = null) {
    try {
        log_message('info', "get_historial_cliente - Cliente: $id_cliente");
        
        // Usar FALSE como segundo parámetro para evitar escape de SQL personalizado
        $this->db->select('
            t.IdTurno,
            t.FechaTurno,
            t.NumeroTurno,
            t.HorarioTurno,
            t.EstadoTurno,
            t.HoraInicio,
            t.HoraFin,
            CONCAT(TRIM(COALESCE(c.NombreCliente, "")), " ", TRIM(COALESCE(c.ApellidosCliente, ""))) AS NombreCompleto,
            c.CarnetCliente,
            CASE 
                WHEN t.EstadoTurno = "finalizado" THEN "Finalizado"
                WHEN t.EstadoTurno = "cancelado" THEN "Cancelado"
                ELSE t.EstadoTurno
            END as EstadoDescripcion,
            TIMEDIFF(t.HoraFin, t.HoraInicio) as Duracion
        ', FALSE); // ← IMPORTANTE: FALSE para no escapar
        $this->db->from('tb_turno t');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente');
        $this->db->where('t.TB_CLIENTE_IdCliente', $id_cliente); // ← Por CLIENTE
        $this->db->where_in('t.EstadoTurno', ['finalizado', 'cancelado']);
        
        if ($fecha_desde) {
            $this->db->where('t.FechaTurno >=', $fecha_desde);
        }
        
        if ($fecha_hasta) {
            $this->db->where('t.FechaTurno <=', $fecha_hasta);
        }
        
        if ($estado) {
            $this->db->where('t.EstadoTurno', $estado);
        }
        
        $this->db->order_by('t.FechaTurno', 'DESC');
        $this->db->order_by('t.NumeroTurno', 'DESC');
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        log_message('info', "get_historial_cliente - Encontrados: " . count($result));
        
        return $result;
    } catch (Exception $e) {
        log_message('error', 'Error en get_historial_cliente: ' . $e->getMessage());
        return [];
    }
}
/**
 * Actualizar estados de turnos automáticamente
 */
public function actualizar_estados_automaticos() {
    try {
        $fecha_actual = date('Y-m-d');
        $hora_actual = date('H:i:s');
        $actualizados = 0;
        
        // 1. Cambiar de 'reservado' a 'en_espera' cuando llega el día
        $this->db->where('FechaTurno', $fecha_actual);
        $this->db->where('EstadoTurno', 'reservado');
        $this->db->update('tb_turno', ['EstadoTurno' => 'en_espera']);
        $actualizados += $this->db->affected_rows();
        
        // 2. Cambiar de 'en_espera' a 'atendiendo' cuando llega la hora
        $this->db->where('FechaTurno', $fecha_actual);
        $this->db->where('EstadoTurno', 'en_espera');
        $this->db->where('HoraInicio <=', $hora_actual);
        $this->db->where('HoraFin >=', $hora_actual);
        $this->db->update('tb_turno', ['EstadoTurno' => 'atendiendo']);
        $actualizados += $this->db->affected_rows();
        
        // 3. NUEVO: Cambiar de 'atendiendo' a 'finalizado' cuando pasa la hora de fin
        $this->db->where('FechaTurno', $fecha_actual);
        $this->db->where('EstadoTurno', 'atendiendo');
        $this->db->where('HoraFin <', $hora_actual);
        $this->db->update('tb_turno', ['EstadoTurno' => 'finalizado']);
        $actualizados += $this->db->affected_rows();
        
        // 4. NUEVO: Finalizar turnos de días anteriores que quedaron en 'en_espera' o 'atendiendo'
        $this->db->where('FechaTurno <', $fecha_actual);
        $this->db->where_in('EstadoTurno', ['en_espera', 'atendiendo', 'reservado']);
        $this->db->update('tb_turno', ['EstadoTurno' => 'finalizado']);
        $actualizados += $this->db->affected_rows();
        
        return $actualizados;
    } catch (Exception $e) {
        log_message('error', 'Error en actualizar_estados_automaticos: ' . $e->getMessage());
        return 0;
    }
}
 /**
 * Obtener el cliente asociado a un usuario
 */
public function get_cliente_usuario($user_id) {
    try {
        $this->db->select('
            c.IdCliente,
            CONCAT(TRIM(COALESCE(c.NombreCliente, "")), " ", TRIM(COALESCE(c.ApellidosCliente, ""))) AS NombreCompleto,
            c.CarnetCliente,
			u.UserRol
        ');
        $this->db->from('tb_cliente c');
        $this->db->join('tb_usuario u', 'c.IdCliente = u.TB_CLIENTE_IdCliente');
        $this->db->where('u.IdUsuario', $user_id);
        $this->db->where('u.UserEstado', 'Activo');
        
        $query = $this->db->get();
        $resultado = $query->result_array();
        
        log_message('info', 'get_cliente_usuario - Usuario: ' . $user_id . ', Encontrados: ' . count($resultado));
        
        return $resultado;
    } catch (Exception $e) {
        log_message('error', 'Error en get_cliente_usuario: ' . $e->getMessage());
        return [];
    }
}
/**
 * Obtener turnos de un día filtrados por usuario
 */
/**
 * Obtener turnos del día para un usuario específico
 * CORREGIDO: Ahora filtra por cliente, no por usuario creador
 */
public function get_turnos_dia_usuario($fecha, $user_id) {
    try {
        // PASO 1: Obtener el ID del cliente del usuario
        $this->db->select('TB_CLIENTE_IdCliente');
        $this->db->where('IdUsuario', $user_id);
        $usuario = $this->db->get('tb_usuario')->row();
        
        if (!$usuario) {
            log_message('error', 'Usuario no encontrado: ' . $user_id);
            return [];
        }
        
        $id_cliente = $usuario->TB_CLIENTE_IdCliente;
        log_message('info', 'get_turnos_dia_usuario - Usuario: ' . $user_id . ', Cliente: ' . $id_cliente);
        
        // PASO 2: Definir horarios disponibles
        $horarios = [
            1 => ['desc' => 'Turno 1 (8:00 AM - 9:00 AM)', 'inicio' => '08:00:00', 'fin' => '09:00:00'],
            2 => ['desc' => 'Turno 2 (9:00 AM - 10:00 AM)', 'inicio' => '09:00:00', 'fin' => '10:00:00'],
            3 => ['desc' => 'Turno 3 (10:00 AM - 11:00 AM)', 'inicio' => '10:00:00', 'fin' => '11:00:00'],
            4 => ['desc' => 'Turno 4 (11:00 AM - 12:00 PM)', 'inicio' => '11:00:00', 'fin' => '12:00:00'],
            5 => ['desc' => 'Turno 5 (1:00 PM - 2:00 PM)', 'inicio' => '13:00:00', 'fin' => '14:00:00'],
            6 => ['desc' => 'Turno 6 (2:00 PM - 3:00 PM)', 'inicio' => '14:00:00', 'fin' => '15:00:00'],
            7 => ['desc' => 'Turno 7 (3:00 PM - 4:00 PM)', 'inicio' => '15:00:00', 'fin' => '16:00:00'],
            8 => ['desc' => 'Turno 8 (4:00 PM - 5:00 PM)', 'inicio' => '16:00:00', 'fin' => '17:00:00']
        ];
        
        $resultado = [];
        
        // PASO 3: Para cada horario, verificar disponibilidad
        foreach ($horarios as $numero => $horario) {
            // Buscar si existe turno para este horario en esta fecha
            $sql = "SELECT 
                t.IdTurno,
                t.TB_CLIENTE_IdCliente,
                t.EstadoTurno,
                CONCAT(COALESCE(c.NombreCliente, ''), ' ', COALESCE(c.ApellidosCliente, '')) as Cliente
            FROM tb_turno t
            LEFT JOIN tb_cliente c ON t.TB_CLIENTE_IdCliente = c.IdCliente
            WHERE t.FechaTurno = ?
            AND t.NumeroTurno = ?
            AND t.EstadoTurno NOT IN ('cancelado')
            LIMIT 1";
            
            $turno = $this->db->query($sql, [$fecha, $numero])->row_array();
            
            if ($turno) {
                // Hay un turno reservado
                $es_mio = ($turno['TB_CLIENTE_IdCliente'] == $id_cliente);
                
                $resultado[] = [
                    'NumeroTurno' => $numero,
                    'HorarioDescripcion' => $horario['desc'],
                    'HoraInicio' => $horario['inicio'],
                    'HoraFin' => $horario['fin'],
                    'IdTurno' => (int)$turno['IdTurno'],
                    'Estado' => $turno['EstadoTurno'],
                    'IdCliente' => (int)$turno['TB_CLIENTE_IdCliente'],
                    'Cliente' => trim($turno['Cliente']),
                    'Disponible' => 0,
                    'EsMio' => $es_mio
                ];
                
                log_message('info', "Turno $numero: Ocupado - EsMio: " . ($es_mio ? 'SI' : 'NO'));
            } else {
                // Turno disponible
                $resultado[] = [
                    'NumeroTurno' => $numero,
                    'HorarioDescripcion' => $horario['desc'],
                    'HoraInicio' => $horario['inicio'],
                    'HoraFin' => $horario['fin'],
                    'IdTurno' => 0,
                    'Estado' => 'disponible',
                    'IdCliente' => 0,
                    'Cliente' => '',
                    'Disponible' => 1,
                    'EsMio' => false
                ];
                
                log_message('info', "Turno $numero: Disponible");
            }
        }
        
        log_message('info', 'Total turnos procesados: ' . count($resultado));
        return $resultado;
        
    } catch (Exception $e) {
        log_message('error', 'Error en get_turnos_dia_usuario: ' . $e->getMessage());
        return [];
    }
}

/**
 * Obtener estadísticas globales (para admin)
 */
public function get_estadisticas_globales($fecha_desde = null, $fecha_hasta = null) {
    try {
        // Si no hay fechas, usar el mes actual
        if (!$fecha_desde) $fecha_desde = date('Y-m-01');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-t');
        
        // Total de turnos
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $total = $this->db->count_all_results('tb_turno');
        
        // Finalizados
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $this->db->where('EstadoTurno', 'finalizado');
        $finalizados = $this->db->count_all_results('tb_turno');
        
        // Cancelados
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $this->db->where('EstadoTurno', 'cancelado');
        $cancelados = $this->db->count_all_results('tb_turno');
        
        // Calcular tasa de asistencia
        $tasa_asistencia = 0;
        if ($total > 0) {
            $tasa_asistencia = round(($finalizados / $total) * 100, 1);
        }
        
        return [
            'total' => $total,
            'finalizados' => $finalizados,
            'cancelados' => $cancelados,
            'pendientes' => $total - $finalizados - $cancelados,
            'tasa_asistencia' => $tasa_asistencia
        ];
    } catch (Exception $e) {
        log_message('error', 'Error en get_estadisticas_globales: ' . $e->getMessage());
        return [
            'total' => 0,
            'finalizados' => 0,
            'cancelados' => 0,
            'pendientes' => 0,
            'tasa_asistencia' => 0
        ];
    }
}

/**
 * Obtener estadísticas de un cliente específico
 */
public function get_estadisticas_cliente($id_cliente, $fecha_desde = null, $fecha_hasta = null) {
    try {
        // Convertir a integer
        $id_cliente = intval($id_cliente);
        
        // Si no hay fechas, usar el mes actual
        if (!$fecha_desde) $fecha_desde = date('Y-m-01');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-t');
        
        // Total de turnos
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $total = $this->db->count_all_results('tb_turno');
        
        // Finalizados
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $this->db->where('EstadoTurno', 'finalizado');
        $finalizados = $this->db->count_all_results('tb_turno');
        
        // Cancelados
        $this->db->where('TB_CLIENTE_IdCliente', $id_cliente);
        $this->db->where('FechaTurno >=', $fecha_desde);
        $this->db->where('FechaTurno <=', $fecha_hasta);
        $this->db->where('EstadoTurno', 'cancelado');
        $cancelados = $this->db->count_all_results('tb_turno');
        
        // Calcular tasa de asistencia
        $tasa_asistencia = 0;
        if ($total > 0) {
            $tasa_asistencia = round(($finalizados / $total) * 100, 1);
        }
        
        return [
            'total' => $total,
            'finalizados' => $finalizados,
            'cancelados' => $cancelados,
            'pendientes' => $total - $finalizados - $cancelados,
            'tasa_asistencia' => $tasa_asistencia
        ];
    } catch (Exception $e) {
        log_message('error', 'Error en get_estadisticas_cliente: ' . $e->getMessage());
        return [
            'total' => 0,
            'finalizados' => 0,
            'cancelados' => 0,
            'pendientes' => 0,
            'tasa_asistencia' => 0
        ];
    }
}

}
