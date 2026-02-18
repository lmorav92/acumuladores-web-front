<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Mproductos');

        // 🔒 Validar sesión
        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Sesión expirada'
                ]);
                exit;
            } else {
                redirect('welcome');
            }
        }
    }

    /**
     * ==========================================================
     * LISTAR PRODUCTOS
     * ==========================================================
     */
    public function list() {
        header('Content-Type: application/json');

        if (!$this->db->table_exists('TB_PRODUCTO')) {
            echo json_encode(['success' => false, 'message' => 'La tabla TB_PRODUCTO no existe']);
            return;
        }

        try {
            $productos = $this->Mproductos->get_all_productos();

            echo json_encode([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error en productos/list: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * OBTENER PRODUCTO POR ID
     * ==========================================================
     */
    public function getById($id) {
        header('Content-Type: application/json');

        $producto = $this->Mproductos->get_by_id($id);

        if ($producto) {
            echo json_encode([
                'success' => true,
                'producto' => $producto
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
        }
    }

    /**
     * ==========================================================
     * CREAR / ACTUALIZAR PRODUCTO
     * ==========================================================
     */
    public function save() {
        header('Content-Type: application/json');

        $idProducto = $this->input->post('IdProducto');
        $imagenActual = $this->input->post('ImagenActual');

        // Manejar subida de imagen
        $nombreImagen = $imagenActual;
        
        if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] == 0) {
            $config['upload_path'] = './ui/assets/img/productos/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = true;

            // Crear directorio si no existe
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('imagen_producto')) {
                $uploadData = $this->upload->data();
                $nombreImagen = $uploadData['file_name'];

                // Eliminar imagen anterior si existe
                if ($imagenActual && file_exists('./ui/assets/img/productos/' . $imagenActual)) {
                    unlink('./ui/assets/img/productos/' . $imagenActual);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $this->upload->display_errors('', '')
                ]);
                return;
            }
        }

        // Generar código automático si no existe
        $codigoProducto = $this->input->post('CodigoProducto');
        if (!$codigoProducto || empty(trim($codigoProducto))) {
            $idCategoria = $this->input->post('IdCategoria');
            $codigoProducto = $this->Mproductos->generar_codigo($idCategoria);
        }

        $data = [
            'CodigoProducto' => $codigoProducto,
            'TB_CATEGORIA_PRODUCTO_IdCategoria' => $this->input->post('IdCategoria'),
            'TB_PROVEEDOR_IdProveedor' => $this->input->post('IdProveedor') ?: NULL,
            'NombreProducto' => $this->input->post('NombreProducto'),
            'DescripcionProducto' => $this->input->post('DescripcionProducto'),
            'Voltaje' => $this->input->post('Voltaje') ?: NULL,
            'Amperaje' => $this->input->post('Amperaje') ?: NULL,
            'TipoAcumulador' => $this->input->post('TipoAcumulador') ?: 'PLOMO_ACIDO',
            'Aplicacion' => $this->input->post('Aplicacion'),
            'Marca' => $this->input->post('Marca'),
            'Modelo' => $this->input->post('Modelo'),
            'Garantia' => $this->input->post('Garantia') ?: NULL,
            'Stock' => $this->input->post('Stock') ?: 0,
            'StockMinimo' => $this->input->post('StockMinimo') ?: 5,
            'StockMaximo' => $this->input->post('StockMaximo') ?: 100,
            'PrecioCosto' => $this->input->post('PrecioCosto'),
            'PrecioVenta' => $this->input->post('PrecioVenta'),
            'ImagenProducto' => $nombreImagen,
            'CodigoBarras' => $this->input->post('CodigoBarras'),
            'Peso' => $this->input->post('Peso') ?: NULL,
            'Dimensiones' => $this->input->post('Dimensiones'),
            'EstadoProducto' => ($this->input->post('Stock') > 0 ? 'DISPONIBLE' : 'AGOTADO')
        ];

        try {
            if ($idProducto) {
                // 🔄 UPDATE
                // Remover CodigoProducto del update para no modificarlo
                unset($data['CodigoProducto']);
                $res = $this->Mproductos->update($idProducto, $data);
                $mensaje = 'Producto actualizado correctamente';
            } else {
                // ➕ INSERT
                $res = $this->Mproductos->insert($data);
                $mensaje = 'Producto creado correctamente';
            }

            echo json_encode([
                'success' => $res,
                'message' => $res ? $mensaje : 'Error al guardar el producto'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error al guardar producto: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * ELIMINAR PRODUCTO (Cambiar a DESCONTINUADO)
     * ==========================================================
     */
    public function delete($id) {
        header('Content-Type: application/json');

        try {
            $result = $this->Mproductos->delete($id);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Producto descontinuado' : 'Error al descontinuar producto'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error al eliminar producto: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================================
     * LISTAR PRODUCTOS POR CATEGORÍA
     * ==========================================================
     */
    public function getByCategoria($idCategoria) {
        header('Content-Type: application/json');

        $productos = $this->Mproductos->get_by_categoria($idCategoria);

        echo json_encode([
            'success' => true,
            'productos' => $productos
        ]);
    }

    /**
     * ==========================================================
     * ACTUALIZAR SOLO STOCK
     * ==========================================================
     */
    public function updateStock() {
        header('Content-Type: application/json');

        $idProducto = $this->input->post('IdProducto');
        $stock = $this->input->post('Stock');

        if (!$idProducto) {
            echo json_encode([
                'success' => false,
                'message' => 'IdProducto no especificado'
            ]);
            return;
        }

        $res = $this->Mproductos->actualizar_stock($idProducto, $stock);

        echo json_encode([
            'success' => $res,
            'message' => $res ? 'Stock actualizado' : 'Error al actualizar stock'
        ]);
    }

    /**
     * ==========================================================
     * BUSCAR PRODUCTOS
     * ==========================================================
     */
    public function search() {
        header('Content-Type: application/json');

        $term = $this->input->get('q') ?: $this->input->post('q');

        if (empty($term)) {
            echo json_encode([
                'success' => false,
                'message' => 'Término de búsqueda vacío'
            ]);
            return;
        }

        $productos = $this->Mproductos->search($term);

        echo json_encode([
            'success' => true,
            'productos' => $productos
        ]);
    }

    /**
     * ==========================================================
     * PRODUCTOS CON STOCK BAJO
     * ==========================================================
     */
    public function stockBajo() {
        header('Content-Type: application/json');

        $productos = $this->Mproductos->get_stock_bajo();

        echo json_encode([
            'success' => true,
            'productos' => $productos,
            'total' => count($productos)
        ]);
    }

    /**
     * ==========================================================
     * GENERAR CÓDIGO AUTOMÁTICO
     * ==========================================================
     */
    public function generarCodigo() {
        header('Content-Type: application/json');

        $idCategoria = $this->input->get('idCategoria');

        if (!$idCategoria) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de categoría requerido'
            ]);
            return;
        }

        $codigo = $this->Mproductos->generar_codigo($idCategoria);

        echo json_encode([
            'success' => true,
            'codigo' => $codigo
        ]);
    }
}
