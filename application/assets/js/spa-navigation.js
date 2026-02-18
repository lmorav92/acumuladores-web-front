/**
 * SPA Navigation System
 * Sistema de navegación para cargar contenido dinámico sin recargar la página
 */

$(document).ready(function() {
    
    // Cargar el dashboard al iniciar
    loadPage('dashboard');
    
    // Event listener para los enlaces del menú
    $('.menu-link').on('click', function(e) {
        e.preventDefault();
        
        // Remover clase active de todos los items
        $('.sidebar-menu li').removeClass('active');
        
        // Agregar clase active al item clickeado
        $(this).parent('li').addClass('active');
        
        // Obtener el nombre de la página
        var page = $(this).data('page');
        
        // Cargar la página
        loadPage(page);
        
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
    
    // Realizar petición AJAX
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
        success: function(response) {
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
            
            // Mostrar mensaje de error
            $('#dynamic-content').html(
                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<strong>Error!</strong> No se pudo cargar el contenido. Por favor, intenta nuevamente.' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
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
            '<div id="ajax-loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 9999; display: flex; align-items: center; justify-content: center;">' +
            '<div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">' +
            '<span class="sr-only">Cargando...</span>' +
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

/**
 * Función logout (ya existente en tu código)
 */
function logout() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Cerrarás tu sesión actual",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem("session_mi_turno");
            window.location.href = baseUrl + "index.html";
        }
    });
}

// Definir baseUrl globalmente (ajusta según tu configuración)
var baseUrl = window.location.origin + '/';

// Manejar el botón back del navegador
window.addEventListener('popstate', function(e) {
    // Extraer la página de la URL
    var path = window.location.pathname;
    var page = path.split('/').pop();
    
    if (page) {
        loadPage(page);
    }
});
