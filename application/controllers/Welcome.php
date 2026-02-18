<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador Welcome
 * Adaptado para trabajar con la BD acumuladores_db
 * Sistema de gestión de tienda de acumuladores
 * Con manejo de errores de conexión a BD y notificaciones mejoradas
 */
class Welcome extends CI_Controller {

    private $db_connected = true;

    public function __construct() {
        parent::__construct();
        
        // Cargar helpers y librerías necesarios
        $this->load->helper('url');
        $this->load->library('session');
		$this->load->library('user_agent');
        
        // Intentar cargar el modelo con manejo de errores
        try {
            $this->load->model('mlogin');
			$this->load->model('Mauth');
			$this->load->model('Mproductos');
			$this->load->model('Mcategorias');
			$this->load->model('Mmultimedia');
        } catch (Exception $e) {
            $this->db_connected = false;
            log_message('error', 'Error al conectar con la base de datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Vista principal de bienvenida
     */
    public function index() {
        // Verificar conexión a BD
        if (!$this->db_connected || !$this->db->conn_id) {
            $this->mostrar_error_bd();
            return;
        }

		// Cargar categorías activas desde la base de datos
        $data['categorias'] = $this->Mcategorias->get_categorias_activas();
        
        // Cargar slides del carousel desde la base de datos
        $data['slides'] = $this->Mmultimedia->get_slides_activos();
        
        
        // Si el usuario ya está logueado, redirigir al dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('pages/index',$data);
        }
        
        // Cargar la vista de bienvenida
        $this->load->view('welcome/index',$data);
    }
    
    /**
     * Mostrar página de error de base de datos
     */
    private function mostrar_error_bd() {
        $db_config = $this->db->database;
        
        $data = array(
            'hostname' => isset($this->db->hostname) ? $this->db->hostname : '127.0.0.1',
            'database' => isset($this->db->database) ? $this->db->database : 'acumuladores_db',
            'port' => '3306'
        );
        
        $this->load->view('errors/db_error', $data);
    }
    
    /**
     * Procesar login (vía AJAX)
     */
    public function login() {
        // Verificar conexión a BD
        if (!$this->db_connected || !$this->db->conn_id) {
            $response = array(
                'success' => false,
                'message' => 'Error de conexión con la base de datos. Por favor, verifica que MySQL esté ejecutándose.',
                'error_type' => 'database'
            );
            echo json_encode($response);
            return;
        }
        
        // Verificar que sea petición POST
        if ($this->input->method() !== 'post') {
            $response = array(
                'success' => false,
                'message' => 'Método no permitido'
            );
            echo json_encode($response);
            return;
        }
        
        // Obtener datos del POST
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        // Validar campos vacíos
        if (empty($username) || empty($password)) {
            $response = array(
                'success' => false,
                'message' => 'Por favor, completa todos los campos'
            );
            echo json_encode($response);
            return;
        }

		// Verificar bloqueo
        if ($this->Mauth->verificar_bloqueo($username)) {
            echo json_encode(['success' => false, 'message' => 'Usuario bloqueado por demasiados intentos.']);
            return;
        }
        
        // Encriptar password con MD5
        $password_encrypted = md5($password);
        
        try {
            // Intentar hacer login usando el modelo mlogin
            $result = $this->mlogin->Ingresar($username, $password_encrypted);
            
            // Verificar resultado del login
            if ($result && is_array($result)) {
                // Login exitoso - Crear sesión
                $this->Mauth->registrar_log_acceso($result['id'], 'login');
                $this->Mauth->crear_sesion_db($result['id']);

                $session_data = array(
                    'user_id' => $result['id'],
                    'user' => $result['usuario'],
                    'nombre' => $result['nombre'],
                    'nombre_solo' => $result['nombre_solo'],
                    'apellidos' => $result['apellidos'],
                    'email' => $result['email'],
                    'carnet' => $result['carnet'],
                    'direccion' => $result['direccion'],
                    'role' => $result['rol'],
                    'rol_original' => $result['rol_original'],
                    'estado' => $result['estado'],
                    'id_cliente' => $result['id_cliente'],
                    'avatar' => $result['avatar'] ? $result['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($result['nombre']) . '&background=random',
                    'logged_in' => true,
                    'login_time' => time()
                );
                
                $this->session->set_userdata($session_data);
                
                // Respuesta exitosa
                $response = array(
                    'success' => true,
                    'message' => 'Login exitoso',
                    'redirect' => base_url('pages/index'),
                    'user' => array(
                        'nombre' => $result['nombre'],
                        'email' => $result['email'],
                        'rol' => $result['rol']
                    )
                );
                
                echo json_encode($response);
            } else {
                // Login fallido
                $this->Mauth->registrar_intento_fallido($username);
                $response = array(
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos'
                );
                echo json_encode($response);
            }
            
        } catch (Exception $e) {
            // Error en el proceso
            log_message('error', 'Error en login: ' . $e->getMessage());
            $response = array(
                'success' => false,
                'message' => 'Error en el servidor. Por favor, intenta nuevamente.'
            );
            echo json_encode($response);
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // Obtener ID de usuario antes de destruir sesión
        $user_id = $this->session->userdata('user_id');
        
        // Registrar cierre de sesión si el usuario está logueado
        if ($user_id && $this->db_connected) {
            try {
                $this->Mauth->registrar_log_acceso($user_id, 'logout');
                $this->Mauth->cerrar_sesion_db($user_id);
            } catch (Exception $e) {
                log_message('error', 'Error al registrar logout: ' . $e->getMessage());
            }
        }
        
        // Destruir sesión
        $this->session->sess_destroy();
        
        // Redirigir a la página de inicio
        redirect('welcome/index');
    }
    
    /**
     * Obtener productos destacados (AJAX)
     */
    public function get_productos_destacados() {
        if (!$this->db_connected) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            return;
        }
        
        try {
            $productos = $this->Mproducto->get_productos_destacados(6);
            echo json_encode(['success' => true, 'productos' => $productos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al cargar productos']);
        }
    }
    
    /**
     * Obtener categorías (AJAX)
     */
    public function get_categorias() {
        if (!$this->db_connected) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            return;
        }
        
        try {
            $categorias = $this->Mcategorias->get_categorias_activas();
            echo json_encode(['success' => true, 'categorias' => $categorias]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al cargar categorías']);
        }
    }
    
    /**
     * Búsqueda de productos (AJAX)
     */
    public function buscar_productos() {
        if (!$this->db_connected) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            return;
        }
        
        $termino = $this->input->post('termino');
        
        if (empty($termino)) {
            echo json_encode(['success' => false, 'message' => 'Ingrese un término de búsqueda']);
            return;
        }
        
        try {
            $productos = $this->Mproducto->buscar_productos($termino);
            echo json_encode(['success' => true, 'productos' => $productos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error en la búsqueda']);
        }
    }
    
    /**
     * Obtener información de la tienda
     */
    public function get_info_tienda() {
        if (!$this->db_connected) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            return;
        }
        
        try {
            $this->load->model('Mtienda');
            $info = $this->Mtienda->get_info_tienda();
            echo json_encode(['success' => true, 'tienda' => $info]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al cargar información']);
        }
    }
    
    /**
     * Verificar disponibilidad de producto
     */
    public function verificar_stock() {
        if (!$this->db_connected) {
            echo json_encode(['success' => false, 'message' => 'Error de conexión']);
            return;
        }
        
        $id_producto = $this->input->post('id_producto');
        
        if (empty($id_producto)) {
            echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
            return;
        }
        
        try {
            $stock = $this->Mproducto->get_stock_producto($id_producto);
            echo json_encode(['success' => true, 'stock' => $stock]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al verificar stock']);
        }
    }
    
    /**
     * Página de catálogo completo
     */
    public function catalogo() {
        if (!$this->db_connected || !$this->db->conn_id) {
            $this->mostrar_error_bd();
            return;
        }
        
        $data['categorias'] = $this->Mcategorias->get_categorias_activas();
        $data['productos'] = $this->Mproductos->get_productos_disponibles();
        
        $this->load->view('welcome/catalogo', $data);
    }
    
    /**
     * Detalle de producto
     */
    public function producto($id_producto) {
        if (!$this->db_connected || !$this->db->conn_id) {
            $this->mostrar_error_bd();
            return;
        }
        
        if (empty($id_producto)) {
            show_404();
            return;
        }
        
        $data['producto'] = $this->Mproducto->get_producto_by_id($id_producto);
        
        if (empty($data['producto'])) {
            show_404();
            return;
        }
        
        $data['productos_relacionados'] = $this->Mproducto->get_productos_relacionados(
            $id_producto, 
            $data['producto']->TB_CATEGORIA_PRODUCTO_IdCategoria
        );
        
        $this->load->view('welcome/producto_detalle', $data);
    }
    
    /**
     * Contacto - Formulario
     */
    public function contacto() {
        $this->load->view('welcome/contacto');
    }
    
    /**
     * Procesar formulario de contacto
     */
    public function enviar_contacto() {
        $nombre = $this->input->post('nombre');
        $email = $this->input->post('email');
        $telefono = $this->input->post('telefono');
        $mensaje = $this->input->post('mensaje');
        
        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($mensaje)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Por favor completa todos los campos obligatorios'
            ]);
            return;
        }
        
        // Aquí puedes agregar lógica para enviar email o guardar en BD
        try {
            // Ejemplo: guardar en tabla de mensajes de contacto
            $data = array(
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'mensaje' => $mensaje,
                'fecha' => date('Y-m-d H:i:s')
            );
            
            // $this->db->insert('TB_CONTACTO', $data);
            
            echo json_encode([
                'success' => true, 
                'message' => '¡Gracias por contactarnos! Te responderemos pronto.'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al enviar el mensaje. Por favor, intenta nuevamente.'
            ]);
        }
    }
}
