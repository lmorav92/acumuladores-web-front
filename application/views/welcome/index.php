<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content="Sistema de Gestión de Inventario - Tienda de Acumuladores"/>
  <meta name="author" content=""/>
  <title>PowerBattery - Tienda de Acumuladores</title>
  
  <!-- Favicon -->
  <link href="<?= base_url('ui/assets/images/logo.png') ?>" rel="icon" type="image/x-icon">
  
  <!-- Google Web Fonts -->  
  <link href="<?= base_url('ui/assets/css/fonts.googleapis.css') ?>" rel="stylesheet">
  
  <!-- Icon Font Stylesheet -->
  <link href="<?= base_url('ui/assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
  
  <!-- Bootstrap core CSS-->
  <link href="<?= base_url('ui/assets/css/bootstrap5.min.css') ?>" rel="stylesheet"/>
  
  <!-- Custom Style-->
  <link href="<?= base_url('ui/assets/css/sweetalert2.min.css') ?>" rel="stylesheet"/>
  <link href="<?= base_url('ui/assets/css/index.css') ?>" rel="stylesheet"/>
  

</head>

<body>
  <!-- Spinner Start -->
  <div id="spinner" class="show">
    <div class="spinner-grow" role="status">
      <span class="sr-only">Cargando...</span>
    </div>
  </div>
  <!-- Spinner End -->

  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg navbar-dark px-lg-5">
    <div class="container-fluid">
      <a href="<?= base_url() ?>" class="navbar-brand ms-4 ms-lg-0">
        <h2 class="mb-0"><i class="bi bi-battery-charging me-2"></i>PowerBattery</h2>
      </a>
      <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav mx-auto p-4 p-lg-0">
          <a href="#home" class="nav-item nav-link active">Inicio</a>
          <a href="#productos" class="nav-item nav-link">Productos</a>
          <a href="#categorias" class="nav-item nav-link">Categorías</a>
          <a href="#marcas" class="nav-item nav-link">Marcas</a>
          <a href="#testimonios" class="nav-item nav-link">Testimonios</a>
        </div>
        <div class="d-none d-lg-flex">
          <button class="btn btn-outline-primary border-2" data-toggle="modal" data-target="#loginModal">
            <i class="bi bi-person me-2"></i>Iniciar Sesión
          </button>
        </div>
      </div>
    </div>
  </nav>
  <!-- Navbar End -->

  <!-- Carousel Start -->
  <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <?php if (!empty($slides)): ?>
        <?php foreach ($slides as $index => $slide): ?>
          <button type="button" 
                  data-bs-target="#header-carousel" 
                  data-bs-slide-to="<?= $index ?>" 
                  <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?>
                  aria-label="Slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
      <?php else: ?>
        <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <?php endif; ?>
    </div>
    
    <div class="carousel-inner">
      <?php if (!empty($slides)): ?>
        <?php foreach ($slides as $index => $slide): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" 
               style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?= base_url('ui/assets/img/carousel/' . htmlspecialchars($slide->UrlMultimedia)) ?>');">
            <div class="carousel-caption">
              <h5><?= htmlspecialchars($slide->SubtituloSlide ?? 'Bienvenido a') ?></h5>
              <h1 class="display-1"><?= htmlspecialchars($slide->TituloSlide ?? 'PowerBattery') ?></h1>
              <p class="fs-5"><?= htmlspecialchars($slide->DescripcionMultimedia ?? 'Tu tienda especializada en acumuladores') ?></p>
              <?php if ($slide->TextoBoton): ?>
                <a href="<?= htmlspecialchars($slide->AccionBoton ?? '#') ?>" 
                   class="btn btn-outline-primary border-2 py-3 px-5"
                   <?= ($slide->AccionBoton === '#' || empty($slide->AccionBoton)) ? 'data-toggle="modal" data-target="#loginModal"' : '' ?>>
                  <?= htmlspecialchars($slide->TextoBoton) ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Slide por defecto si no hay datos en la BD -->
        <div class="carousel-item active" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?= base_url('ui/assets/img/carousel/slide1.jpg') ?>');">
          <div class="carousel-caption">
            <h5>Bienvenido a</h5>
            <h1 class="display-1">PowerBattery</h1>
            <p class="fs-5">Tu tienda especializada en acumuladores de alta calidad.<br>Encuentra la batería perfecta para tu vehículo con garantía y servicio profesional.</p>
            <button class="btn btn-outline-primary border-2 py-3 px-5" data-toggle="modal" data-target="#loginModal">
              Ver Catálogo
            </button>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <button class="carousel-control-prev" type="button" data-target="#header-carousel" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-target="#header-carousel" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
  <!-- Carousel End -->

  <!-- Products Section Start -->
  <div id="productos" class="services-section">
    <div class="container">
      <div class="section-title">
        <h5>Nuestros Productos</h5>
        <h1>Acumuladores de Calidad</h1>
      </div>
      
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-car-front"></i>
            <h5>Baterías para Auto</h5>
            <p>Acumuladores de alto rendimiento para vehículos livianos. Disponibles en diferentes amperajes y voltajes.</p>
            <div class="price">Desde $85.00</div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-truck"></i>
            <h5>Baterías para Camión</h5>
            <p>Potencia extrema para vehículos pesados. Baterías de ciclo profundo con garantía extendida.</p>
            <div class="price">Desde $250.00</div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-bicycle"></i>
            <h5>Baterías para Moto</h5>
            <p>Acumuladores compactos y potentes para motocicletas. Selladas y libres de mantenimiento.</p>
            <div class="price">Desde $45.00</div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-lightning-charge"></i>
            <h5>Baterías AGM</h5>
            <p>Tecnología AGM de alta gama. Ideales para sistemas Start-Stop y vehículos de lujo.</p>
            <div class="price">Desde $180.00</div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-sun"></i>
            <h5>Baterías Solares</h5>
            <p>Almacenamiento de energía para sistemas fotovoltaicos. Ciclo profundo y larga duración.</p>
            <div class="price">Desde $320.00</div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="service-item">
            <i class="bi bi-water"></i>
            <h5>Baterías Marinas</h5>
            <p>Resistentes a ambientes húmedos y salinos. Perfectas para embarcaciones y equipos náuticos.</p>
            <div class="price">Desde $195.00</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Products Section End -->

  <!-- Categories Panel Start -->
  <div id="categorias" class="turnos-panel">
    <div class="container">
      <div class="section-title">
        <h5>Nuestro Inventario</h5>
        <h1>Categorías de Productos</h1>
      </div>
      
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="row">
            <?php if (!empty($categorias)): ?>
              <?php foreach ($categorias as $categoria): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="category-card">
                    <?php if ($categoria->ImagenCategoria): ?>
                      <img src="<?= base_url('ui/assets/img/categorias/' . htmlspecialchars($categoria->ImagenCategoria)) ?>" 
                           alt="<?= htmlspecialchars($categoria->NombreCategoria) ?>"
                           onerror="this.src='<?= base_url('ui/assets/img/categorias/default.jpg') ?>'">
                    <?php endif; ?>
                    <div class="category-info">
                      <h5><?= htmlspecialchars($categoria->NombreCategoria) ?></h5>
                      <p><?= htmlspecialchars($categoria->DescripcionCategoria) ?></p>
                      <span class="badge bg-primary">Ver Productos</span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <div class="notifications-card">
                  <div class="notifications-empty">
                    <i class="bi bi-box-seam"></i>
                    <p class="mt-3 mb-0">Cargando categorías de productos...</p>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Categories Panel End -->

  <!-- Brands Section Start -->
  <div id="marcas" class="team-section">
    <div class="container">
      <div class="section-title">
        <h5>Marcas Líderes</h5>
        <h1>Trabajamos con las Mejores</h1>
      </div>
      
      <div class="row" id="marcas-container">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/bosch.jpg') ?>" alt="Bosch">
            <div class="team-overlay">
              <h5>BOSCH</h5>
              <p>Líder mundial en tecnología automotriz</p>
              <p class="small mt-2">Alta calidad alemana</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/varta.jpg') ?>" alt="Varta">
            <div class="team-overlay">
              <h5>VARTA</h5>
              <p>Innovación en almacenamiento de energía</p>
              <p class="small mt-2">Tecnología europea</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/acdelco.jpg') ?>" alt="AC Delco">
            <div class="team-overlay">
              <h5>AC DELCO</h5>
              <p>Calidad General Motors</p>
              <p class="small mt-2">Rendimiento confiable</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/willard.jpg') ?>" alt="Willard">
            <div class="team-overlay">
              <h5>WILLARD</h5>
              <p>Tradición y experiencia</p>
              <p class="small mt-2">Baterías premium</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/optima.jpg') ?>" alt="Optima">
            <div class="team-overlay">
              <h5>OPTIMA</h5>
              <p>Tecnología SpiralCell</p>
              <p class="small mt-2">Alto rendimiento</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/duralast.jpg') ?>" alt="Duralast">
            <div class="team-overlay">
              <h5>DURALAST</h5>
              <p>Durabilidad comprobada</p>
              <p class="small mt-2">Garantía extendida</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/interstate.jpg') ?>" alt="Interstate">
            <div class="team-overlay">
              <h5>INTERSTATE</h5>
              <p>Confianza profesional</p>
              <p class="small mt-2">Batería del año</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
          <div class="team-item brand-item">
            <img src="<?= base_url('ui/assets/img/marcas/trojan.jpg') ?>" alt="Trojan">
            <div class="team-overlay">
              <h5>TROJAN</h5>
              <p>Especialistas en ciclo profundo</p>
              <p class="small mt-2">Líderes en calidad</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Brands Section End -->

  <!-- Testimonials Section Start -->
  <div id="testimonios" class="testimonials-section">
    <div class="container">
      <div class="section-title">
        <h5>Opiniones</h5>
        <h1>Lo Que Dicen Nuestros Clientes</h1>
      </div>
      
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="testimonial-card">
            <div class="stars">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p>"Excelente servicio y productos de calidad. La batería que compré para mi camioneta ha funcionado perfectamente desde el primer día. ¡Muy recomendado!"</p>
            <div class="testimonial-author">
              <img src="<?= base_url('ui/assets/img/testimonials/client1.jpg') ?>" alt="Cliente">
              <div>
                <h6>Roberto Méndez</h6>
                <small>Propietario de Camioneta</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="testimonial-card">
            <div class="stars">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p>"El mejor lugar para comprar baterías. Tienen gran variedad, buenos precios y la atención al cliente es excepcional. Instalación rápida y profesional."</p>
            <div class="testimonial-author">
              <img src="<?= base_url('ui/assets/img/testimonials/client2.jpg') ?>" alt="Cliente">
              <div>
                <h6>María García</h6>
                <small>Empresaria</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="testimonial-card">
            <div class="stars">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <p>"Necesitaba una batería de emergencia y me atendieron super rápido. La garantía que ofrecen es inigualable. Sin duda mi tienda de confianza."</p>
            <div class="testimonial-author">
              <img src="<?= base_url('ui/assets/img/testimonials/client3.jpg') ?>" alt="Cliente">
              <div>
                <h6>Carlos Ramírez</h6>
                <small>Conductor de Taxi</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Testimonials Section End -->

  <!-- Features Section Start -->
  <div class="features-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="feature-box">
            <i class="bi bi-shield-check"></i>
            <h5>Garantía Total</h5>
            <p>Todos nuestros productos cuentan con garantía de fábrica</p>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="feature-box">
            <i class="bi bi-tools"></i>
            <h5>Instalación Gratis</h5>
            <p>Instalamos tu batería sin costo adicional</p>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="feature-box">
            <i class="bi bi-truck"></i>
            <h5>Entrega Rápida</h5>
            <p>Servicio a domicilio en toda la ciudad</p>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="feature-box">
            <i class="bi bi-headset"></i>
            <h5>Soporte 24/7</h5>
            <p>Asistencia técnica cuando la necesites</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Features Section End -->

  <!-- Footer Start -->
  <footer class="footer-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <h4><i class="bi bi-battery-charging me-2"></i>PowerBattery</h4>
          <p class="mt-3">Tu tienda especializada en acumuladores de alta calidad. Más de 15 años brindando energía confiable.</p>
          <div class="social-links mt-3">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6 mb-4">
          <h5>Enlaces Rápidos</h5>
          <ul class="footer-links">
            <li><a href="#home">Inicio</a></li>
            <li><a href="#productos">Productos</a></li>
            <li><a href="#categorias">Categorías</a></li>
            <li><a href="#marcas">Marcas</a></li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Servicios</h5>
          <ul class="footer-links">
            <li><a href="#">Venta de Baterías</a></li>
            <li><a href="#">Instalación</a></li>
            <li><a href="#">Mantenimiento</a></li>
            <li><a href="#">Asesoría Técnica</a></li>
          </ul>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Contacto</h5>
          <ul class="footer-contact">
            <li><i class="bi bi-geo-alt"></i> Av. Principal 123, Ciudad</li>
            <li><i class="bi bi-telephone"></i> +1 234 567 8900</li>
            <li><i class="bi bi-envelope"></i> info@powerbattery.com</li>
            <li><i class="bi bi-clock"></i> Lun - Sáb: 8:00 AM - 6:00 PM</li>
          </ul>
        </div>
      </div>
      
      <hr class="footer-divider">
      
      <div class="row">
        <div class="col-12 text-center">
          <p class="mb-0">&copy; 2026 PowerBattery. Todos los derechos reservados. | Diseñado con <i class="bi bi-heart-fill text-danger"></i></p>
        </div>
      </div>
    </div>
  </footer>
  <!-- Footer End -->

  <!-- Login Modal -->
