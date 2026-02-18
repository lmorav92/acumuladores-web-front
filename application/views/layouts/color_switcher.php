<!--start color switcher-->
<div class="right-sidebar">
  <div class="switcher-icon">
    <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
  </div>
  <div class="right-sidebar-content">

    <p class="mb-0">Temas Energía (Baterías)</p>
    <hr>
    
    <ul class="switcher">
      <li id="theme1" title="Naranja Clásico"></li>
      <li id="theme2" title="Voltaje Amarillo"></li>
      <li id="theme3" title="Azul Eléctrico"></li>
      <li id="theme4" title="Verde Energía"></li>
      <li id="theme5" title="Rojo Potencia"></li>
      <li id="theme6" title="Morado Voltio"></li>
    </ul>

    <p class="mb-0">Temas Degradados</p>
    <hr>
    
    <ul class="switcher">
      <li id="theme7" title="Sunset Battery"></li>
      <li id="theme8" title="Power Pink"></li>
      <li id="theme9" title="Electric Blue"></li>
      <li id="theme10" title="Energy Green"></li>
      <li id="theme11" title="Warm Charge"></li>
      <li id="theme12" title="Deep Ocean"></li>
    </ul>

    <p class="mb-0">Tema Oscuro</p>
    <hr>
    
    <ul class="switcher">
      <li id="theme16" title="Negro Profesional"></li>
    </ul>
    
  </div>
</div>
<!--end color switcher-->

<script>
$(document).ready(function() {
    // Cargar el tema guardado del usuario al iniciar
    cargarTemaUsuario();
    
    // Evento al hacer clic en cualquier tema
    $('.switcher li').on('click', function() {
        var temaId = $(this).attr('id');
        cambiarTema(temaId);
        guardarTemaUsuario(temaId);
    });
    
    // Toggle del panel de temas
    $('.switcher-icon').on('click', function() {
        $('.right-sidebar').toggleClass('show');
    });
    
    // Cerrar el panel al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.right-sidebar, .switcher-icon').length) {
            $('.right-sidebar').removeClass('show');
        }
    });
});

// Función para cargar el tema del usuario desde el servidor
function cargarTemaUsuario() {
    $.ajax({
        url: '<?= base_url("preferencias/getTemaActual") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Tema cargado:', response);
            if(response.success && response.tema) {
                cambiarTema(response.tema);
            } else {
                console.log('⚠️ No se encontró tema, usando theme1 (Naranja) por defecto');
                cambiarTema('theme1');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar el tema:', error);
            
            // En caso de error, intentar cargar desde localStorage
            var temaLocal = localStorage.getItem('user_theme');
            if (temaLocal) {
                console.log('📱 Cargando tema desde localStorage:', temaLocal);
                cambiarTema(temaLocal);
            } else {
                cambiarTema('theme1');
            }
        }
    });
}

// Función para cambiar el tema visualmente
function cambiarTema(temaId) {
    console.log('🎨 Aplicando tema:', temaId);
    
    // Remover la clase active de todos los temas
    $('.switcher li').removeClass('active');
    
    // Agregar clase active al tema seleccionado
    $('#' + temaId).addClass('active');
    
    // Aplicar el tema al body
    $('body').removeClass(function (index, className) {
        return (className.match(/(^|\s)bg-theme\S+/g) || []).join(' ');
    });
    
    var themeNumber = temaId.replace('theme', '');
    $('body').addClass('bg-theme bg-theme' + themeNumber);
    
    // Guardar en localStorage como respaldo
    localStorage.setItem('user_theme', temaId);
    
    console.log('✅ Tema aplicado:', temaId);
}

// Función para guardar el tema seleccionado en la base de datos
function guardarTemaUsuario(temaId) {
    $.ajax({
        url: '<?= base_url("preferencias/updateTema") ?>',
        type: 'POST',
        data: { tema: temaId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                console.log('💾 Tema guardado en BD:', temaId);
                
                // Mostrar notificación de éxito
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Tema guardado correctamente',
                        background: '#2A2A2A',
                        color: '#F5F5F5'
                    });
                }
            }
        },
        error: function(xhr) {
            console.warn('⚠️ No se pudo guardar en BD (usando localStorage)');
        }
    });
}
</script>

<style>
/* ============================================
   COLOR SWITCHER - TEMA ACUMULADORES
   Naranja y Grises
   ============================================ */

