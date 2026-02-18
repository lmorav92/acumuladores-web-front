<!--Start topbar header-->
<header class="topbar-nav">
  <nav class="navbar navbar-expand fixed-top">
    <ul class="navbar-nav mr-auto align-items-center">
      <li class="nav-item">
        <a class="nav-link toggle-menu" href="javascript:void();">
          <i class="icon-menu menu-icon"></i>
        </a>
      </li>
      <li class="nav-item">
        <form class="search-bar">
          <input type="text" class="form-control" placeholder="Buscar productos, clientes...">
          <a href="javascript:void();"><i class="icon-magnifier"></i></a>
        </form>
      </li>
    </ul>
     
    <ul class="navbar-nav align-items-center right-nav-link">
      <!-- Notificaciones -->
      <li class="nav-item dropdown dropdown-lg">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="javascript:void();">
          <i class="fa fa-bell-o"></i>
          <span class="badge badge-primary badge-up">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Notificaciones
              <span class="badge badge-primary">3</span>
            </li>
            <li class="list-group-item">
              <a href="javascript:void();">
                <div class="media">
                  <i class="zmdi zmdi-storage text-warning mr-3" style="font-size: 30px;"></i>
                  <div class="media-body">
                    <h6 class="msg-name">Stock Bajo
                      <span class="msg-time float-right text-muted">Hace 5 min</span>
                    </h6>
                    <p class="msg-info">Batería 12V-45Ah con stock bajo</p>
                  </div>
                </div>
              </a>
            </li>
            <li class="list-group-item">
              <a href="javascript:void();">
                <div class="media">
                  <i class="zmdi zmdi-shopping-cart text-success mr-3" style="font-size: 30px;"></i>
                  <div class="media-body">
                    <h6 class="msg-name">Nueva Venta
                      <span class="msg-time float-right text-muted">Hace 15 min</span>
                    </h6>
                    <p class="msg-info">Venta #1234 completada</p>
                  </div>
                </div>
              </a>
            </li>
            <li class="list-group-item">
              <a href="javascript:void();">
                <div class="media">
                  <i class="zmdi zmdi-truck text-info mr-3" style="font-size: 30px;"></i>
                  <div class="media-body">
                    <h6 class="msg-name">Pedido Recibido
                      <span class="msg-time float-right text-muted">Hace 1 hora</span>
                    </h6>
                    <p class="msg-info">Llegó el pedido de Proveedor XYZ</p>
                  </div>
                </div>
              </a>
            </li>
            <li class="list-group-item text-center">
              <a href="javascript:void();" class="text-primary">Ver todas</a>
            </li>
          </ul>
        </div>
      </li>
      
      <!-- Carrito (solo para clientes) -->
      <?php if (isset($user['role']) && $user['role'] === 'Cliente'): ?>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" data-page="carrito">
          <i class="zmdi zmdi-shopping-cart"></i>
          <span class="badge badge-primary badge-up cart-count">0</span>
        </a>
      </li>
      <?php endif; ?>
      
      <!-- Usuario -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
          <span class="user-profile">
            <img src="<?= isset($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre']) . '&background=FF6B35&color=fff' ?>" 
                 class="img-circle" alt="user avatar">
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="dropdown-item user-details">
            <a href="javaScript:void();">
              <div class="media">
                <div class="avatar">
                  <img class="align-self-start mr-3" 
                       src="<?= isset($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre']) . '&background=FF6B35&color=fff' ?>" 
                       alt="user avatar">
                </div>
                <div class="media-body">
                  <h6 class="mt-2 user-title"><?= isset($user['nombre']) ? $user['nombre'] : 'Usuario' ?></h6>
                  <p class="user-subtitle"><?= isset($user['email']) ? $user['email'] : '' ?></p>
                  <p class="user-role"><?= isset($user['role']) ? $user['role'] : 'Usuario' ?></p>
                </div>
              </div>
            </a>
          </li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item">
            <a href="javascript:void(0);" class="menu-link" data-page="perfil">
              <i class="icon-user mr-2"></i> Mi Perfil
            </a>
          </li>
          <li class="dropdown-divider"></li>
          <li class="dropdown-item">
            <a href="javascript:void(0);" onclick="logout()">
              <i class="icon-power mr-2"></i> Cerrar Sesión
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>
<!--End topbar header-->

<style>
/* ============================================
   NAVBAR - TEMA ACUMULADORES
   Naranja y Grises
   ============================================ */

