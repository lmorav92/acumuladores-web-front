<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mdashboard');
        
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }
    }

    /**
     * Vista principal del dashboard
     */
    public function index() {
        $this->load->view('dashboard');
    }

    /**
     * Obtener estadísticas generales
     */
    public function estadisticas() {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->Mdashboard->get_estadisticas_generales();
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener turnos recientes (últimos 10)
     */
    public function turnos_recientes() {
        header('Content-Type: application/json');
        
        try {
            $turnos = $this->Mdashboard->get_turnos_recientes(10);
            
            echo json_encode([
                'success' => true,
                'turnos' => $turnos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener turnos: ' . $e->getMessage(),
                'turnos' => []
            ]);
        }
    }

    /**
     * Obtener clientes recientes con su cantidad de turnos
     */
    public function clientes_recientes() {
        header('Content-Type: application/json');
        
        try {
            $clientes = $this->Mdashboard->get_clientes_recientes(10);
            
            echo json_encode([
                'success' => true,
                'clientes' => $clientes
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage(),
                'clientes' => []
            ]);
        }
    }

    /**
     * Obtener actividad reciente del sistema
     */
    public function actividad_reciente() {
        header('Content-Type: application/json');
        
        try {
            $actividad = $this->Mdashboard->get_actividad_reciente(10);
            
            echo json_encode([
                'success' => true,
                'actividad' => $actividad
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener actividad: ' . $e->getMessage(),
                'actividad' => []
            ]);
        }
    }

    /**
     * Obtener estadísticas mensuales (últimos 6 meses)
     */
    public function estadisticas_mensuales() {
        header('Content-Type: application/json');
        
        try {
            $meses = $this->Mdashboard->get_estadisticas_mensuales(6);
            
            echo json_encode([
                'success' => true,
                'meses' => $meses
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas mensuales: ' . $e->getMessage(),
                'meses' => []
            ]);
        }
    }

    /**
     * Obtener estadísticas de un día específico
     */
    public function estadisticas_dia($fecha = null) {
        header('Content-Type: application/json');
        
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        
        try {
            $stats = $this->Mdashboard->get_estadisticas_dia($fecha);
            
            echo json_encode([
                'success' => true,
                'fecha' => $fecha,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas del día: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener top clientes con más turnos
     */
    public function top_clientes() {
        header('Content-Type: application/json');
        
        try {
            $top = $this->Mdashboard->get_top_clientes(5);
            
            echo json_encode([
                'success' => true,
                'top_clientes' => $top
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener top clientes: ' . $e->getMessage(),
                'top_clientes' => []
            ]);
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function productos_stock_bajo() {
        header('Content-Type: application/json');
        
        try {
            $productos = $this->Mdashboard->get_productos_stock_bajo(10);
            
            echo json_encode([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage(),
                'productos' => []
            ]);
        }
    }

    /**
     * Obtener productos sin stock
     */
    public function productos_sin_stock() {
        header('Content-Type: application/json');
        
        try {
            $productos = $this->Mdashboard->get_productos_sin_stock();
            
            echo json_encode([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos sin stock: ' . $e->getMessage(),
                'productos' => []
            ]);
        }
    }

    /**
     * Exportar reporte (para futuro desarrollo)
     */
    public function exportar() {
        $tipo = $this->input->get('tipo');
        $formato = $this->input->get('formato');
        
        // Aquí iría la lógica de exportación
        // Por ahora, redirigir de vuelta
        redirect('home?page=dashboard');
    }

    /**
     * Obtener productos más vendidos
     */
    public function productos_top() {
        header('Content-Type: application/json');
        
        try {
            $productos = $this->Mdashboard->get_productos_top(10);

			log_message('info', 'Productos top: ' . json_encode($productos));
            
            echo json_encode([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage(),
                'productos' => []
            ]);
        }
    }

    /**
     * Obtener alertas de stock bajo
     */
    public function alertas_stock() {
        header('Content-Type: application/json');
        
        try {
            $alertas = $this->Mdashboard->get_alertas_stock(10);
            
            echo json_encode([
                'success' => true,
                'alertas' => $alertas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener alertas: ' . $e->getMessage(),
                'alertas' => []
            ]);
        }
    }

    /**
     * Obtener clientes frecuentes
     */
    public function clientes_frecuentes() {
        header('Content-Type: application/json');
        
        try {
            $clientes = $this->Mdashboard->get_clientes_frecuentes(10);
            
            echo json_encode([
                'success' => true,
                'clientes' => $clientes
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage(),
                'clientes' => []
            ]);
        }
    }

    /**
     * Obtener performance de barberos
     */
    public function barberos_performance() {
        header('Content-Type: application/json');
        
        try {
            $barberos = $this->Mdashboard->get_barberos_performance();
            
            echo json_encode([
                'success' => true,
                'barberos' => $barberos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener barberos: ' . $e->getMessage(),
                'barberos' => []
            ]);
        }
    }

    /**
     * Obtener ingresos mensuales para gráficos
     */
    public function ingresos_mensuales() {
        header('Content-Type: application/json');
        
        try {
            $meses = $this->Mdashboard->get_ingresos_mensuales(6);
            
            echo json_encode([
                'success' => true,
                'meses' => $meses
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener ingresos mensuales: ' . $e->getMessage(),
                'meses' => []
            ]);
        }
    }

    /**
     * Obtener comparativo mensual para gráficos
     */
    public function comparativo_mensual() {
        header('Content-Type: application/json');
        
        try {
            $meses = $this->Mdashboard->get_comparativo_mensual(6);
            
            echo json_encode([
                'success' => true,
                'meses' => $meses
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener comparativo: ' . $e->getMessage(),
                'meses' => []
            ]);
        }
    }
}
