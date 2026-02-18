<!--Start sidebar-wrapper-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
  <div class="brand-logo">
    <a href="javascript:void(0);" data-page="dashboard">      
      <h5 class="logo-text">Acumuladores Pro</h5>
    </a>
  </div>
  
  <?php 
  // Obtener el rol del usuario
  $role = isset($user['role']) ? $user['role'] : 'Cliente';
  $rol_original = isset($user['rol_original']) ? $user['rol_original'] : 'CLIENTE';
  ?>
  
  <ul class="sidebar-menu do-nicescrol">
    <li class="sidebar-header text-center">
      <h4>MENÚ</h4>
      <small class="text-white text-xl"><?php echo $rol_original; ?></small>
    </li>
    
    <!-- ========== MENÚ PARA ADMINISTRADOR ========== -->
    <?php if ($role === 'Administrador' || $rol_original === 'ADMIN'): ?>
    
    <!-- Dashboard Admin -->
    <li>
      <a href="javascript:void(0);" class="menu-link active" data-page="dashboard">
        <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
      </a>
    </li>

    <!-- Menú Desplegable: Gestión de Inventario -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-storage"></i>
        <span>Inventario</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="productos">
            <i class="zmdi zmdi-battery-flash"></i> Productos
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="categorias">
            <i class="zmdi zmdi-view-list"></i> Categorías
          </a>
        </li>        
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="stock">
            <i class="zmdi zmdi-storage"></i> Control de Stock
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="movimientos">
            <i class="zmdi zmdi-swap"></i> Movimientos
          </a>
        </li>
      </ul>
    </li>

    <!-- Menú Desplegable: Ventas -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-shopping-cart"></i>
        <span>Ventas</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="nueva_venta">
            <i class="zmdi zmdi-plus-circle"></i> Nueva Venta
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="ventas">
            <i class="zmdi zmdi-money"></i> Historial de Ventas
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="cotizaciones">
            <i class="zmdi zmdi-file-text"></i> Cotizaciones
          </a>
        </li>
       
      </ul>
    </li>

    <!-- Menú Desplegable: Clientes -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-accounts"></i>
        <span>Clientes</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="clientes">
            <i class="zmdi zmdi-accounts-list"></i> Lista de Clientes
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="creditos">
            <i class="zmdi zmdi-card"></i> Créditos
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="historial_compras">
            <i class="zmdi zmdi-time-restore"></i> Historial de Compras
          </a>
        </li>
      </ul>
    </li>

    <!-- Menú Desplegable: Proveedores -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-truck"></i>
        <span>Proveedores</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="proveedores">
            <i class="zmdi zmdi-truck"></i> Proveedores
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="compras">
            <i class="zmdi zmdi-shopping-basket"></i> Compras
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="ordenes_compra">
            <i class="zmdi zmdi-assignment"></i> Órdenes de Compra
          </a>
        </li>
      </ul>
    </li>

    <!-- Caja -->
    <!-- <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-money-box"></i>
        <span>Caja</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="abrir_caja">
            <i class="zmdi zmdi-lock-open"></i> Abrir Caja
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="cerrar_caja">
            <i class="zmdi zmdi-lock"></i> Cerrar Caja
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="movimientos_caja">
            <i class="zmdi zmdi-balance"></i> Movimientos
          </a>
        </li>
      </ul>
    </li> -->

    <!-- Reportes -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-chart"></i>
        <span>Reportes</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="reportes_ventas">
            <i class="zmdi zmdi-trending-up"></i> Ventas
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="reportes_inventario">
            <i class="zmdi zmdi-storage"></i> Inventario
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="reportes_financieros">
            <i class="zmdi zmdi-money-box"></i> Financieros
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="reportes_clientes">
            <i class="zmdi zmdi-accounts-list"></i> Clientes
          </a>
        </li>
      </ul>
    </li>

    <!-- Usuarios del Sistema -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="usuarios">
        <i class="zmdi zmdi-accounts-alt"></i> <span>Usuarios</span>
      </a>
    </li>

    <!-- Configuración -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-settings"></i>
        <span>Configuración</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="config_tienda">
            <i class="zmdi zmdi-store"></i> Datos de la Tienda
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="config_sistema">
            <i class="zmdi zmdi-settings-square"></i> Sistema
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="perfil">
            <i class="zmdi zmdi-face"></i> Mi Perfil
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="respaldos">
            <i class="zmdi zmdi-cloud-upload"></i> Respaldos
          </a>
        </li>
      </ul>
    </li>
    
    <!-- ========== FIN MENÚ ADMINISTRADOR ========== -->
    
    <?php elseif ($role === 'Empleado' || $rol_original === 'VENDEDOR' || $rol_original === 'ALMACEN'): ?>
    
    <!-- ========== MENÚ PARA EMPLEADO/VENDEDOR ========== -->
    
    <!-- Dashboard -->
    <li>
      <a href="javascript:void(0);" class="menu-link active" data-page="dashboard_vendedor">
        <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
      </a>
    </li>

    <!-- Ventas -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-shopping-cart"></i>
        <span>Ventas</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="nueva_venta">
            <i class="zmdi zmdi-plus-circle"></i> Nueva Venta
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="mis_ventas">
            <i class="zmdi zmdi-money"></i> Mis Ventas
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="cotizaciones">
            <i class="zmdi zmdi-file-text"></i> Cotizaciones
          </a>
        </li>
      </ul>
    </li>

    <!-- Productos -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="productos">
        <i class="zmdi zmdi-battery-flash"></i> <span>Productos</span>
      </a>
    </li>

    <!-- Clientes -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="clientes">
        <i class="zmdi zmdi-accounts"></i> <span>Clientes</span>
      </a>
    </li>

    <!-- Caja -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-money-box"></i>
        <span>Caja</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="mi_caja">
            <i class="zmdi zmdi-balance"></i> Mi Caja
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="movimientos_caja">
            <i class="zmdi zmdi-swap"></i> Movimientos
          </a>
        </li>
      </ul>
    </li>

    <!-- Mi Perfil -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="perfil">
        <i class="zmdi zmdi-face"></i> <span>Mi Perfil</span>
      </a>
    </li>
    
    <!-- ========== FIN MENÚ EMPLEADO ========== -->
    
    <?php else: ?>
    
    <!-- ========== MENÚ PARA CLIENTE ========== -->
    
    <!-- Dashboard Cliente -->
    <li>
      <a href="javascript:void(0);" class="menu-link active" data-page="dashboard_cliente">
        <i class="zmdi zmdi-home"></i> <span>Inicio</span>
      </a>
    </li>

    <!-- Catálogo -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="catalogo">
        <i class="zmdi zmdi-view-module"></i> <span>Catálogo</span>
      </a>
    </li>

    <!-- Mis Compras -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-shopping-basket"></i>
        <span>Mis Compras</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="mis_pedidos">
            <i class="zmdi zmdi-assignment"></i> Mis Pedidos
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="historial_compras">
            <i class="zmdi zmdi-time-restore"></i> Historial
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="carrito">
            <i class="zmdi zmdi-shopping-cart"></i> Carrito
            <span class="badge badge-orange badge-pill float-right" id="cart-count">0</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Cotizaciones -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="solicitar_cotizacion">
        <i class="zmdi zmdi-file-text"></i> <span>Solicitar Cotización</span>
      </a>
    </li>

    <!-- Mi Cuenta -->
    <li>
      <a href="javaScript:void();" class="waves-effect">
        <i class="zmdi zmdi-account"></i>
        <span>Mi Cuenta</span>
        <i class="fa fa-angle-left pull-right"></i>
      </a>
      <ul class="sidebar-submenu">
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="perfil">
            <i class="zmdi zmdi-face"></i> Mi Perfil
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" onclick="cambiarPassword()">
            <i class="zmdi zmdi-lock"></i> Cambiar Contraseña
          </a>
        </li>
        <li>
          <a href="javascript:void(0);" class="menu-link" data-page="mis_datos">
            <i class="zmdi zmdi-edit"></i> Mis Datos
          </a>
        </li>
      </ul>
    </li>

    <!-- Ayuda -->
    <li>
      <a href="javascript:void(0);" class="menu-link" data-page="ayuda">
        <i class="zmdi zmdi-help"></i> <span>Ayuda</span>
      </a>
    </li>
    
    <!-- ========== FIN MENÚ CLIENTE ========== -->
    
    <?php endif; ?>
    
    <!-- Cerrar Sesión (para todos) -->
    <li class="sidebar-divider"></li>
    <li>
      <a href="javascript:void(0);" onclick="logout()">
        <i class="zmdi zmdi-power"></i> <span>Cerrar Sesión</span>
      </a>
    </li>

  </ul>
