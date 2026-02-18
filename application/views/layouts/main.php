<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content="Sistema de Gestión de Acumuladores"/>
  <meta name="author" content=""/>
  <title>Acumuladores Pro - Panel de Control</title>
  
  <!-- Base URL para JavaScript -->
  <script>
    var baseUrl = '<?= base_url() ?>';
  </script>
  
  <!-- loader-->
  <link href="<?= base_url('ui/assets/css/pace.min.css') ?>" rel="stylesheet"/>
  <script src="<?= base_url('ui/assets/js/pace.min.js') ?>"></script>
  
  <!--favicon-->
  <link rel="icon" href="<?= base_url('ui/assets/images/logo.png') ?>" type="image/x-icon">
  
  <!-- Vector CSS -->
  <link href="<?= base_url('ui/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') ?>" rel="stylesheet"/>
  
  <!-- simplebar CSS-->
  <link href="<?= base_url('ui/assets/plugins/simplebar/css/simplebar.css') ?>" rel="stylesheet"/>
  
  <!-- Icon Font Stylesheet -->
  <link href="<?= base_url('ui/assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
  
  <!-- Bootstrap core CSS-->
  <link href="<?= base_url('ui/assets/css/bootstrap.min.css') ?>" rel="stylesheet"/>
  
  <!-- animate CSS-->
  <link href="<?= base_url('ui/assets/css/animate.css') ?>" rel="stylesheet" type="text/css"/>
  
  <!-- Icons CSS-->
  <link href="<?= base_url('ui/assets/css/icons.css') ?>" rel="stylesheet" type="text/css"/>
  
  <!-- Sidebar CSS-->
  <link href="<?= base_url('ui/assets/css/sidebar-menu.css') ?>" rel="stylesheet"/>
  
  <!-- Custom Style-->
  <link href="<?= base_url('ui/assets/css/app-style.css') ?>" rel="stylesheet"/>
  <link href="<?= base_url('ui/assets/css/sweetalert2.min.css') ?>" rel="stylesheet"/>
  <link href="<?= base_url('ui/assets/css/fix_inputs_css.css') ?>" rel="stylesheet"/>
  <link href="<?= base_url('ui/assets/css/themes-acumuladores.css') ?>" rel="stylesheet"/>
  
  <style>
    /* ============================================
       TEMA PRINCIPAL - ACUMULADORES
       Naranja y Grises
       ============================================ */
    :root {
      --primary: #FF6B35;
      --primary-dark: #E85A2A;
      --primary-light: #FF8C61;
      --secondary: #3D3D3D;
      --dark: #1E1E1E;
      --dark-alt: #2A2A2A;
      --light: #F5F5F5;
      --light-gray: #CCCCCC;
      --accent: #FFB84D;
      --success: #28A745;
      --danger: #DC3545;
      --warning: #FFC107;
    }
    
    body {
      background: linear-gradient(135deg, #1E1E1E 0%, #2A2A2A 100%);
      color: #F5F5F5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Content Wrapper */
    .content-wrapper {
      background: transparent;
      min-height: 100vh;
    }
    
    /* Cards */
    .card {
      background: rgba(42, 42, 42, 0.95);
      border: 1px solid rgba(255, 107, 53, 0.2);
      border-radius: 12px;
      color: #F5F5F5;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      border-color: #FF6B35;
      box-shadow: 0 8px 30px rgba(255, 107, 53, 0.2);
      transform: translateY(-2px);
    }
    
    .card-header {
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      color: white;
      border-bottom: none;
      border-radius: 12px 12px 0 0 !important;
      font-weight: 600;
      padding: 15px 20px;
    }
    
    .card-title {
      color: #F5F5F5;
      font-weight: 600;
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      border: none;
      color: white;
      font-weight: 600;
      padding: 10px 25px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, #FF8C61 0%, #FF6B35 100%);
      transform: translateY(-2px);
      box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
    }
    
    .btn-secondary {
      background: #3D3D3D;
      border: 1px solid #555;
      color: #F5F5F5;
    }
    
    .btn-secondary:hover {
      background: #4A4A4A;
      border-color: #FF6B35;
    }
    
    /* Tables */
    .table {
      color: #F5F5F5;
      background: rgba(42, 42, 42, 0.5);
    }
    
    .table thead th {
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      color: white;
      border: none;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 1px;
      padding: 12px;
    }
    
    .table tbody tr {
      background: rgba(42, 42, 42, 0.3);
      border-bottom: 1px solid rgba(255, 107, 53, 0.1);
      transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
      background: rgba(255, 107, 53, 0.1);
      transform: scale(1.01);
    }
    
    .table tbody td {
      padding: 12px;
      vertical-align: middle;
    }
    
    /* Forms */
    .form-control {
      background: rgba(61, 61, 61, 0.8);
      border: 1px solid #555;
      color: #F5F5F5;
      border-radius: 8px;
      padding: 10px 15px;
    }
    
    .form-control:focus {
      background: rgba(61, 61, 61, 0.9);
      border-color: #FF6B35;
      color: #F5F5F5;
      box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .form-control::placeholder {
      color: #999;
    }
    
    .form-label {
      color: #F5F5F5;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    /* Badges */
    .badge-primary {
      background: #FF6B35;
    }
    
    .badge-success {
      background: #28A745;
    }
    
    .badge-warning {
      background: #FFC107;
      color: #1E1E1E;
    }
    
    .badge-danger {
      background: #DC3545;
    }
    
    .badge-secondary {
      background: #3D3D3D;
    }
    
    /* Alerts */
    .alert {
      border-radius: 10px;
      border: none;
    }
    
    .alert-success {
      background: rgba(40, 167, 69, 0.2);
      border-left: 4px solid #28A745;
      color: #4ade80;
    }
    
    .alert-danger {
      background: rgba(220, 53, 69, 0.2);
      border-left: 4px solid #DC3545;
      color: #f87171;
    }
    
    .alert-warning {
      background: rgba(255, 193, 7, 0.2);
      border-left: 4px solid #FFC107;
      color: #fbbf24;
    }
    
    .alert-info {
      background: rgba(255, 107, 53, 0.2);
      border-left: 4px solid #FF6B35;
      color: #FF8C61;
    }
    
    /* Modal */
    .modal-content {
      background: #2A2A2A;
      border: 2px solid #FF6B35;
      border-radius: 15px;
      color: #F5F5F5;
    }
    
    .modal-header {
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      border-bottom: none;
      border-radius: 13px 13px 0 0;
    }
    
    .modal-title {
      color: white;
      font-weight: 600;
    }
    
    .modal-footer {
      border-top: 1px solid rgba(255, 107, 53, 0.2);
    }
    
    /* Loader */
    .spinner-border {
      border-color: #FF6B35;
      border-right-color: transparent;
    }
    
    /* Page Title */
    .page-title {
      color: #F5F5F5;
      font-weight: 700;
      font-size: 28px;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 3px solid #FF6B35;
      display: inline-block;
    }
    
    /* Stats Cards */
    .stats-card {
      background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(42, 42, 42, 0.9) 100%);
      border: 1px solid rgba(255, 107, 53, 0.3);
      border-radius: 12px;
      padding: 20px;
      transition: all 0.3s ease;
    }
    
    .stats-card:hover {
      border-color: #FF6B35;
      box-shadow: 0 8px 30px rgba(255, 107, 53, 0.3);
      transform: translateY(-5px);
    }
    
    .stats-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: white;
      margin-bottom: 15px;
      box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }
    
    .stats-value {
      font-size: 32px;
      font-weight: 700;
      color: #FF6B35;
      margin-bottom: 5px;
    }
    
    .stats-label {
      color: #CCCCCC;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }
    
    ::-webkit-scrollbar-track {
      background: #1E1E1E;
    }
    
    ::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
      border-radius: 5px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(135deg, #FF8C61 0%, #FF6B35 100%);
    }
  </style>
</head>

<body class="bg-theme bg-theme-acumuladores">

<!-- Start wrapper-->
<div id="wrapper">
 
  <!-- SIDEBAR -->
  <?php $this->load->view('layouts/sidebar'); ?>
  
  <!-- NAVBAR -->
  <?php $this->load->view('layouts/navbar'); ?>
  
  <div class="clearfix"></div>
  
  <!-- CONTENIDO DINÁMICO -->
  <div class="content-wrapper">
    <div class="container-fluid">
      
      <!-- Aquí se cargará el contenido dinámicamente -->
      <div id="dynamic-content">
        <!-- Cargar dashboard por defecto según el rol -->
        <?php 
        $role = isset($user['role']) ? $user['role'] : 'Cliente';
        
        if ($role === 'Administrador' || (isset($user['rol_original']) && $user['rol_original'] === 'ADMIN')) {
            $this->load->view('pages/dashboard');
        } elseif ($role === 'Empleado' || (isset($user['rol_original']) && in_array($user['rol_original'], ['VENDEDOR', 'ALMACEN']))) {
            $this->load->view('pages/dashboard_vendedor');
        } else {
            $this->load->view('pages/dashboard_cliente');
        }
        ?>
      </div>
      
      <!--start overlay-->
      <div class="overlay toggle-menu"></div>
      <!--end overlay-->
      
    </div>
    <!-- End container-fluid-->
  </div>
  <!--End content-wrapper-->
  
  <!--Start Back To Top Button-->
  <a href="javaScript:void();" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
  </a>
  <!--End Back To Top Button-->
  
  <!-- FOOTER -->
  <?php $this->load->view('layouts/footer'); ?>
  
  <!--start color switcher-->
  <?php $this->load->view('layouts/color_switcher'); ?>
  <!--end color switcher-->
   
</div><!--End wrapper-->

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('ui/assets/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('ui/assets/js/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('ui/assets/js/popper.min.js') ?>"></script>
<script src="<?= base_url('ui/assets/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('ui/assets/plugins/Chart.js/Chart.min.js') ?>"></script>

<!-- simplebar js -->
<script src="<?= base_url('ui/assets/plugins/simplebar/js/simplebar.js') ?>"></script>

<!-- sidebar-menu js -->
<script src="<?= base_url('ui/assets/js/sidebar-menu.js') ?>"></script>

<!-- loader scripts -->
<script src="<?= base_url('ui/assets/js/jquery.loading-indicator.js') ?>"></script>

<!-- Custom scripts -->
<script src="<?= base_url('ui/assets/js/app-script.js') ?>"></script>

<!-- Script para navegación SPA -->
<script src="<?= base_url('ui/assets/js/spa-navigation.js') ?>"></script>
<script src="<?= base_url('ui/assets/js/color_switcher.js') ?>"></script>

<!-- Configuración de SweetAlert2 con tema naranja -->
<script>
const SwalConfig = {
  confirmButtonColor: '#FF6B35',
  cancelButtonColor: '#3D3D3D',
  background: '#2A2A2A',
  color: '#F5F5F5'
};

// Función helper para SweetAlert con estilos
window.showAlert = function(type, title, text) {
  Swal.fire({
    icon: type,
    title: title,
    text: text,
    ...SwalConfig
  });
};

window.confirmAction = function(title, text, callback) {
  Swal.fire({
    title: title,
    text: text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, continuar',
    cancelButtonText: 'Cancelar',
    ...SwalConfig
  }).then((result) => {
    if (result.isConfirmed && typeof callback === 'function') {
      callback();
    }
  });
};
</script>

<!-- Script de navegación básica de respaldo -->
<script>
$(document).ready(function() {
    console.log('✅ Dashboard de Acumuladores cargado correctamente');
    console.log('📍 baseUrl configurado:', baseUrl);
    console.log('👤 Rol de usuario:', '<?php echo $role; ?>');
    
    // Verificar que baseUrl esté definido
    if (typeof baseUrl === 'undefined') {
        console.error('❌ baseUrl no está definido!');
        baseUrl = '<?= base_url() ?>';
    }
    
    // Manejar clicks en el menú lateral (respaldo si spa-navigation.js no funciona)
    $('.menu-link').off('click').on('click', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        
        console.log('🔄 Click en menú, página:', page);
        
        if (page) {
            cargarPagina(page);
        }
    });
    
    // Función para cargar páginas dinámicamente (respaldo)
    function cargarPagina(page) {
        $('#dynamic-content').html(`
          <div class="text-center mt-5">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
              <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3" style="color: #FF6B35;">Cargando...</p>
          </div>
        `);
        
        var url = baseUrl + 'pages/load/' + page;
        console.log('📡 Cargando URL:', url);
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('✅ Contenido cargado exitosamente');
                $('#dynamic-content').html(response);
                
                // Scroll al inicio
                $('html, body').animate({ scrollTop: 0 }, 300);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al cargar:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                $('#dynamic-content').html(`
                    <div class="alert alert-danger mt-3" style="background: rgba(220, 53, 69, 0.2); border-left: 4px solid #DC3545;">
                      <h4><i class="fa fa-exclamation-triangle"></i> Error al Cargar</h4>
                      <p><strong>No se pudo cargar el contenido.</strong></p>
                      <p class="mb-0">Página: ${page}<br>URL: ${url}</p>
                      <button class="btn btn-primary mt-3" onclick="location.reload()">
                        <i class="fa fa-refresh"></i> Recargar Página
                      </button>
                    </div>
                `);
            }
        });
    }
    
    // Hacer la función disponible globalmente
    window.cargarPagina = cargarPagina;
    window.loadPage = cargarPagina;
    
    // Configurar tooltips de Bootstrap si existen
    if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});
</script>

</body>
</html>