.topbar-nav {
  background: linear-gradient(135deg, #1E1E1E 0%, #2A2A2A 100%);
  border-bottom: 3px solid #FF6B35;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
}

.navbar {
  background: transparent !important;
  padding: 0.5rem 1rem;
}

/* Toggle Menu */
.toggle-menu {
  color: #FF6B35 !important;
  font-size: 24px;
  transition: all 0.3s ease;
}

.toggle-menu:hover {
  color: #FF8C61 !important;
  transform: scale(1.1);
}

/* Search Bar */
.search-bar {
  position: relative;
  max-width: 500px;
  margin-left: 20px;
}

.search-bar input {
  background: rgba(61, 61, 61, 0.8);
  border: 1px solid #555;
  color: #F5F5F5;
  border-radius: 25px;
  padding: 8px 45px 8px 20px;
  width: 100%;
  transition: all 0.3s ease;
}

.search-bar input:focus {
  background: rgba(61, 61, 61, 0.95);
  border-color: #FF6B35;
  box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
  outline: none;
}

.search-bar input::placeholder {
  color: #999;
}

.search-bar a {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #FF6B35;
  font-size: 18px;
}

/* Nav Links */
.right-nav-link .nav-link {
  color: #F5F5F5 !important;
  font-size: 20px;
  padding: 0.5rem 1rem;
  position: relative;
  transition: all 0.3s ease;
}

.right-nav-link .nav-link:hover {
  color: #FF6B35 !important;
  transform: scale(1.1);
}

/* Badges */
.badge-up {
  position: absolute;
  top: 5px;
  right: 5px;
  padding: 3px 6px;
  font-size: 10px;
  border-radius: 10px;
}

.badge-primary {
  background: #FF6B35;
}

/* User Profile */
.user-profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #FF6B35;
  transition: all 0.3s ease;
}

.user-profile:hover img {
  border-color: #FF8C61;
  box-shadow: 0 0 15px rgba(255, 107, 53, 0.5);
}

/* Dropdown Menu */
.dropdown-menu {
  background: #2A2A2A;
  border: 2px solid #FF6B35;
  border-radius: 10px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
  padding: 0;
  margin-top: 10px;
}

.dropdown-menu::before {
  content: '';
  position: absolute;
  top: -8px;
  right: 20px;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-bottom: 8px solid #FF6B35;
}

.list-group-item {
  background: #2A2A2A;
  border: none;
  border-bottom: 1px solid rgba(255, 107, 53, 0.2);
  color: #F5F5F5;
  transition: all 0.3s ease;
}

.list-group-item:hover {
  background: rgba(255, 107, 53, 0.1);
  transform: translateX(5px);
}

.list-group-item:first-child {
  background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
  color: white;
  font-weight: 600;
  border-radius: 8px 8px 0 0;
}

.list-group-item:last-child {
  border-radius: 0 0 8px 8px;
}

/* Dropdown Item */
.dropdown-item {
  color: #F5F5F5;
  padding: 10px 20px;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background: rgba(255, 107, 53, 0.1);
  color: #FF6B35;
}

.dropdown-item a {
  color: inherit;
  text-decoration: none;
  display: flex;
  align-items: center;
}

.dropdown-item i {
  color: #FF6B35;
  margin-right: 10px;
}

/* User Details */
.user-details {
  background: rgba(255, 107, 53, 0.1);
  padding: 15px;
}

.user-details .avatar img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid #FF6B35;
}

.user-title {
  color: #F5F5F5;
  margin: 0;
  font-weight: 600;
  font-size: 14px;
}

.user-subtitle {
  color: #CCCCCC;
  margin: 2px 0;
  font-size: 12px;
}

.user-role {
  display: inline-block;
  background: #FF6B35;
  color: white;
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  margin-top: 5px;
}

/* Messages */
.msg-name {
  color: #F5F5F5;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 5px;
}

.msg-info {
  color: #CCCCCC;
  font-size: 12px;
  margin: 0;
}

.msg-time {
  font-size: 11px;
  color: #999;
}

/* Dropdown Large */
.dropdown-lg {
  min-width: 350px;
}

.dropdown-lg .dropdown-menu {
  max-height: 400px;
  overflow-y: auto;
}

/* Scrollbar del dropdown */
.dropdown-lg .dropdown-menu::-webkit-scrollbar {
  width: 6px;
}

.dropdown-lg .dropdown-menu::-webkit-scrollbar-track {
  background: #1E1E1E;
}

.dropdown-lg .dropdown-menu::-webkit-scrollbar-thumb {
  background: #FF6B35;
  border-radius: 3px;
}

/* Responsive */
@media (max-width: 768px) {
  .search-bar {
    display: none;
  }
  
  .dropdown-lg {
    min-width: 280px;
  }
  
  .right-nav-link .nav-link {
    font-size: 18px;
    padding: 0.5rem 0.7rem;
  }
}

/* Animación del contador del carrito */
@keyframes cart-pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
}

.cart-count {
  animation: cart-pulse 2s infinite;
}
</style>

<script>
$(document).ready(function() {
  // Actualizar contador de notificaciones
  function actualizarNotificaciones() {
    $.ajax({
      url: baseUrl + 'notificaciones/get_count',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success && response.count > 0) {
          $('.fa-bell-o').next('.badge').text(response.count).show();
        }
      }
    });
  }
  
  // Actualizar cada minuto
  actualizarNotificaciones();
  setInterval(actualizarNotificaciones, 60000);
});
</script>
