<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdashboard extends CI_Model {

    /**
     * ==========================================================
     * ESTADÍSTICAS GENERALES DEL DASHBOARD
     * ==========================================================
     */
    public function get_estadisticas_generales() {

        $hoy = date('Y-m-d');
        $mesActual = date('Y-m');

		log_message('info', 'Mdashboard::get_estadisticas_generales()' . $hoy);
        
        // Total de clientes registrados
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_cliente');
        $totalClientes = $this->db->get()->row()->total;

		log_message('info', 'Total de clientes: ' . $totalClientes);
        
        // Turnos de hoy
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $hoy);
        $turnosHoy = $this->db->get()->row()->total;

		log_message('info', 'Turnos de hoy: ' . $turnosHoy);
        
        // Turnos pendientes hoy
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $hoy);
        $this->db->where('EstadoTurno', 'Pendiente');
        $turnosPendientes = $this->db->get()->row()->total;

		log_message('info', 'Turnos pendientes hoy: ' . $turnosPendientes);
        
        // Turnos atendidos hoy
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $hoy);
        $this->db->where('EstadoTurno', 'Atendido');
        $turnosAtendidos = $this->db->get()->row()->total;

		log_message('info', 'Turnos atendidos hoy: ' . $turnosAtendidos);
        
        // Productos vendidos hoy (cantidad total)
        $this->db->select('COALESCE(SUM(Cantidad), 0) as total');
        $this->db->from('TB_ORDEN_COMPRA');
        $this->db->where('DATE(CreatedDate)', $hoy);
		 $this->db->where('EstadoOrden', 'Pagado');
        $productosVendidos = $this->db->get()->row()->total;

		log_message('info', 'Productos vendidos hoy: ' . $productosVendidos);
        
        // Ingresos del día por turnos
        $this->db->select('COALESCE(SUM(p.PrecioPelado), 0) as total');
        $this->db->from('tb_turno t');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->where('DATE(t.FechaTurno)', $hoy);
        $this->db->where('t.EstadoTurno', 'Atendido');
        $ingresosTurnos = $this->db->get()->row()->total;

		log_message('info', 'Ingresos del día por turnos: ' . $ingresosTurnos);
        
        // Ingresos del día por productos
        $this->db->select('COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as total');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->where('DATE(o.CreatedDate)', $hoy);
        $this->db->where('o.EstadoOrden', 'Pagado');
        $ingresosProductos = $this->db->get()->row()->total;

		log_message('info', 'Ingresos del día por productos: ' . $ingresosProductos);
        
        $ingresosDia = $ingresosTurnos + $ingresosProductos;

		log_message('info', 'Ingresos totales del día: ' . $ingresosDia);
        
        // Productos en stock
        $this->db->select('COUNT(*) as total');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('Stock >', 0);
        $this->db->where('EstadoProducto', 'Stock');
        $productosStock = $this->db->get()->row()->total;

		log_message('info', 'Productos en stock: ' . $productosStock);
        
        // Ventas del mes (solo productos pagados)
        $this->db->select('COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as total');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->where('DATE_FORMAT(o.CreatedDate, "%Y-%m") =', $mesActual);
        $this->db->where('o.EstadoOrden', 'Pagado');
        $ventasMes = $this->db->get()->row()->total;
		
		log_message('info', 'Ventas del mes: ' . $ventasMes);

        // Estados de turnos para el gráfico
        $this->db->select('EstadoTurno, COUNT(*) as cantidad');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $hoy);
        $this->db->group_by('EstadoTurno');
        $estadosTurnos = $this->db->get()->result_array();

		log_message('info', 'Estados de turnos hoy: ' . json_encode($estadosTurnos));
        
        // Convertir estados a formato para gráfico
        $estadosChart = [
            'pendiente' => 0,
            'atendido' => 0,
            'cancelado' => 0,
			'reservado' => 0
        ];
        
        foreach ($estadosTurnos as $estado) {
            if (isset($estadosChart[$estado['EstadoTurno']])) {
                $estadosChart[$estado['EstadoTurno']] = (int)$estado['cantidad'];
            }
        }
        
        return [
            'totalClientes' => (int)$totalClientes,
            'turnosHoy' => (int)$turnosHoy,
            'turnosPendientes' => (int)$turnosPendientes,
            'turnosAtendidos' => (int)$turnosAtendidos,
            'productosVendidos' => (int)$productosVendidos,
            'ingresosDia' => (float)$ingresosDia,
            'productosStock' => (int)$productosStock,
            'ventasMes' => (float)$ventasMes,
            'estadosTurnos' => $estadosChart
        ];
    }

    /**
     * ==========================================================
     * TURNOS RECIENTES
     * ==========================================================
     */
    public function get_turnos_recientes($limit = 10) {
        $this->db->select('
            t.IdTurno,
            t.FechaTurno,
            t.HorarioTurno,
            t.EstadoTurno,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) as Cliente,
            b.NombreBarbero as Barbero,
            p.NombrePelado as Servicio,
            p.PrecioPelado as Precio
        ');
        $this->db->from('tb_turno t');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->join('tb_barbero b', 't.TB_BARBERO_IdBarbero = b.IdBarbero', 'left');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->order_by('t.FechaTurno', 'DESC');
        //$this->db->order_by('t.HoraTurno', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
		log_message('info', 'Turnos recientes: ' . json_encode($query->result_array()));
        return $query->result_array();
    }

    /**
     * ==========================================================
     * CLIENTES RECIENTES CON CANTIDAD DE TURNOS
     * ==========================================================
     */
    public function get_clientes_recientes($limit = 10) {
        $this->db->select('
            c.IdCliente,
            c.NombreCliente,
            c.ApellidosCliente,
            c.Email,
            c.Telefono,
            c.Avatar,
            COUNT(t.IdTurno) as CantidadTurnos,
            MAX(t.FechaTurno) as UltimoTurno
        ');
        $this->db->from('tb_cliente c');
        $this->db->join('tb_turno t', 'c.IdCliente = t.TB_CLIENTE_IdCliente', 'left');
        $this->db->group_by('c.IdCliente');
        $this->db->order_by('UltimoTurno', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * ACTIVIDAD RECIENTE DEL SISTEMA
     * ==========================================================
     */
    public function get_actividad_reciente($limit = 10) {
        // Combinar actividades de turnos y ventas
        $actividades = [];
        
        // Últimos turnos
        $this->db->select('
            "turno" as tipo,
            t.IdTurno as id,
            t.FechaTurno as fecha,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) as descripcion,
            t.EstadoTurno as estado
        ');
        $this->db->from('tb_turno t');
        $this->db->join('tb_cliente c', 't.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->order_by('t.FechaTurno', 'DESC');
        $this->db->limit($limit / 2);
        $turnos = $this->db->get()->result_array();
        
        // Últimas ventas
        $this->db->select('
            "venta" as tipo,
            o.IdOrden as id,
            o.CreatedDate as fecha,
            CONCAT(c.NombreCliente, " compró ", p.NombreProducto) as descripcion,
            o.EstadoOrden as estado
        ');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('tb_cliente c', 'o.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->order_by('o.CreatedDate', 'DESC');
        $this->db->limit($limit / 2);
        $ventas = $this->db->get()->result_array();
        
        // Combinar y ordenar
        $actividades = array_merge($turnos, $ventas);
        usort($actividades, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
        
        return array_slice($actividades, 0, $limit);
    }

    /**
     * ==========================================================
     * ESTADÍSTICAS MENSUALES (ÚLTIMOS N MESES)
     * ==========================================================
     */
    public function get_estadisticas_mensuales($meses = 6) {
        $resultado = [];
        
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $nombreMes = $this->obtener_nombre_mes($fecha);
            
            // Turnos del mes
            $this->db->select('COUNT(*) as total');
            $this->db->from('tb_turno');
            $this->db->where('DATE_FORMAT(FechaTurno, "%Y-%m") =', $fecha);
            $this->db->where('EstadoTurno', 'Atendido');
            $turnosMes = $this->db->get()->row()->total;
            
            // Ingresos por turnos
            $this->db->select('COALESCE(SUM(p.PrecioPelado), 0) as total');
            $this->db->from('tb_turno t');
            $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
            $this->db->where('DATE_FORMAT(t.FechaTurno, "%Y-%m") =', $fecha);
            $this->db->where('t.EstadoTurno', 'Atendido');
            $ingresosTurnos = $this->db->get()->row()->total;
            
            // Productos vendidos (cantidad)
            $this->db->select('COALESCE(SUM(Cantidad), 0) as total');
            $this->db->from('TB_ORDEN_COMPRA');
            $this->db->where('DATE_FORMAT(CreatedDate, "%Y-%m") =', $fecha);
            $this->db->where('EstadoOrden', 'Pagado');
            $productosVendidos = $this->db->get()->row()->total;
            
            // Ingresos por productos
            $this->db->select('COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as total');
            $this->db->from('TB_ORDEN_COMPRA o');
            $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
            $this->db->where('DATE_FORMAT(o.CreatedDate, "%Y-%m") =', $fecha);
            $this->db->where('o.EstadoOrden', 'Pagado');
            $ingresosProductos = $this->db->get()->row()->total;
            
            $resultado[] = [
                'mes' => $nombreMes,
                'periodo' => $fecha,
                'turnos' => (int)$turnosMes,
                'ingresos_turnos' => (float)$ingresosTurnos,
                'ventas_productos' => (int)$productosVendidos,
                'ingresos_productos' => (float)$ingresosProductos,
                'ingresos_totales' => (float)($ingresosTurnos + $ingresosProductos)
            ];
        }
        
        return $resultado;
    }

    /**
     * ==========================================================
     * ESTADÍSTICAS DE UN DÍA ESPECÍFICO
     * ==========================================================
     */
    public function get_estadisticas_dia($fecha) {
        // Turnos del día
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $fecha);
        $turnosDia = $this->db->get()->row()->total;
        
        // Turnos atendidos
        $this->db->select('COUNT(*) as total');
        $this->db->from('tb_turno');
        $this->db->where('DATE(FechaTurno)', $fecha);
        $this->db->where('EstadoTurno', 'Atendido');
        $turnosAtendidos = $this->db->get()->row()->total;
        
        // Ingresos por turnos
        $this->db->select('COALESCE(SUM(p.PrecioPelado), 0) as total');
        $this->db->from('tb_turno t');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->where('DATE(t.FechaTurno)', $fecha);
        $this->db->where('t.EstadoTurno', 'Atendido');
        $ingresosTurnos = $this->db->get()->row()->total;
        
        // Productos vendidos
        $this->db->select('COALESCE(SUM(Cantidad), 0) as total');
        $this->db->from('TB_ORDEN_COMPRA');
        $this->db->where('DATE(CreatedDate)', $fecha);
        $this->db->where('EstadoOrden', 'Pagado');
        $productosVendidos = $this->db->get()->row()->total;
        
        // Ingresos por productos
        $this->db->select('COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as total');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->where('DATE(o.CreatedDate)', $fecha);
        $this->db->where('o.EstadoOrden', 'Pagado');
        $ingresosProductos = $this->db->get()->row()->total;
        
        return [
            'fecha' => $fecha,
            'turnos_total' => (int)$turnosDia,
            'turnos_atendidos' => (int)$turnosAtendidos,
            'ingresos_turnos' => (float)$ingresosTurnos,
            'productos_vendidos' => (int)$productosVendidos,
            'ingresos_productos' => (float)$ingresosProductos,
            'ingresos_totales' => (float)($ingresosTurnos + $ingresosProductos)
        ];
    }

    /**
     * ==========================================================
     * TOP CLIENTES CON MÁS TURNOS
     * ==========================================================
     */
    public function get_top_clientes($limit = 5) {
        $this->db->select('
            c.IdCliente,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) as Cliente,
            c.Email,
            c.Telefono,
            COUNT(t.IdTurno) as TotalTurnos,
            COALESCE(SUM(p.PrecioPelado), 0) as TotalGastado
        ');
        $this->db->from('tb_cliente c');
        $this->db->join('tb_turno t', 'c.IdCliente = t.TB_CLIENTE_IdCliente', 'left');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->where('t.EstadoTurno', 'Atendido');
        $this->db->group_by('c.IdCliente');
        $this->db->order_by('TotalTurnos', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PRODUCTOS CON STOCK BAJO
     * ==========================================================
     */
    public function get_productos_stock_bajo($limite = 10) {
        $this->db->select('
            IdProducto,
            NombreProducto,
            Stock,
            PrecioProducto,
            EstadoProducto
        ');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('Stock <=', $limite);
        $this->db->where('Stock >', 0);
        $this->db->order_by('Stock', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PRODUCTOS SIN STOCK
     * ==========================================================
     */
    public function get_productos_sin_stock() {
        $this->db->select('
            IdProducto,
            NombreProducto,
            PrecioProducto
        ');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('Stock', 0);
        $this->db->or_where('EstadoProducto', 'No Stock');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * HELPER: OBTENER NOMBRE DEL MES
     * ==========================================================
     */
    private function obtener_nombre_mes($fecha) {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        $partes = explode('-', $fecha);
        $mes = $partes[1];
        $anio = $partes[0];
        
        return $meses[$mes] . ' ' . $anio;
    }

    /**
     * ==========================================================
     * PRODUCTOS MÁS VENDIDOS (TOP)
     * ==========================================================
     */
    public function get_productos_top($limit = 10) {
        $this->db->select('
    p.IdProducto as id,
    p.NombreProducto as nombre,
    c.NombreCategoria as categoria,
    p.Stock as stock,
    COALESCE(SUM(o.Cantidad), 0) as vendidos,
    COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as ingresos,
    10 as stock_minimo
', FALSE);
$this->db->from('TB_PRODUCTO p');
$this->db->join('TB_ORDEN_COMPRA o', 'p.IdProducto = o.TB_PRODUCTO_IdProducto AND o.EstadoOrden = "Pagado"', 'left');
$this->db->join('TB_CATEGORIA_PRODUCTO c', 'p.TB_CATEGORIA_PRODUCTO_IdCategoria = c.IdCategoria', 'left');
$this->db->group_by('p.IdProducto');
$this->db->order_by('vendidos', 'DESC');

        $this->db->limit($limit);
        
        $query = $this->db->get();

		log_message('info', 'Productos top: ' . json_encode($query->result_array()));

        return $query->result_array();
    }

    /**
     * ==========================================================
     * ALERTAS DE STOCK BAJO
     * ==========================================================
     */
    public function get_alertas_stock($stock_minimo = 10) {
        $this->db->select('
            IdProducto as id,
            NombreProducto as nombre,
            Stock as stock,
            ' . $stock_minimo . ' as stock_minimo
        ');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('Stock <=', $stock_minimo);
        $this->db->where('Stock >', 0);
        $this->db->where('EstadoProducto', 'Stock');
        $this->db->order_by('Stock', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * CLIENTES FRECUENTES
     * ==========================================================
     */
    public function get_clientes_frecuentes($limit = 10) {
        $this->db->select('
            c.IdCliente as id,
            CONCAT(c.NombreCliente, " ", c.ApellidosCliente) as nombre,
            COUNT(t.IdTurno) as visitas,
            COALESCE(SUM(p.PrecioPelado), 0) as gasto_total
        ');
        $this->db->from('tb_cliente c');
        $this->db->join('tb_turno t', 'c.IdCliente = t.TB_CLIENTE_IdCliente', 'left');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->where('t.EstadoTurno', 'Atendido');
        $this->db->group_by('c.IdCliente');
        $this->db->having('visitas >', 0);
        $this->db->order_by('visitas', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PERFORMANCE DE BARBEROS
     * ==========================================================
     */
    public function get_barberos_performance() {
        // Verificar si la tabla tb_barbero existe
        if (!$this->db->table_exists('tb_barbero')) {
            // Si no existe, retornar array vacío
            return [];
        }
        
        $this->db->select('
            b.IdBarbero as id,
            b.NombreBarbero as nombre,
            COUNT(t.IdTurno) as turnos_totales,
            SUM(CASE WHEN t.EstadoTurno = "Atendido" THEN 1 ELSE 0 END) as turnos_atendidos,
            COALESCE(SUM(CASE WHEN t.EstadoTurno = "Atendido" THEN p.PrecioPelado ELSE 0 END), 0) as ingresos_generados
        ');
        $this->db->from('tb_barbero b');
        $this->db->join('tb_turno t', 'b.IdBarbero = t.TB_BARBERO_IdBarbero', 'left');
        $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
        $this->db->where('b.EstadoBarbero', 'Activo');
        $this->db->group_by('b.IdBarbero');
        $this->db->order_by('turnos_atendidos', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * INGRESOS MENSUALES (PARA GRÁFICOS)
     * ==========================================================
     */
    public function get_ingresos_mensuales($meses = 6) {
        $resultado = [];
        
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $nombreMes = $this->obtener_nombre_mes_corto($fecha);
            
            // Ingresos por turnos
            $this->db->select('COALESCE(SUM(p.PrecioPelado), 0) as total');
            $this->db->from('tb_turno t');
            $this->db->join('TB_PELADO p', 't.TB_PELADO_IdPelado = p.IdPelado', 'left');
            $this->db->where('DATE_FORMAT(t.FechaTurno, "%Y-%m") =', $fecha);
            $this->db->where('t.EstadoTurno', 'Atendido');
            $ingresosTurnos = $this->db->get()->row()->total;
            
            // Ingresos por productos
            $this->db->select('COALESCE(SUM(o.Cantidad * p.PrecioProducto), 0) as total');
            $this->db->from('TB_ORDEN_COMPRA o');
            $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
            $this->db->where('DATE_FORMAT(o.CreatedDate, "%Y-%m") =', $fecha);
            $this->db->where('o.EstadoOrden', 'Pagado');
            $ingresosProductos = $this->db->get()->row()->total;
            
            $resultado[] = [
                'mes' => $nombreMes,
                'periodo' => $fecha,
                'ingresos_turnos' => (float)$ingresosTurnos,
                'ingresos_productos' => (float)$ingresosProductos,
                'total' => (float)($ingresosTurnos + $ingresosProductos)
            ];
        }
        
        return $resultado;
    }

    /**
     * ==========================================================
     * COMPARATIVO MENSUAL (PARA GRÁFICOS)
     * ==========================================================
     */
    public function get_comparativo_mensual($meses = 6) {
        $resultado = [];
        
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $nombreMes = $this->obtener_nombre_mes_corto($fecha);
            
            // Turnos atendidos del mes
            $this->db->select('COUNT(*) as total');
            $this->db->from('tb_turno');
            $this->db->where('DATE_FORMAT(FechaTurno, "%Y-%m") =', $fecha);
            $this->db->where('EstadoTurno', 'Atendido');
            $turnos = $this->db->get()->row()->total;
            
            // Productos vendidos del mes
            $this->db->select('COALESCE(SUM(Cantidad), 0) as total');
            $this->db->from('TB_ORDEN_COMPRA');
            $this->db->where('DATE_FORMAT(CreatedDate, "%Y-%m") =', $fecha);
            $this->db->where('EstadoOrden', 'Pagado');
            $productos = $this->db->get()->row()->total;
            
            // Nuevos clientes del mes
            $this->db->select('COUNT(*) as total');
            $this->db->from('tb_cliente');
            $this->db->where('DATE_FORMAT(CreatedDate, "%Y-%m") =', $fecha);
            $clientes = $this->db->get()->row()->total;
            
            $resultado[] = [
                'mes' => $nombreMes,
                'periodo' => $fecha,
                'turnos' => (int)$turnos,
                'productos' => (int)$productos,
                'clientes' => (int)$clientes
            ];
        }
        
        return $resultado;
    }

    /**
     * ==========================================================
     * HELPER: OBTENER NOMBRE DEL MES CORTO
     * ==========================================================
     */
    private function obtener_nombre_mes_corto($fecha) {
        $meses = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar',
            '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep',
            '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];
        
        $partes = explode('-', $fecha);
        $mes = $partes[1];
        
        return $meses[$mes];
    }
}