<!-- Login Modal - CORREGIDO CON ICONOS DENTRO -->
  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</h5>
          <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="loginForm">
            <!-- Usuario con icono dentro -->
            <div class="form-group mb-3">
              <label for="username">Usuario</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-person"></i>
                </span>
                <input type="text" class="form-control" id="username" placeholder="Ingrese su usuario" required>
              </div>
            </div>
            
            <!-- Contraseña con icono dentro -->
            <div class="form-group mb-4">
              <label for="password">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock"></i>
                </span>
                <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña" required>
              </div>
            </div>
            
            <!-- Checkbox recordarme -->
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="rememberMe">
              <label class="form-check-label" for="rememberMe">
                Recordarme
              </label>
            </div>
            
            <!-- Botón de submit -->
            <button type="submit" class="btn btn-primary btn-block w-100">
              <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
            </button>
          </form>
          
          <!-- Link a registro -->
          <div class="text-center mt-4">
            <p class="mb-2">
              <a href="#" class="text-primary forgot-link">¿Olvidaste tu contraseña?</a>
            </p>
            <p class="mb-0">
              ¿No tienes cuenta? 
              <a href="#" class="text-primary font-weight-bold" data-toggle="modal" data-target="#registerModal" data-dismiss="modal">
                Regístrate aquí
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Register Modal - CORREGIDO CON ICONOS DENTRO -->
  <div class="modal fade" id="registerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Crear Cuenta</h5>
          <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="registerForm">
            <!-- Usuario -->
            <div class="mb-3">
              <label for="reg_username" class="form-label">Usuario</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-person"></i>
                </span>
                <input type="text" class="form-control" id="reg_username" placeholder="Elige un usuario" required>
              </div>
            </div>
            
            <!-- Email -->
            <div class="mb-3">
              <label for="reg_email" class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-envelope"></i>
                </span>
                <input type="email" class="form-control" id="reg_email" placeholder="tu@email.com" required>
              </div>
            </div>
            
            <!-- Teléfono -->
            <div class="mb-3">
              <label for="reg_phone" class="form-label">Teléfono</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-telephone"></i>
                </span>
                <input type="tel" class="form-control" id="reg_phone" placeholder="123-456-7890" required>
              </div>
            </div>
            
            <!-- Contraseña -->
            <div class="mb-3">
              <label for="reg_password" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock"></i>
                </span>
                <input type="password" class="form-control" id="reg_password" placeholder="Mínimo 6 caracteres" required>
              </div>
            </div>
            
            <!-- Botón registro -->
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-person-plus me-2"></i>Registrarse
            </button>
          </form>
          
          <!-- Link a login -->
          <div class="text-center mt-3">
            <p class="mb-0">
              ¿Ya tienes cuenta? 
              <a href="#" class="text-primary font-weight-bold" data-bs-dismiss="modal" data-toggle="modal" data-target="#loginModal" data-dismiss="modal">
                Inicia Sesión
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Back to Top -->
  <a href="#" class="btn btn-primary btn-back-to-top"><i class="bi bi-arrow-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="<?= base_url('ui/assets/js/jquery.min.js') ?>"></script>
  <script src="<?= base_url('ui/assets/js/bootstrap.min.js') ?>"></script>
  <script src="<?= base_url('ui/assets/js/sweetalert2.all.min.js') ?>"></script>
  
  <!-- Custom JavaScript -->
  <script>
    $(document).ready(function() {
      // Spinner
      setTimeout(function() {
        $('#spinner').removeClass('show');
      }, 1000);
      
      // Smooth scrolling
      $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if(target.length) {
          event.preventDefault();
          $('html, body').stop().animate({
            scrollTop: target.offset().top - 70
          }, 1000);
        }
      });
      
      // Navbar scroll effect
      $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
          $('.navbar').addClass('navbar-scrolled');
        } else {
          $('.navbar').removeClass('navbar-scrolled');
        }
      });
      
      // Back to top button
      $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
          $('.btn-back-to-top').fadeIn();
        } else {
          $('.btn-back-to-top').fadeOut();
        }
      });
      
      $('.btn-back-to-top').click(function() {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
      });
      
      // Login form submission
      $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        var username = $('#username').val();
        var password = $('#password').val();
        
        $.ajax({
          url: '<?= base_url('welcome/login') ?>',
          type: 'POST',
          data: {
            username: username,
            password: password
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: 'Inicio de sesión exitoso',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = response.redirect || '<?= base_url('pages/index') ?>';
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message || 'Usuario o contraseña incorrectos'
              });
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Ha ocurrido un error. Por favor, intente nuevamente.'
            });
          }
        });
      });
    });
  </script>
</body>
</html>
