<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo Multimedia
 * Gestión de multimedia (carousel, imágenes, etc.)
 */
class Mmultimedia extends CI_Model {

    private $tabla = 'tb_multimedia';
    private $tabla_config = 'tb_configuracion';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener slides activos del carousel ordenados
     * @return array
     */
    public function get_slides_activos() {
        $this->db->select('m.*, c.DescripcionConfig, c.SloganConfig');
        $this->db->from($this->tabla . ' m');
        $this->db->join($this->tabla_config . ' c', 'm.TB_CONFIGURACION_IdConfig = c.IdConfig', 'left');
        $this->db->where('m.ActivoMultimedia', 1);
        $this->db->where('m.TipoMultimedia', 'carousel_slide');
        $this->db->order_by('m.OrdenMultimedia', 'ASC');
        
        $query = $this->db->get();
        
        if (!$query) {
            return [];
        }
        
        return $query->result();
    }

    /**
     * Obtener un slide por ID
     * @param int $id
     * @return object|null
     */
    public function get_slide_por_id($id) {
        $this->db->select('m.*, c.DescripcionConfig, c.SloganConfig');
        $this->db->from($this->tabla . ' m');
        $this->db->join($this->tabla_config . ' c', 'm.TB_CONFIGURACION_IdConfig = c.IdConfig', 'left');
        $this->db->where('m.IdMultimedia', $id);
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Obtener todos los slides (activos e inactivos)
     * @return array
     */
    public function get_todos_slides() {
        $this->db->select('m.*, c.DescripcionConfig');
        $this->db->from($this->tabla . ' m');
        $this->db->join($this->tabla_config . ' c', 'm.TB_CONFIGURACION_IdConfig = c.IdConfig', 'left');
        $this->db->where('m.TipoMultimedia', 'carousel_slide');
        $this->db->order_by('m.OrdenMultimedia', 'ASC');
        
        $query = $this->db->get();
        
        if (!$query) {
            return [];
        }
        
        return $query->result();
    }

    /**
     * Crear un nuevo slide
     * @param array $datos
     * @return bool
     */
    public function crear_slide($datos) {
        $insert_data = array(
            'TB_CONFIGURACION_IdConfig' => $datos['id_config'] ?? 1,
            'UrlMultimedia' => $datos['url_imagen'],
            'DescripcionMultimedia' => $datos['descripcion'] ?? null,
            'TituloSlide' => $datos['titulo'],
            'SubtituloSlide' => $datos['subtitulo'] ?? null,
            'IconoSlide' => $datos['icono'] ?? null,
            'TextoBoton' => $datos['texto_boton'] ?? null,
            'AccionBoton' => $datos['enlace_boton'] ?? null,
            'OrdenMultimedia' => $datos['orden'] ?? $this->get_next_orden(),
            'TipoMultimedia' => 'carousel_slide',
            'ActivoMultimedia' => isset($datos['activo']) ? $datos['activo'] : 1,
            'CreatedDateMultimedia' => date('Y-m-d'),
            'CreatedUserMultimedia' => $datos['created_user'] ?? null
        );
        
        return $this->db->insert($this->tabla, $insert_data);
    }

    /**
     * Actualizar un slide
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar_slide($id, $datos) {
        $update_data = array();
        
        if (isset($datos['url_imagen'])) {
            $update_data['UrlMultimedia'] = $datos['url_imagen'];
        }
        if (isset($datos['titulo'])) {
            $update_data['TituloSlide'] = $datos['titulo'];
        }
        if (isset($datos['subtitulo'])) {
            $update_data['SubtituloSlide'] = $datos['subtitulo'];
        }
        if (isset($datos['descripcion'])) {
            $update_data['DescripcionMultimedia'] = $datos['descripcion'];
        }
        if (isset($datos['icono'])) {
            $update_data['IconoSlide'] = $datos['icono'];
        }
        if (isset($datos['texto_boton'])) {
            $update_data['TextoBoton'] = $datos['texto_boton'];
        }
        if (isset($datos['enlace_boton'])) {
            $update_data['AccionBoton'] = $datos['enlace_boton'];
        }
        if (isset($datos['orden'])) {
            $update_data['OrdenMultimedia'] = $datos['orden'];
        }
        if (isset($datos['activo'])) {
            $update_data['ActivoMultimedia'] = $datos['activo'];
        }
        
        $update_data['UpdatedDateMultimedia'] = date('Y-m-d');
        if (isset($datos['updated_user'])) {
            $update_data['UpdatedUserMultimedia'] = $datos['updated_user'];
        }
        
        $this->db->where('IdMultimedia', $id);
        return $this->db->update($this->tabla, $update_data);
    }

    /**
     * Eliminar (desactivar) un slide
     * @param int $id
     * @return bool
     */
    public function eliminar_slide($id) {
        $this->db->where('IdMultimedia', $id);
        return $this->db->update($this->tabla, array(
            'ActivoMultimedia' => 0,
            'UpdatedDateMultimedia' => date('Y-m-d')
        ));
    }

    /**
     * Eliminar permanentemente un slide
     * @param int $id
     * @return bool
     */
    public function eliminar_permanente($id) {
        $this->db->where('IdMultimedia', $id);
        return $this->db->delete($this->tabla);
    }

    /**
     * Reordenar slides
     * @param array $orden Array con id => orden
     * @return bool
     */
    public function reordenar_slides($orden) {
        $this->db->trans_start();
        
        foreach ($orden as $id => $posicion) {
            $this->db->where('IdMultimedia', $id);
            $this->db->update($this->tabla, array('OrdenMultimedia' => $posicion));
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Obtener el siguiente número de orden
     * @return int
     */
    private function get_next_orden() {
        $this->db->select_max('OrdenMultimedia');
        $this->db->from($this->tabla);
        $this->db->where('TipoMultimedia', 'carousel_slide');
        $query = $this->db->get();
        $row = $query->row();
        
        return ($row->OrdenMultimedia ?? 0) + 1;
    }

    /**
     * Obtener configuración general
     * @return object|null
     */
    public function get_configuracion() {
        $this->db->select('*');
        $this->db->from($this->tabla_config);
        $this->db->where('IdConfig', 1);
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Actualizar configuración general
     * @param array $datos
     * @return bool
     */
    public function actualizar_configuracion($datos) {
        $update_data = array();
        
        if (isset($datos['descripcion'])) {
            $update_data['DescripcionConfig'] = $datos['descripcion'];
        }
        if (isset($datos['slogan'])) {
            $update_data['SloganConfig'] = $datos['slogan'];
        }
        
        $this->db->where('IdConfig', 1);
        
        // Si no existe, insertar
        if ($this->db->count_all_results($this->tabla_config) == 0) {
            $update_data['IdConfig'] = 1;
            $update_data['CreatedDateConfig'] = date('Y-m-d');
            return $this->db->insert($this->tabla_config, $update_data);
        }
        
        return $this->db->update($this->tabla_config, $update_data);
    }

    /**
     * Obtener estadísticas de multimedia
     * @return object
     */
    public function get_estadisticas() {
        $stats = new stdClass();
        
        // Total de slides activos
        $this->db->from($this->tabla);
        $this->db->where('ActivoMultimedia', 1);
        $this->db->where('TipoMultimedia', 'carousel_slide');
        $stats->total_activos = $this->db->count_all_results();
        
        // Total de slides inactivos
        $this->db->from($this->tabla);
        $this->db->where('ActivoMultimedia', 0);
        $this->db->where('TipoMultimedia', 'carousel_slide');
        $stats->total_inactivos = $this->db->count_all_results();
        
        return $stats;
    }
}