</div>
<!--End sidebar-wrapper-->

<style>
/* ============================================
   ESTILOS SIDEBAR - TEMA ACUMULADORES
   Naranja y Grises
   ============================================ */

/* Sidebar principal */
#sidebar-wrapper {
  background: linear-gradient(135deg, #1E1E1E 0%, #2A2A2A 100%);
  border-right: 3px solid #FF6B35;
}

/* Estilos para el submenú */
.sidebar-submenu {
  display: none;
  list-style: none;
  padding-left: 20px;
  background-color: rgba(0, 0, 0, 0.3);
  border-left: 2px solid #FF6B35;
  margin-left: 10px;
}

.sidebar-submenu li {
  margin: 0;
}

.sidebar-submenu li a {
  padding: 10px 20px;
  display: block;
  color: #F5F5F5;
  font-size: 13px;
  transition: all 0.3s;
  border-left: 3px solid transparent;
}

.sidebar-submenu li a:hover {
  background-color: rgba(255, 107, 53, 0.1);
  padding-left: 25px;
  border-left-color: #FF6B35;
  color: #FF8C61;
}

.sidebar-submenu li a i {
  margin-right: 10px;
  font-size: 16px;
  color: #FF6B35;
}

.sidebar-divider {
  height: 2px;
  background: linear-gradient(to right, transparent, #FF6B35, transparent);
  margin: 15px 10px;
}

.sidebar-header {
  background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
  padding: 5px;  
  border-radius: 8px;
  margin: 10px;
}

.sidebar-header h4 {
  color: #fff;
  font-weight: 700;
  margin: 0;
  font-size: 16px;
  letter-spacing: 2px;
}

.sidebar-header small {
  font-size: 12px;
  display: block;
  margin-top: 5px;
  opacity: 0.9;
  font-weight: 600;
  color: #FFB84D;
}

.badge-pill {
  padding: 0.25em 0.6em;
  border-radius: 10rem;
}

.badge-orange {
  background-color: #FF6B35;
  color: white;
}

/* Animación para active */
.sidebar-menu li.active > a {
  background: linear-gradient(90deg, rgba(255, 107, 53, 0.2) 0%, rgba(255, 107, 53, 0.05) 100%);
  border-left: 4px solid #FF6B35;
  color: #FF8C61;
}

/* Hover effect */
.sidebar-menu li a:hover {
  background-color: rgba(255, 107, 53, 0.1);
  color: #FF8C61;
}

.sidebar-menu li a {
  color: #F5F5F5;
  transition: all 0.3s ease;
}

.sidebar-menu li a i {
  color: #FF6B35;
  margin-right: 10px;
  transition: all 0.3s ease;
}

.sidebar-menu li a:hover i {
  color: #FF8C61;
  transform: scale(1.1);
}

/* Estilo especial para el logo */
.brand-logo {
  padding: 20px;
  text-align: center;
  border-bottom: 2px solid #FF6B35;
  background: linear-gradient(135deg, #2A2A2A 0%, #1E1E1E 100%);
}

.brand-logo a {
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.brand-logo .logo-icon {
  width: 50px;
  height: 50px;
  margin-bottom: 10px;
  filter: drop-shadow(0 0 10px rgba(255, 107, 53, 0.5));
}

.brand-logo .logo-text {
  color: #F5F5F5;
  font-weight: 700;
  font-size: 20px;
  margin: 0;
  letter-spacing: 2px;
  text-transform: uppercase;
  background: linear-gradient(135deg, #FF6B35 0%, #FFB84D 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Badge del carrito */
#cart-count {
  font-size: 11px;
  padding: 3px 7px;
  margin-left: 5px;
  animation: pulse-badge 2s infinite;
}

@keyframes pulse-badge {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(255, 107, 53, 0.7);
  }
  50% {
    box-shadow: 0 0 0 5px rgba(255, 107, 53, 0);
  }
}

/* Flecha del submenu */
.fa-angle-left {
  transition: transform 0.3s ease;
}

.fa-angle-down {
  transform: rotate(-90deg);
}

/* Scrollbar personalizado */
.sidebar-menu::-webkit-scrollbar {
  width: 6px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: #1E1E1E;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: #FF6B35;
  border-radius: 3px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
  background: #E85A2A;
}

/* Responsive */
@media (max-width: 768px) {
  .brand-logo .logo-text {
    font-size: 16px;
  }
  
  .sidebar-header h4 {
    font-size: 14px;
  }
}
</style>

<script>
// Script para manejar el menú desplegable
$(document).ready(function() {
  // Toggle de submenús
  $('.sidebar-menu > li > a[href="javaScript:void();"]').on('click', function(e) {
    e.preventDefault();
    
    var $submenu = $(this).next('.sidebar-submenu');
    
    // Cerrar otros submenús abiertos
    $('.sidebar-submenu').not($submenu).slideUp();
    $('.sidebar-menu > li > a .fa-angle-left')
      .not($(this).find('.fa-angle-left'))
      .removeClass('fa-angle-down')
      .addClass('fa-angle-left');
    
    // Toggle del submenú actual
    $submenu.slideToggle();
    $(this).find('.fa-angle-left').toggleClass('fa-angle-down');
  });
  
  // Manejar click en links del menú
  $('.menu-link').on('click', function(e) {
    // Remover active de todos
    $('.sidebar-menu li').removeClass('active');
    
    // Agregar active al clickeado
    $(this).parent('li').addClass('active');
  });
  
  // Actualizar contador del carrito
  function actualizarContadorCarrito() {
    $.ajax({
      url: baseUrl + 'carrito/get_cart_count',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if(response.success && response.count > 0) {
          $('#cart-count').text(response.count);
          $('#cart-count').show();
        } else {
          $('#cart-count').hide();
        }
      },
      error: function() {
        console.log('Error al actualizar contador del carrito');
      }
    });
  }
  
  // Actualizar cada 30 segundos
  actualizarContadorCarrito();
  setInterval(actualizarContadorCarrito, 30000);
});

// Función para cambiar contraseña
function cambiarPassword() {
  Swal.fire({
    title: 'Cambiar Contraseña',
    html: `
      <input type="password" id="password_actual" class="swal2-input" placeholder="Contraseña actual">
      <input type="password" id="password_nueva" class="swal2-input" placeholder="Nueva contraseña">
      <input type="password" id="password_confirmar" class="swal2-input" placeholder="Confirmar contraseña">
    `,
    confirmButtonText: 'Cambiar',
    confirmButtonColor: '#FF6B35',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const actual = document.getElementById('password_actual').value;
      const nueva = document.getElementById('password_nueva').value;
      const confirmar = document.getElementById('password_confirmar').value;
      
      if (!actual || !nueva || !confirmar) {
        Swal.showValidationMessage('Todos los campos son requeridos');
        return false;
      }
      
      if (nueva !== confirmar) {
        Swal.showValidationMessage('Las contraseñas no coinciden');
        return false;
      }
      
      if (nueva.length < 6) {
        Swal.showValidationMessage('La contraseña debe tener al menos 6 caracteres');
        return false;
      }
      
      return { actual, nueva };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Aquí iría la llamada AJAX para cambiar la contraseña
      $.ajax({
        url: baseUrl + 'usuario/cambiar_password',
        type: 'POST',
        data: {
          password_actual: result.value.actual,
          password_nueva: result.value.nueva
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: '¡Éxito!',
              text: 'Contraseña cambiada correctamente',
              confirmButtonColor: '#FF6B35'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message,
              confirmButtonColor: '#FF6B35'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al cambiar la contraseña',
            confirmButtonColor: '#FF6B35'
          });
        }
      });
    }
  });
}

// Función para cerrar sesión
function logout() {
  Swal.fire({
    title: '¿Cerrar sesión?',
    text: '¿Estás seguro de que deseas salir?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#FF6B35',
    cancelButtonColor: '#3D3D3D',
    confirmButtonText: 'Sí, salir',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
       window.location.href = '<?php echo base_url("welcome/logout"); ?>';
    }
  });
}
</script>
