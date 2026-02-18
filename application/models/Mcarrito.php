<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcarrito extends CI_Model {

    public function agregarProducto($idCliente, $idUsuario, $idProducto) {

        // Ver si ya existe
        $this->db->where([
            'TB_CLIENTE_IdCliente' => $idCliente,
            'TB_PRODUCTO_IdProducto' => $idProducto,
            'EstadoOrden' => 'Pendiente'
        ]);
        $query = $this->db->get('TB_ORDEN_COMPRA');

        if ($query->num_rows() > 0) {
            // Incrementar cantidad
            $this->db->set('Cantidad', 'Cantidad+1', false);
            $this->db->where('IdOrden', $query->row()->IdOrden);
            $this->db->update('TB_ORDEN_COMPRA');
        } else {
            // Crear nuevo registro
            $this->db->insert('TB_ORDEN_COMPRA', [
                'TB_CLIENTE_IdCliente' => $idCliente,
                'TB_PRODUCTO_IdProducto' => $idProducto,
                'TB_PELADO_IdPelado' => 1, // o NULL según tu lógica
                'Cantidad' => 1,
                'CreatedDate' => date('Y-m-d'),
                'CreatedUser' => $idUsuario,
                'EstadoOrden' => 'Pendiente'
            ]);
        }

        return $this->totalCarrito($idCliente);
    }

    public function totalCarrito($idCliente) {
        $this->db->select_sum('Cantidad');
        $this->db->where([
            'TB_CLIENTE_IdCliente' => $idCliente,
            'EstadoOrden' => 'Pendiente'
        ]);
        return (int) $this->db->get('TB_ORDEN_COMPRA')->row()->Cantidad;
    }

    public function obtenerCarrito($idCliente) {
        $this->db->select('o.*, p.NombreProducto, p.PrecioProducto');
        $this->db->from('TB_ORDEN_COMPRA o');
        $this->db->join('TB_PRODUCTO p', 'p.IdProducto = o.TB_PRODUCTO_IdProducto');
        $this->db->where([
            'o.TB_CLIENTE_IdCliente' => $idCliente,
            'o.EstadoOrden' => 'Pendiente'
        ]);
        return $this->db->get()->result();
    }
}

