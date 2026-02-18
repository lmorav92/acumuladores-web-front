<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mturnos');
        
        // Solo permitir acceso desde línea de comandos o localhost
        if (!$this->input->is_cli_request() && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            show_404();
        }
    }

    /**
     * Actualizar estados de turnos cada minuto
     */
    public function actualizar_turnos() {
        $actualizados = $this->Mturnos->actualizar_estados_automaticos();
        
        $mensaje = date('Y-m-d H:i:s') . " - Turnos actualizados: $actualizados\n";
        
        // Guardar en log
        log_message('info', $mensaje);
        
        // Si se ejecuta desde CLI, mostrar en consola
        if ($this->input->is_cli_request()) {
            echo $mensaje;
        }
        
        return $actualizados;
    }
}
