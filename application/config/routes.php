<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
*/

/*
| -------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN
| -------------------------------------------------------------------------
*/
$route['login'] = 'welcome/login';
$route['logout'] = 'welcome/logout';

// =========================================================================
// DASHBOARD - API ENDPOINTS (DEBEN IR PRIMERO)
// =========================================================================
// Estas rutas apuntan al controlador Dashboard y devuelven JSON
// NO deben ser interceptadas por Pages

$route['dashboard/estadisticas'] = 'dashboard/estadisticas';
$route['dashboard/estadisticas_mensuales'] = 'dashboard/estadisticas_mensuales';
$route['dashboard/estadisticas_dia/(:any)'] = 'dashboard/estadisticas_dia/$1';
$route['dashboard/estadisticas_dia'] = 'dashboard/estadisticas_dia';

$route['dashboard/turnos_recientes'] = 'dashboard/turnos_recientes';

$route['dashboard/clientes_recientes'] = 'dashboard/clientes_recientes';
$route['dashboard/clientes_frecuentes'] = 'dashboard/clientes_frecuentes';
$route['dashboard/top_clientes'] = 'dashboard/top_clientes';

$route['dashboard/actividad_reciente'] = 'dashboard/actividad_reciente';

$route['dashboard/productos_top'] = 'dashboard/productos_top';
$route['dashboard/productos_stock_bajo'] = 'dashboard/productos_stock_bajo';
$route['dashboard/productos_sin_stock'] = 'dashboard/productos_sin_stock';
$route['dashboard/alertas_stock'] = 'dashboard/alertas_stock';

$route['dashboard/barberos_performance'] = 'dashboard/barberos_performance';

$route['dashboard/ingresos_mensuales'] = 'dashboard/ingresos_mensuales';
$route['dashboard/comparativo_mensual'] = 'dashboard/comparativo_mensual';

$route['dashboard/exportar'] = 'dashboard/exportar';

// Layout principal
$route['dashboard'] = 'layouts/main';

// Catch-all AL FINAL
$route['dashboard/(:any)'] = 'pages/load/$1';


/*
| -------------------------------------------------------------------------
| RUTAS DE API (Notificaciones)
| -------------------------------------------------------------------------
*/
$route['api/notifications'] = 'welcome/get_notifications';

/*
| -------------------------------------------------------------------------
| RUTAS DEL DASHBOARD USUARIO (AGREGAR ESTAS)
| -------------------------------------------------------------------------
*/

$route['dashboard'] = 'dashboard/index';
$route['dashboard/index'] = 'dashboard/index';



// Rutas para Clientes
$route['clientes'] = 'clientes/index';
$route['clientes/list'] = 'clientes/list';
$route['clientes/save'] = 'clientes/save';
$route['clientes/delete/(:num)'] = 'clientes/delete/$1';

// Rutas para Estado de Turnos
$route['estadoturnos'] = 'estadoturnos/index';
$route['estadoturnos/list'] = 'estadoturnos/list';
$route['estadoturnos/getTurnos'] = 'estadoturnos/getTurnos';
$route['estadoturnos/save'] = 'estadoturnos/save';
$route['estadoturnos/delete/(:num)'] = 'estadoturnos/delete/$1';

// Rutas para Preferencias
$route['preferencias/getTemaActual'] = 'preferencias/getTemaActual';
$route['preferencias/updateTema'] = 'preferencias/updateTema';
$route['preferencias/list'] = 'preferencias/list';
$route['preferencias/save'] = 'preferencias/save';
$route['preferencias/delete/(:num)'] = 'preferencias/delete/$1';
$route['preferencias/getUsuarios'] = 'preferencias/getUsuarios';

