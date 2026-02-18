<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mventas');
        $this->load->model('Mproductos');

        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Sesión expirada'
            ]);
            exit;
        }
    }

    /**
     * ==========================================================
     * CONFIRMAR COMPRA - Cambiar de "por_comprobar" a "Pagado"
     * Y registrar en historial de inventario
     * ==========================================================
     */
    public function confirmar_compra() {
        header('Content-Type: application/json');

        $idOrden = $this->input->post('IdOrden');
        $idProducto = $this->input->post('IdProducto');

        if (!$idOrden) {
            echo json_encode([
                'success' => false,
                'message' => 'Orden no especificada'
            ]);
            return;
        }

        // Obtener información de la venta
        $venta = $this->Mventas->get_by_id($idOrden);

        if (!$venta) {
            echo json_encode([
                'success' => false,
                'message' => 'Venta no encontrada'
            ]);
            return;
        }

        // Verificar que el estado sea "por_comprobar"
        if ($venta['EstadoOrden'] !== 'por_comprobar') {
            echo json_encode([
                'success' => false,
                'message' => 'Esta venta no está en estado "por_comprobar". Estado actual: ' . $venta['EstadoOrden']
            ]);
            return;
        }

        // Iniciar transacción
        $this->db->trans_start();

        // Cambiar estado a "Pagado"
        $this->db->where('IdOrden', $idOrden);
        $this->db->update('TB_ORDEN_COMPRA', ['EstadoOrden' => 'Pagado']);

        // Registrar en historial de inventario si la tabla existe
        if ($this->db->table_exists('TB_HISTORIAL_INVENTARIO')) {
            // Obtener información del producto
            $productoId = $idProducto ?: $venta['TB_PRODUCTO_IdProducto'];
            $producto = $this->Mproductos->get_by_id($productoId);

            if ($producto) {
                $historial = [
                    'TB_PRODUCTO_IdProducto' => $productoId,
                    'TB_USUARIO_IdUsuario' => $this->session->userdata('user_id'),
                    'TipoMovimiento' => 'VENTA',
                    'CantidadAnterior' => $producto['Stock'] + $venta['Cantidad'], // Stock antes de la venta original
                    'CantidadMovimiento' => $venta['Cantidad'],
                    'CantidadNueva' => $producto['Stock'], // Stock actual
                    'Motivo' => 'Confirmación de compra - Orden #' . $idOrden . ' - Estado cambiado de "por_comprobar" a "Pagado"',
                    'FechaHora' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('TB_HISTORIAL_INVENTARIO', $historial);
            }
        }

        // Completar transacción
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al confirmar la compra'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Compra confirmada exitosamente.<br>Estado actualizado a <strong>"Pagado"</strong>.<br>Registro añadido al historial de inventario.'
            ]);
        }
    }

    /**
     * ==========================================================
     * REGISTRAR COMPRA DESDE TIENDA (CON QR)
     * ==========================================================
     */
    public function registrar_compra_tienda() {
        header('Content-Type: application/json');

        $idProducto = $this->input->post('IdProducto');
        $idCliente = $this->session->userdata('user_id');
        
        if (!$idProducto) {
            echo json_encode([
                'success' => false,
                'message' => 'Producto no especificado'
            ]);
            return;
        }

        $producto = $this->Mproductos->get_by_id($idProducto);
        
        if (!$producto) {
            echo json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
            return;
        }

        if ($producto['Stock'] < 1) {
            echo json_encode([
                'success' => false,
                'message' => 'Producto sin stock disponible'
            ]);
            return;
        }

        $data = [
            'TB_CLIENTE_IdCliente' => $idCliente,
            'TB_PRODUCTO_IdProducto' => $idProducto,
            'TB_PELADO_IdPelado' => 1,
            'Cantidad' => 1,
            'CreatedDate' => date('Y-m-d'),
            'CreatedUser' => $idCliente,
            'EstadoOrden' => 'por_comprobar'
        ];

        $idVenta = $this->Mventas->insert_con_historial($data, $idCliente);

        if ($idVenta) {
            echo json_encode([
                'success' => true,
                'message' => 'Venta insertada correctamente. Debe ser comprobada.',
                'id_venta' => $idVenta,
                'producto' => $producto['NombreProducto'],
                'precio' => $producto['PrecioProducto']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar la compra. Verifique el stock disponible.'
            ]);
        }
    }

    /**
     * ==========================================================
     * LISTAR VENTAS
     * ==========================================================
     */
    public function list() {
        header('Content-Type: application/json');

        $ventas = $this->Mventas->get_all_ventas();

        echo json_encode([
            'success' => true,
            'ventas' => $ventas
        ]);
    }

    /**
     * ==========================================================
     * OBTENER VENTA POR ID
     * ==========================================================
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $venta = $this->Mventas->get_by_id($id);

        if ($venta) {
            echo json_encode([
                'success' => true,
                'venta' => $venta
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Venta no encontrada'
            ]);
        }
    }

    /**
     * ==========================================================
     * CREAR VENTA (Método administrativo)
     * ==========================================================
     */
    public function save() {
        header('Content-Type: application/json');

        $idOrden = $this->input->post('IdOrden');
        $idProducto = $this->input->post('IdProducto');
        $cantidad = $this->input->post('Cantidad');

        $producto = $this->Mproductos->get_by_id($idProducto);
        
        if (!$producto) {
            echo json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
            return;
        }

        if ($producto['Stock'] < $cantidad && !$idOrden) {
            echo json_encode([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $producto['Stock']
            ]);
            return;
        }

        $data = [
            'TB_CLIENTE_IdCliente' => $this->input->post('IdCliente'),
            'TB_PRODUCTO_IdProducto' => $idProducto,
            'TB_PELADO_IdPelado' => $this->input->post('IdPelado') ?: 1,
            'Cantidad' => $cantidad,
            'CreatedDate' => date('Y-m-d'),
            'CreatedUser' => $this->session->userdata('user_id'),
            'EstadoOrden' => $this->input->post('EstadoOrden') ?: 'Pendiente'
        ];

        if ($idOrden) {
            $res = $this->Mventas->update($idOrden, $data);
            $mensaje = 'Venta actualizada correctamente';
        } else {
            $res = $this->Mventas->insert_con_historial($data, $this->session->userdata('user_id'));
            $mensaje = 'Venta registrada correctamente';
        }

        echo json_encode([
            'success' => $res ? true : false,
            'message' => $res ? $mensaje : 'Error al guardar la venta'
        ]);
    }

    /**
     * ==========================================================
     * CAMBIAR ESTADO
     * ==========================================================
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        $idOrden = $this->input->post('IdOrden');
        $estado = $this->input->post('EstadoOrden');

        $res = $this->Mventas->cambiar_estado($idOrden, $estado);

        echo json_encode([
            'success' => $res,
            'message' => $res ? 'Estado actualizado' : 'Error al actualizar'
        ]);
    }

    /**
     * ==========================================================
     * ELIMINAR VENTA
     * ==========================================================
     */
    public function delete($id) {
        header('Content-Type: application/json');

        $venta = $this->Mventas->get_by_id($id);
        
        $result = $this->Mventas->delete($id);

        if ($result && $venta && ($venta['EstadoOrden'] === 'Pendiente' || $venta['EstadoOrden'] === 'por_comprobar')) {
            $producto = $this->Mproductos->get_by_id($venta['TB_PRODUCTO_IdProducto']);
            if ($producto) {
                $nuevoStock = $producto['Stock'] + $venta['Cantidad'];
                $this->Mproductos->actualizar_stock($venta['TB_PRODUCTO_IdProducto'], $nuevoStock);
            }
        }

        echo json_encode([
            'success' => $result
        ]);
    }

    /**
     * ==========================================================
     * ESTADÍSTICAS
     * ==========================================================
     */
    public function estadisticas() {
        header('Content-Type: application/json');

        $stats = $this->Mventas->get_estadisticas();

        echo json_encode([
            'success' => true,
            'estadisticas' => $stats
        ]);
    }

    /**
     * ==========================================================
     * VENTAS POR FECHA
     * ==========================================================
     */
    public function byFecha() {
        header('Content-Type: application/json');

        $fechaInicio = $this->input->get('fecha_inicio');
        $fechaFin = $this->input->get('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            echo json_encode([
                'success' => false,
                'message' => 'Debe especificar fecha de inicio y fin'
            ]);
            return;
        }

        $ventas = $this->Mventas->get_by_fecha($fechaInicio, $fechaFin);

        echo json_encode([
            'success' => true,
            'ventas' => $ventas
        ]);
    }

    /**
     * ==========================================================
     * PRODUCTOS MÁS VENDIDOS
     * ==========================================================
     */
    public function masVendidos() {
        header('Content-Type: application/json');

        $limit = $this->input->get('limit') ?: 10;
        $productos = $this->Mventas->productos_mas_vendidos($limit);

        echo json_encode([
            'success' => true,
            'productos' => $productos
        ]);
    }
}