.right-sidebar {
    position: fixed;
    right: -270px;
    top: 50%;
    transform: translateY(-50%);
    width: 270px;
    background: linear-gradient(135deg, #2A2A2A 0%, #1E1E1E 100%);
    box-shadow: -5px 0 20px rgba(0,0,0,0.5);
    transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 9999;
    border-radius: 15px 0 0 15px;
    padding: 25px;
    border: 2px solid #FF6B35;
    border-right: none;
}

.right-sidebar.show {
    right: 0;
}

.switcher-icon {
    position: absolute;
    left: -50px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
    color: #fff;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 10px 0 0 10px;
    box-shadow: -3px 0 10px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}

.switcher-icon:hover {
    background: linear-gradient(135deg, #FF8C61 0%, #FF6B35 100%);
    box-shadow: -5px 0 15px rgba(255, 107, 53, 0.5);
    transform: translateY(-50%) scale(1.1);
}

.switcher-icon i {
    font-size: 24px;
}

.right-sidebar-content {
    max-height: 550px;
    overflow-y: auto;
}

.right-sidebar-content::-webkit-scrollbar {
    width: 6px;
}

.right-sidebar-content::-webkit-scrollbar-track {
    background: #1E1E1E;
    border-radius: 3px;
}

.right-sidebar-content::-webkit-scrollbar-thumb {
    background: #FF6B35;
    border-radius: 3px;
}

.right-sidebar-content p {
    color: #F5F5F5;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 15px;
    margin-bottom: 10px;
}

.right-sidebar-content hr {
    border-top: 2px solid rgba(255, 107, 53, 0.3);
    margin: 10px 0;
}

.switcher {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.switcher li {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: 3px solid transparent;
    position: relative;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.switcher li:hover {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    z-index: 1;
}

.switcher li.active {
    border-color: #FFB84D;
    box-shadow: 0 0 20px rgba(255, 184, 77, 0.7);
    transform: scale(1.1);
}

.switcher li.active::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 24px;
    font-weight: bold;
    text-shadow: 0 2px 5px rgba(0,0,0,0.5);
}

/* ============================================
   DEFINICIÓN DE TEMAS - ESTILO ACUMULADORES
   ============================================ */

/* Temas Energía (Inspirados en baterías) */
.switcher li#theme1 { 
    background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
}

.switcher li#theme2 { 
    background: linear-gradient(135deg, #FFB84D 0%, #FF9500 100%);
}

.switcher li#theme3 { 
    background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);
}

.switcher li#theme4 { 
    background: linear-gradient(135deg, #4CAF50 0%, #1B5E20 100%);
}

.switcher li#theme5 { 
    background: linear-gradient(135deg, #F44336 0%, #B71C1C 100%);
}

.switcher li#theme6 { 
    background: linear-gradient(135deg, #9C27B0 0%, #4A148C 100%);
}

/* Temas Degradados */
.switcher li#theme7 { 
    background: linear-gradient(135deg, #FF6B35 0%, #FFB84D 50%, #FF9500 100%);
}

.switcher li#theme8 { 
    background: linear-gradient(135deg, #FF6B9D 0%, #FFC371 100%);
}

.switcher li#theme9 { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.switcher li#theme10 { 
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.switcher li#theme11 { 
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.switcher li#theme12 { 
    background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
}

/* Tema Oscuro */
.switcher li#theme16 { 
    background: #1A1A1A;
    border: 2px solid #333;
}

/* ============================================
   APLICACIÓN DE TEMAS AL BODY
   ============================================ */

/* Theme 1 - Naranja Clásico (DEFAULT) */
body.bg-theme.bg-theme1 { 
    background: linear-gradient(135deg, #1E1E1E 0%, #2A2A2A 50%, #1E1E1E 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 2 - Voltaje Amarillo */
body.bg-theme.bg-theme2 { 
    background: linear-gradient(135deg, #2A1F00 0%, #3D2E00 50%, #2A1F00 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 3 - Azul Eléctrico */
body.bg-theme.bg-theme3 { 
    background: linear-gradient(135deg, #001F3D 0%, #002B52 50%, #001F3D 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 4 - Verde Energía */
body.bg-theme.bg-theme4 { 
    background: linear-gradient(135deg, #0D2818 0%, #143D28 50%, #0D2818 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 5 - Rojo Potencia */
body.bg-theme.bg-theme5 { 
    background: linear-gradient(135deg, #2D0D0D 0%, #3D1414 50%, #2D0D0D 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 6 - Morado Voltio */
body.bg-theme.bg-theme6 { 
    background: linear-gradient(135deg, #1A0D2D 0%, #261440 50%, #1A0D2D 100%) !important;
    background-attachment: fixed !important;
}

/* Themes 7-12 - Degradados */
body.bg-theme.bg-theme7 { 
    background: linear-gradient(135deg, #2A1800 0%, #3D2400 50%, #2A1800 100%) !important;
    background-attachment: fixed !important;
}

body.bg-theme.bg-theme8 { 
    background: linear-gradient(135deg, #2D1824 0%, #3D2433 50%, #2D1824 100%) !important;
    background-attachment: fixed !important;
}

body.bg-theme.bg-theme9 { 
    background: linear-gradient(135deg, #00232D 0%, #003340 50%, #00232D 100%) !important;
    background-attachment: fixed !important;
}

body.bg-theme.bg-theme10 { 
    background: linear-gradient(135deg, #0D2D1F 0%, #144033 50%, #0D2D1F 100%) !important;
    background-attachment: fixed !important;
}

body.bg-theme.bg-theme11 { 
    background: linear-gradient(135deg, #2D1820 0%, #3D242D 50%, #2D1820 100%) !important;
    background-attachment: fixed !important;
}

body.bg-theme.bg-theme12 { 
    background: linear-gradient(135deg, #0A2D2D 0%, #0F1A40 50%, #0A2D2D 100%) !important;
    background-attachment: fixed !important;
}

/* Theme 16 - Negro Profesional */
body.bg-theme.bg-theme16 { 
    background: #0D0D0D !important;
    background-attachment: fixed !important;
}

/* Responsive */
@media (max-width: 768px) {
    .right-sidebar {
        width: 220px;
        right: -220px;
        padding: 20px 15px;
    }
    
    .switcher {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    
    .switcher li {
        width: 50px;
        height: 50px;
    }
    
    .switcher-icon {
        width: 45px;
        height: 45px;
        left: -45px;
    }
}

/* Animación del ícono */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.zmdi-hc-spin {
    animation: spin 3s linear infinite;
}

/* Efecto de brillo al pasar el mouse */
.switcher li::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, transparent 100%);
    border-radius: 12px;
    opacity: 0;
    transition: opacity 0.3s;
}

.switcher li:hover::before {
    opacity: 1;
}
</style>
