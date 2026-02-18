<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo Barbero
 * Gestión de barberos
 */
class Mbarbero extends CI_Model {

    private $tabla = 'tb_barbero';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener todos los barberos activos
     * @return array
     */
    public function get_barberos_activos() {
        $this->db->select('*');
        $this->db->from($this->tabla);
        $this->db->where('EstadoBarbero', 'Activo');
        $this->db->order_by('IdBarbero', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Obtener un barbero por ID
     * @param int $id
     * @return object|null
     */
    public function get_barbero_por_id($id) {
        $this->db->select('*');
        $this->db->from($this->tabla);
        $this->db->where('IdBarbero', $id);
        $this->db->where('EstadoBarbero', 'Activo');
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Obtener todos los barberos (activos e inactivos)
     * @return array
     */
    public function get_todos_barberos() {
        $this->db->select('*');
        $this->db->from($this->tabla);
        $this->db->order_by('EstadoBarbero', 'DESC');
        $this->db->order_by('NombreBarbero', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Crear un nuevo barbero
     * @param array $datos
     * @return bool
     */
    public function crear_barbero($datos) {
        $insert_data = array(
            'NombreBarbero' => $datos['nombre'],
            'ApellidosBarbero' => $datos['apellidos'],
            'TelefonoBarbero' => $datos['telefono'] ?? null,
            'EmailBarbero' => $datos['email'] ?? null,
            'EstadoBarbero' => $datos['estado'] ?? 'Activo',
            'FechaContratacion' => $datos['fecha_contratacion'] ?? date('Y-m-d'),
            'Especialidad' => $datos['especialidad'] ?? null,
            'FotoUrl' => $datos['foto_url'] ?? null
        );
        
        return $this->db->insert($this->tabla, $insert_data);
    }

    /**
     * Actualizar un barbero
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar_barbero($id, $datos) {
        $update_data = array();
        
        if (isset($datos['nombre'])) {
            $update_data['NombreBarbero'] = $datos['nombre'];
        }
        if (isset($datos['apellidos'])) {
            $update_data['ApellidosBarbero'] = $datos['apellidos'];
        }
        if (isset($datos['telefono'])) {
            $update_data['TelefonoBarbero'] = $datos['telefono'];
        }
        if (isset($datos['email'])) {
            $update_data['EmailBarbero'] = $datos['email'];
        }
        if (isset($datos['estado'])) {
            $update_data['EstadoBarbero'] = $datos['estado'];
        }
        if (isset($datos['especialidad'])) {
            $update_data['Especialidad'] = $datos['especialidad'];
        }
        if (isset($datos['foto_url'])) {
            $update_data['FotoUrl'] = $datos['foto_url'];
        }
        
        $this->db->where('IdBarbero', $id);
        return $this->db->update($this->tabla, $update_data);
    }

    /**
     * Eliminar (desactivar) un barbero
     * @param int $id
     * @return bool
     */
    public function eliminar_barbero($id) {
        $this->db->where('IdBarbero', $id);
        return $this->db->update($this->tabla, array('EstadoBarbero' => 'Inactivo'));
    }

    /**
     * Obtener estadísticas de barberos
     * @return object
     */
    public function get_estadisticas() {
        $stats = new stdClass();
        
        // Total de barberos activos
        $this->db->from($this->tabla);
        $this->db->where('EstadoBarbero', 'Activo');
        $stats->total_activos = $this->db->count_all_results();
        
        // Total de barberos inactivos
        $this->db->from($this->tabla);
        $this->db->where('EstadoBarbero', 'Inactivo');
        $stats->total_inactivos = $this->db->count_all_results();
        
        return $stats;
    }
}
