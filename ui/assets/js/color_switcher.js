/**
 * ========================================================================
 * SISTEMA DE TEMAS - Color Switcher
 * Funciones adicionales para mejorar la experiencia del usuario
 * ========================================================================
 */

// Mapa de temas con sus nombres y descripciones
const THEMES_INFO = {
    // Gaussion Texture
    'theme1': { name: 'Purple Dream', category: 'Gaussion Texture' },
    'theme2': { name: 'Sunset Fire', category: 'Gaussion Texture' },
    'theme3': { name: 'Ocean Blue', category: 'Gaussion Texture' },
    'theme4': { name: 'Fresh Green', category: 'Gaussion Texture' },
    'theme5': { name: 'Ruby Red', category: 'Gaussion Texture' },
    'theme6': { name: 'Royal Purple', category: 'Gaussion Texture' },
    
    // Gradient Background
    'theme7': { name: 'Diagonal Purple', category: 'Gradient Background' },
    'theme8': { name: 'Pink Passion', category: 'Gradient Background' },
    'theme9': { name: 'Sky Horizon', category: 'Gradient Background' },
    'theme10': { name: 'Mint Fresh', category: 'Gradient Background' },
    'theme11': { name: 'Golden Sunset', category: 'Gradient Background' },
    'theme12': { name: 'Deep Ocean', category: 'Gradient Background' },
    'theme13': { name: 'Pastel Dream', category: 'Gradient Background' },
    'theme14': { name: 'Rose Garden', category: 'Gradient Background' },
    'theme15': { name: 'Peach Cream', category: 'Gradient Background' }
};

$(document).ready(function() {
    // Inicializar el color switcher
    initColorSwitcher();
    
    // Cargar el tema guardado del usuario al iniciar
    cargarTemaUsuario();
    
    // Evento al hacer clic en cualquier tema
    $('.switcher li').on('click', function() {
        var temaId = $(this).attr('id');
        cambiarTema(temaId);
        guardarTemaUsuario(temaId);
        mostrarNotificacionTema(temaId);
    });
    
    // Toggle del panel de temas
    $('.switcher-icon').on('click', function() {
        $('.right-sidebar').toggleClass('show');
    });
    
    // Cerrar el panel al hacer clic fuera de él
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.right-sidebar, .switcher-icon').length) {
            $('.right-sidebar').removeClass('show');
        }
    });
});

/**
 * Inicializar el color switcher
 */
function initColorSwitcher() {
    console.log('Inicializando Color Switcher...');
    
    // Agregar tooltips a los temas
    $('.switcher li').each(function() {
        var temaId = $(this).attr('id');
        var themeInfo = THEMES_INFO[temaId];
        if (themeInfo) {
            $(this).attr('title', themeInfo.name);
            $(this).attr('data-toggle', 'tooltip');
        }
    });
    
    // Inicializar tooltips de Bootstrap si está disponible
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
}

/**
 * Función para cargar el tema del usuario desde el servidor
 */
function cargarTemaUsuario() {
    $.ajax({
        url: base_url_themes('preferencias/getTemaActual'),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta getTemaActual:', response);
            if(response.success && response.tema) {
                console.log('Tema cargado:', response.tema);
                cambiarTema(response.tema);
            } else {
                console.log('No se encontró tema, usando theme1 por defecto');
                cambiarTema('theme1');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar el tema:', error);
            console.log('Status:', status);
            console.log('Respuesta del servidor:', xhr.responseText);
            // En caso de error, usar theme1 por defecto
            cambiarTema('theme1');
        }
    });
}

/**
 * Función para cambiar el tema visualmente
 */
function cambiarTema(temaId) {
    // Remover la clase active de todos los temas
    $('.switcher li').removeClass('active');
    
    // Agregar clase active al tema seleccionado
    $('#' + temaId).addClass('active');
    
    // Remover todas las clases de tema anteriores del body
    $('body').removeClass(function (index, className) {
        return (className.match(/(^|\s)bg-theme\S+/g) || []).join(' ');
    });
    
    // Extraer el número del tema
    var themeNumber = temaId.replace('theme', '');
    
    // Aplicar el nuevo tema al body
    $('body').addClass('bg-theme bg-theme' + themeNumber);
    
    // Guardar en localStorage para respaldo
    localStorage.setItem('user_theme', temaId);
    
    console.log('Tema aplicado:', temaId);
}

/**
 * Función para guardar el tema seleccionado en la base de datos
 */
function guardarTemaUsuario(temaId) {
    $.ajax({
        url: base_url_themes('preferencias/updateTema'),
        type: 'POST',
        data: { tema: temaId },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta updateTema:', response);
            if(response.success) {
                console.log('Tema guardado correctamente en BD:', temaId);
            } else {
                console.warn('No se pudo guardar el tema en BD');
                mostrarErrorTema();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar el tema:', error);
            console.log('Status:', status);
            console.log('Respuesta del servidor:', xhr.responseText);
            mostrarErrorTema();
        }
    });
}

/**
 * Mostrar notificación cuando se cambia el tema
 */
function mostrarNotificacionTema(temaId) {
    var themeInfo = THEMES_INFO[temaId];
    var mensaje = themeInfo ? `Tema "${themeInfo.name}" aplicado` : 'Tema aplicado';
    
    // Si SweetAlert está disponible, usarlo
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: mensaje,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    } else {
        // Mostrar notificación simple
        mostrarNotificacionSimple(mensaje, 'success');
    }
}

/**
 * Mostrar error al guardar el tema
 */
function mostrarErrorTema() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'No se pudo guardar el tema',
            text: 'El tema se aplicó pero no se guardó en el servidor',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    } else {
        mostrarNotificacionSimple('Error al guardar el tema', 'error');
    }
}

/**
 * Notificación simple sin dependencias
 */
function mostrarNotificacionSimple(mensaje, tipo) {
    var color = tipo === 'success' ? '#28a745' : '#dc3545';
    var notification = $('<div>')
        .css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: color,
            color: '#fff',
            padding: '15px 20px',
            borderRadius: '5px',
            boxShadow: '0 2px 10px rgba(0,0,0,0.2)',
            zIndex: 99999,
            fontWeight: 'bold'
        })
        .text(mensaje)
        .appendTo('body');
    
    setTimeout(function() {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 2000);
}

/**
 * Helper para construir URLs base - ESPECÍFICO PARA TEMAS
 * Esta función usa la variable global baseUrl que ya existe en spa-navigation.js
 */
function base_url_themes(uri) {
    // Intentar usar la variable global baseUrl definida en spa-navigation.js
    if (typeof baseUrl !== 'undefined' && baseUrl) {
        console.log('Usando baseUrl global:', baseUrl);
        return baseUrl + uri;
    }
    
    // Fallback: construir manualmente
    var base = window.location.protocol + "//" + window.location.host + "/turnos-web-front/";
    console.log('Usando baseUrl construida:', base);
    return base + uri;
}

/**
 * Obtener el tema actual desde localStorage (respaldo)
 */
function getTemaActualLocal() {
    return localStorage.getItem('user_theme') || 'theme1';
}

/**
 * Precargar el tema desde localStorage mientras se carga desde el servidor
 * Esto mejora la experiencia del usuario al evitar el "flash" del tema por defecto
 */
(function() {
    var temaLocal = getTemaActualLocal();
    if (temaLocal) {
        var themeNumber = temaLocal.replace('theme', '');
        $('body').addClass('bg-theme bg-theme' + themeNumber);
    }
})();
