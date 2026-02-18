/**
 * SPA Navigation System
 * Sistema de navegación para cargar contenido dinámico sin recargar la página
 */

// Definir baseUrl globalmente usando la configuración de CodeIgniter
var baseUrl = document.querySelector('base') ? document.querySelector('base').href : window.location.origin + '/turnos-web-front/';

// Si no existe base tag, intentar obtener de scripts
if (!document.querySelector('base')) {
    var scripts = document.getElementsByTagName('script');
    for (var i = 0; i < scripts.length; i++) {
        if (scripts[i].src.indexOf('spa-navigation.js') > -1) {
            var src = scripts[i].src;
            baseUrl = src.substring(0, src.indexOf('ui/assets/js/'));
            break;
        }
    }
}

$(document).ready(function() {
    
    console.log('SPA Navigation inicializado con baseUrl:', baseUrl);
    
    // Event listener para los enlaces del menú
    $('.menu-link').on('click', function(e) {
        e.preventDefault();
        
        // Remover clase active de todos los items
        $('.sidebar-menu li').removeClass('active');
        
        // Agregar clase active al item clickeado
        $(this).parent('li').addClass('active');
        
        // Obtener el nombre de la página
        var page = $(this).data('page');
        
        console.log('Cargando página:', page);
        
        // Cargar la página
        if (page) {
            loadPage(page);
        }
        
        // Cerrar sidebar en móviles
        if ($(window).width() < 768) {
            $('body').removeClass('sidebar-toggled');
        }
    });
    
    // También manejar el logo que apunta al dashboard
    $('[data-page="dashboard"]').first().on('click', function(e) {
        e.preventDefault();
        loadPage('dashboard');
        
        // Activar el item del dashboard en el menú
        $('.menu-link[data-page="dashboard"]').parent('li').addClass('active');
    });
});

/**
 * Función para cargar contenido dinámicamente
 * @param {string} page - Nombre de la página a cargar
 */
function loadPage(page) {
    // Mostrar loading
    showLoading();
    
    // URL del controlador de CodeIgniter
    var url = baseUrl + 'pages/load/' + page;
    
    console.log('Cargando URL:', url);
    
    // Realizar petición AJAX
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            console.log('Página cargada exitosamente');
            
            // Insertar el contenido en el contenedor dinámico
            $('#dynamic-content').html(response);
            
            // Scroll al inicio del contenido
            $('html, body').animate({ scrollTop: 0 }, 300);
            
            // Ocultar loading
            hideLoading();
            
            // Actualizar la URL sin recargar (opcional - requiere history API)
            if (typeof history.pushState !== 'undefined') {
                history.pushState(null, null, baseUrl + 'dashboard/' + page);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar la página:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            // Mostrar mensaje de error
            $('#dynamic-content').html(
                '<div class="container-fluid mt-3">' +
                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Error al Cargar</h4>' +
                '<p><strong>No se pudo cargar el contenido.</strong></p>' +
                '<hr>' +
                '<p class="mb-0">' +
                'Posibles causas:<br>' +
                '• La página "' + page + '" no existe<br>' +
                '• Error en el servidor<br>' +
                '• Problema de permisos<br>' +
                '</p>' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="text-center">' +
                '<button class="btn btn-primary" onclick="loadPage(\'dashboard\')">Volver al Dashboard</button>' +
                '</div>' +
                '</div>'
            );
            
            hideLoading();
        }
    });
}

/**
 * Mostrar indicador de carga
 */
function showLoading() {
    // Agregar overlay de loading
    if ($('#ajax-loading').length === 0) {
        $('body').append(
            '<div id="ajax-loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">' +
            '<div style="text-align: center;">' +
            '<div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">' +
            '<span class="sr-only">Cargando...</span>' +
            '</div>' +
            '<p class="text-white mt-3">Cargando...</p>' +
            '</div>' +
            '</div>'
        );
    }
}

/**
 * Ocultar indicador de carga
 */
function hideLoading() {
    $('#ajax-loading').fadeOut(300, function() {
        $(this).remove();
    });
}

// Manejar el botón back del navegador
window.addEventListener('popstate', function(e) {
    // Extraer la página de la URL
    var path = window.location.pathname;
    var page = path.split('/welcome/index').pop();
    
    if (page && page !== '' && page !== 'index') {
        loadPage(page);
    } else {
        loadPage('dashboard');
    }
});