// Rutas de Turnos (API JSON)
$route['turnos'] = 'turnos/index';
$route['turnos/resumen_mes'] = 'turnos/resumen_mes';
$route['turnos/turnos_dia'] = 'turnos/turnos_dia';
$route['turnos/reservar'] = 'turnos/reservar';
$route['turnos/detalle/(:num)'] = 'turnos/detalle/$1';
$route['turnos/cambiar_estado'] = 'turnos/cambiar_estado';
$route['turnos/lista_clientes'] = 'turnos/lista_clientes';
$route['turnos/cancelar/(:num)'] = 'turnos/cancelar/$1';
$route['turnos/eliminar/(:num)'] = 'turnos/eliminar/$1';
$route['turnos/actualizar_estados'] = 'turnos/actualizar_estados';
$route['turnos/turnos_cliente/(:num)'] = 'turnos/turnos_cliente/$1';
$route['turnos/estadisticas'] = 'turnos/estadisticas';
$route['turnos/mis_turnos_lista'] = 'turnos/mis_turnos_lista';
$route['turnos/historial_lista'] = 'turnos/historial_lista';
$route['turnos/exportar_excel'] = 'turnos/exportar_excel';
$route['turnos/exportar_pdf'] = 'turnos/exportar_pdf';


$route['perfil'] = 'perfil/index';
$route['perfil/actualizar_usuario'] = 'perfil/actualizar_usuario';
$route['perfil/actualizar_preferencias'] = 'perfil/actualizar_preferencias';
$route['perfil/actualizar_avatar'] = 'perfil/actualizar_avatar';
$route['perfil/get_usuario_info'] = 'perfil/get_usuario_info';
$route['perfil/sesiones_activas'] = 'perfil/sesiones_activas';
$route['perfil/historial_acceso'] = 'perfil/historial_acceso';
$route['perfil/cerrar_sesion'] = 'perfil/cerrar_sesion';


$route['calendario'] = 'calendario/index';

/*
| -------------------------------------------------------------------------
| RUTAS DE TIENDA DE PRODUCTOS
| -------------------------------------------------------------------------
*/
$route['tienda'] = 'tienda/index';
$route['tienda/productos'] = 'tienda/listar_productos';
$route['tienda/producto/(:num)'] = 'tienda/detalle/$1';
$route['tienda/categorias'] = 'tienda/listar_categorias';
$route['tienda/generar_qr'] = 'tienda/generar_qr';
$route['tienda/procesar_pago'] = 'tienda/procesar_pago';


// Rutas para Productos
$route['productos/getTemaActual'] = 'productos/getTemaActual';
$route['productos/updateTema'] = 'productos/updateTema';
$route['productos/list'] = 'productos/list';
$route['productos/save'] = 'productos/save';
$route['productos/delete/(:num)'] = 'productos/delete/$1';
$route['productos/getUsuarios'] = 'productos/getUsuarios';

// =============================================================================
// RUTAS DE CATEGORÍAS  PRODUCTOS
// =============================================================================
$route['categorias/list'] = 'categorias/list';
$route['categorias/save'] = 'categorias/save';
$route['categorias/delete/(:num)'] = 'categorias/delete/$1';
$route['categorias/getById/(:num)'] = 'categorias/getById/$1';


// =============================================================================
// RUTAS DE VENTAS
// =============================================================================
$route['ventas/list'] = 'ventas/list';
$route['ventas/save'] = 'ventas/save';
$route['ventas/delete/(:num)'] = 'ventas/delete/$1';
$route['ventas/getById/(:num)'] = 'ventas/getById/$1';
$route['ventas/cambiarEstado'] = 'ventas/cambiarEstado';
$route['ventas/estadisticas'] = 'ventas/estadisticas';
$route['ventas/byFecha'] = 'ventas/byFecha';
$route['ventas/masVendidos'] = 'ventas/masVendidos';
// ⭐ NUEVAS RUTAS PARA SISTEMA DE COMPRAS
$route['ventas/registrar_compra_tienda'] = 'ventas/registrar_compra_tienda';
$route['ventas/confirmar_compra'] = 'ventas/confirmar_compra'; // ⭐ NUEVA


$route['carrito'] = 'carrito/index';
$route['carrito/save'] = 'carrito/agregar';

/*

/*
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
