<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mventas extends CI_Model {

    /**
     * ==========================================================
     * LISTAR TODAS LAS VENTAS
     * ==========================================================
     */
    public function get_all_ventas() {
        $this->db->select('
            o.IdOrden,
            o.TB_CLIENTE_IdCliente,
            o.Cantidad,
            o.CreatedDate,
            o.EstadoOrden,
            c.NombreCliente,
            c.ApellidosCliente,
            c.Email,
            c.Telefono,
            p.NombreProducto,
            p.PrecioProducto,
            (o.Cantidad * p.PrecioProducto) as Total
        ');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('tb_cliente c', 'o.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->order_by('o.IdOrden', 'DESC');

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
    }

    /**
     * ==========================================================
     * OBTENER VENTA POR ID
     * ==========================================================
     */
    public function get_by_id($id) {
        $this->db->select('
            o.*,
            c.NombreCliente,
            c.ApellidosCliente,
            c.Email,
            p.NombreProducto,
            p.PrecioProducto,
            (o.Cantidad * p.PrecioProducto) as Total
        ');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('tb_cliente c', 'o.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->where('o.IdOrden', $id);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * ==========================================================
     * CREAR VENTA CON DESCUENTO DE STOCK Y REGISTRO EN HISTORIAL
     * ==========================================================
     */
    public function insert_con_historial($data, $idUsuario = null) {
        // Iniciar transacción
        $this->db->trans_start();

        // Obtener información del producto
        $this->db->select('Stock, NombreProducto');
        $this->db->from('TB_PRODUCTO');
        $this->db->where('IdProducto', $data['TB_PRODUCTO_IdProducto']);
        $producto = $this->db->get()->row_array();

        if (!$producto) {
            $this->db->trans_rollback();
            return false;
        }

        $stockActual = $producto['Stock'];
        $cantidad = $data['Cantidad'];

        // Verificar stock disponible
        if ($stockActual < $cantidad) {
            $this->db->trans_rollback();
            return false;
        }

        // Insertar la venta
        $this->db->insert('TB_ORDEN_COMPRA', $data);
        $idVenta = $this->db->insert_id();

        // Calcular nuevo stock
        $nuevoStock = $stockActual - $cantidad;

        // Actualizar stock del producto
        $this->db->where('IdProducto', $data['TB_PRODUCTO_IdProducto']);
        $this->db->update('TB_PRODUCTO', [
            'Stock' => $nuevoStock,
            'EstadoProducto' => $nuevoStock > 0 ? 'Stock' : 'No Stock'
        ]);

        // Registrar en historial de inventario si la tabla existe
        if ($this->db->table_exists('TB_HISTORIAL_INVENTARIO')) {
            $historial = [
                'TB_PRODUCTO_IdProducto' => $data['TB_PRODUCTO_IdProducto'],
                'TB_USUARIO_IdUsuario' => $idUsuario ?: $data['CreatedUser'],
                'TipoMovimiento' => 'VENTA',
                'CantidadAnterior' => $stockActual,
                'CantidadMovimiento' => $cantidad,
                'CantidadNueva' => $nuevoStock,
                'Motivo' => 'Venta desde tienda - Orden #' . $idVenta . ' - Estado: ' . $data['EstadoOrden'],
                'FechaHora' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('TB_HISTORIAL_INVENTARIO', $historial);
        }

        // Completar transacción
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $idVenta;
    }

    /**
     * ==========================================================
     * INSERTAR VENTA (método original, mantener compatibilidad)
     * ==========================================================
     */
    public function insert($data) {
        return $this->db->insert('TB_ORDEN_COMPRA', $data);
    }

    /**
     * ==========================================================
     * ACTUALIZAR VENTA
     * ==========================================================
     */
    public function update($id, $data) {
        $this->db->where('IdOrden', $id);
        return $this->db->update('TB_ORDEN_COMPRA', $data);
    }

    /**
     * ==========================================================
     * ELIMINAR VENTA
     * ==========================================================
     */
    public function delete($id) {
        $this->db->where('IdOrden', $id);
        return $this->db->delete('TB_ORDEN_COMPRA');
    }

    /**
     * ==========================================================
     * CAMBIAR ESTADO DE VENTA
     * ==========================================================
     */
    public function cambiar_estado($id, $estado) {
        $this->db->where('IdOrden', $id);
        return $this->db->update('TB_ORDEN_COMPRA', ['EstadoOrden' => $estado]);
    }

    /**
     * ==========================================================
     * ESTADÍSTICAS DE VENTAS
     * ==========================================================
     */
    public function get_estadisticas() {
        // Total de ventas
        $this->db->select('COUNT(*) as total_ventas');
        $this->db->from('TB_ORDEN_COMPRA');
        $total = $this->db->get()->row_array();

        // Ventas pendientes
        $this->db->select('COUNT(*) as ventas_pendientes');
        $this->db->from('TB_ORDEN_COMPRA');
        $this->db->where('EstadoOrden', 'Pendiente');
        $pendientes = $this->db->get()->row_array();

        // Ventas por comprobar
        $this->db->select('COUNT(*) as ventas_por_comprobar');
        $this->db->from('TB_ORDEN_COMPRA');
        $this->db->where('EstadoOrden', 'por_comprobar');
        $porComprobar = $this->db->get()->row_array();

        // Total ingresos
        $this->db->select('SUM(o.Cantidad * p.PrecioProducto) as total_ingresos');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto');
        $this->db->where('o.EstadoOrden', 'Pagado');
        $ingresos = $this->db->get()->row_array();

        return [
            'total_ventas' => $total['total_ventas'],
            'ventas_pendientes' => $pendientes['ventas_pendientes'],
            'ventas_por_comprobar' => $porComprobar['ventas_por_comprobar'],
            'total_ingresos' => $ingresos['total_ingresos'] ?? 0
        ];
    }

    /**
     * ==========================================================
     * VENTAS POR FECHA
     * ==========================================================
     */
    public function get_by_fecha($fechaInicio, $fechaFin) {
        $this->db->select('
            o.*,
            c.NombreCliente,
            c.ApellidosCliente,
            p.NombreProducto,
            p.PrecioProducto,
            (o.Cantidad * p.PrecioProducto) as Total
        ');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('tb_cliente c', 'o.TB_CLIENTE_IdCliente = c.IdCliente', 'left');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto', 'left');
        $this->db->where('o.CreatedDate >=', $fechaInicio);
        $this->db->where('o.CreatedDate <=', $fechaFin);
        $this->db->order_by('o.CreatedDate', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ==========================================================
     * PRODUCTOS MÁS VENDIDOS
     * ==========================================================
     */
    public function productos_mas_vendidos($limit = 10) {
        $this->db->select('
            p.IdProducto,
            p.NombreProducto,
            p.PrecioProducto,
            SUM(o.Cantidad) as TotalVendido,
            SUM(o.Cantidad * p.PrecioProducto) as TotalIngresos
        ');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'o.TB_PRODUCTO_IdProducto = p.IdProducto');
        $this->db->group_by('p.IdProducto');
        $this->db->order_by('TotalVendido', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }
}
