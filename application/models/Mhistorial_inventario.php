<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * =============================================================================
 * MODELO DE HISTORIAL DE INVENTARIO (OPCIONAL)
 * =============================================================================
 * 
 * Este modelo requiere que se haya ejecutado el script: database_optional.sql
 * para crear la tabla TB_HISTORIAL_INVENTARIO
 */

class Mhistorial_inventario extends CI_Model {

    /**
     * ==========================================================
     * REGISTRAR MOVIMIENTO DE INVENTARIO
     * ==========================================================
     */
    public function registrar_movimiento($data) {
        return $this->db->insert('TB_HISTORIAL_INVENTARIO', $data);
    }

    /**
     * ==========================================================
     * OBTENER HISTORIAL POR PRODUCTO
     * ==========================================================
     */
    public function get_by_producto($idProducto, $limit = 50) {
        $this->db->select('
            h.*,
            p.NombreProducto,
            u.UserName as Usuario
        ');
        $this->db->from('TB_HISTORIAL_INVENTARIO h');
        $this->db->join('TB_PRODUCTO p', 'h.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->join('tb_usuario u', 'h.TB_USUARIO_IdUsuario = u.IdUsuario', 'left');
        $this->db->where('h.TB_PRODUCTO_IdProducto', $idProducto);
        $this->db->order_by('h.FechaHora', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * OBTENER HISTORIAL GENERAL
     * ==========================================================
     */
    public function get_historial_general($fechaInicio = null, $fechaFin = null, $limit = 100) {
        $this->db->select('
            h.*,
            p.NombreProducto,
            u.UserName as Usuario
        ');
        $this->db->from('TB_HISTORIAL_INVENTARIO h');
        $this->db->join('TB_PRODUCTO p', 'h.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->join('tb_usuario u', 'h.TB_USUARIO_IdUsuario = u.IdUsuario', 'left');
        
        if ($fechaInicio && $fechaFin) {
            $this->db->where('DATE(h.FechaHora) >=', $fechaInicio);
            $this->db->where('DATE(h.FechaHora) <=', $fechaFin);
        }
        
        $this->db->order_by('h.FechaHora', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * OBTENER MOVIMIENTOS POR TIPO
     * ==========================================================
     */
    public function get_by_tipo($tipo, $limit = 50) {
        $this->db->select('
            h.*,
            p.NombreProducto,
            u.UserName as Usuario
        ');
        $this->db->from('TB_HISTORIAL_INVENTARIO h');
        $this->db->join('TB_PRODUCTO p', 'h.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->join('tb_usuario u', 'h.TB_USUARIO_IdUsuario = u.IdUsuario', 'left');
        $this->db->where('h.TipoMovimiento', $tipo);
        $this->db->order_by('h.FechaHora', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * ESTADÍSTICAS DE MOVIMIENTOS
     * ==========================================================
     */
    public function get_estadisticas($fechaInicio = null, $fechaFin = null) {
        $this->db->select('
            TipoMovimiento,
            COUNT(*) as TotalMovimientos,
            SUM(CantidadMovimiento) as CantidadTotal
        ');
        $this->db->from('TB_HISTORIAL_INVENTARIO');
        
        if ($fechaInicio && $fechaFin) {
            $this->db->where('DATE(FechaHora) >=', $fechaInicio);
            $this->db->where('DATE(FechaHora) <=', $fechaFin);
        }
        
        $this->db->group_by('TipoMovimiento');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PRODUCTOS CON MÁS MOVIMIENTOS
     * ==========================================================
     */
    public function productos_mas_movimientos($limit = 10) {
        $this->db->select('
            p.IdProducto,
            p.NombreProducto,
            COUNT(h.IdHistorial) as TotalMovimientos,
            SUM(CASE WHEN h.TipoMovimiento = "ENTRADA" THEN h.CantidadMovimiento ELSE 0 END) as TotalEntradas,
            SUM(CASE WHEN h.TipoMovimiento = "SALIDA" THEN h.CantidadMovimiento ELSE 0 END) as TotalSalidas,
            SUM(CASE WHEN h.TipoMovimiento = "VENTA" THEN h.CantidadMovimiento ELSE 0 END) as TotalVentas
        ');
        $this->db->from('TB_HISTORIAL_INVENTARIO h');
        $this->db->join('TB_PRODUCTO p', 'h.TB_PRODUCTO_IdProducto = p.IdProducto');
        $this->db->group_by('p.IdProducto');
        $this->db->order_by('TotalMovimientos', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * REGISTRAR AJUSTE DE STOCK CON HISTORIAL
     * ==========================================================
     */
    public function ajustar_stock_con_historial($idProducto, $idUsuario, $tipoMovimiento, $cantidad, $motivo = '') {
        // Iniciar transacción
        $this->db->trans_start();

        // Obtener producto actual
        $this->db->select('Stock, NombreProducto');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('IdProducto', $idProducto);
        $producto = $this->db->get()->row_array();

        if (!$producto) {
            $this->db->trans_rollback();
            return false;
        }

        $stockActual = $producto['Stock'];
        $stockNuevo = $stockActual;

        // Calcular nuevo stock según el tipo de movimiento
        switch ($tipoMovimiento) {
            case 'ENTRADA':
                $stockNuevo = $stockActual + $cantidad;
                break;
            case 'SALIDA':
            case 'VENTA':
                $stockNuevo = max(0, $stockActual - $cantidad);
                break;
            case 'AJUSTE':
                $stockNuevo = $cantidad;
                $cantidad = $cantidad - $stockActual; // Ajustar la cantidad movida
                break;
            case 'DEVOLUCION':
                $stockNuevo = $stockActual + $cantidad;
                break;
        }

        // Actualizar stock del producto
        $this->db->where('IdProducto', $idProducto);
        $this->db->update('TB_PRODUCTO', [
            'Stock' => $stockNuevo,
            'EstadoProducto' => $stockNuevo > 0 ? 'Stock' : 'No Stock'
        ]);

        // Registrar en historial
        $this->db->insert('TB_HISTORIAL_INVENTARIO', [
            'TB_PRODUCTO_IdProducto' => $idProducto,
            'TB_USUARIO_IdUsuario' => $idUsuario,
            'TipoMovimiento' => $tipoMovimiento,
            'CantidadAnterior' => $stockActual,
            'CantidadMovimiento' => abs($cantidad),
            'CantidadNueva' => $stockNuevo,
            'Motivo' => $motivo,
            'FechaHora' => date('Y-m-d H:i:s')
        ]);

        // Completar transacción
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
