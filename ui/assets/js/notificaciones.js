/**
 * Sistema de Notificaciones en Tiempo Real
 * Maneja la actualización y visualización de turnos en la pantalla principal
 */

// Variables globales
var baseUrl = window.baseUrl || '';
var ultimoTurnoAtendiendo = null;
var ultimoProximoTurno = null;
var notificacionesPrevias = [];

$(document).ready(function() {
    
    // Iniciar carousel automático
    $('#turnosCarousel').carousel({
        interval: 5000,
        pause: 'hover'
    });
    
    // Cargar notificaciones al iniciar
    loadNotifications();
    
    // Actualizar notificaciones cada 5 segundos para mayor dinamismo
    setInterval(loadNotifications, 5000);
    
    // Manejar el formulario de login
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        handleLogin();
    });
    
});

/**
 * Cargar notificaciones de turnos desde el servidor
 */
function loadNotifications() {
    $.ajax({
        url: baseUrl + 'welcome/get_notifications',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayNotifications(response);
                
                // Verificar cambios en el turno actual
                checkTurnoActualChanges(response.turno_actual);
                
                // Verificar cambios en el próximo turno
                checkProximoTurnoChanges(response.proximo_turno);
                
                // Guardar notificaciones actuales
                notificacionesPrevias = response.notifications;
            } else {
                console.error('Error en respuesta:', response.message);
                showEmptyNotifications();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar notificaciones:', error);
            showEmptyNotifications();
        }
    });
}

/**
 * Mostrar notificaciones en el panel
 */
function displayNotifications(response) {
    var container = $('#notifications-container');
    container.empty();
    
    var notifications = response.notifications || [];
    
    // Si no hay notificaciones
    if (notifications.length === 0) {
        showEmptyNotifications();
        $('#notification-count').text('0');
        return;
    }
    
    // Actualizar contador
    $('#notification-count').text(notifications.length);
    
    // Agregar encabezado si hay turno actual o próximo
    if (response.turno_actual || response.proximo_turno) {
        var headerHtml = '<div class="mb-3">';
        
        // Mostrar turno actual
        if (response.turno_actual) {
            headerHtml += `
                <div class="alert alert-success mb-2" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="zmdi zmdi-account-circle mr-2" style="font-size: 24px;"></i>
                        <div class="flex-grow-1">
                            <strong>ATENDIENDO AHORA</strong><br>
                            <span class="badge badge-success">${response.turno_actual.turno}</span>
                            <span class="ml-2">${response.turno_actual.cliente}</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Mostrar próximo turno
        if (response.proximo_turno) {
            headerHtml += `
                <div class="alert alert-warning mb-2" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="zmdi zmdi-time mr-2" style="font-size: 24px;"></i>
                        <div class="flex-grow-1">
                            <strong>PRÓXIMO TURNO</strong><br>
                            <span class="badge badge-warning">${response.proximo_turno.turno}</span>
                            <span class="ml-2">${response.proximo_turno.cliente}</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        headerHtml += '</div>';
        container.append(headerHtml);
    }
    
    // Mostrar todas las notificaciones
    notifications.forEach(function(notif) {
        var itemHtml = createNotificationItem(notif);
        container.append(itemHtml);
    });
}

/**
 * Crear elemento HTML para una notificación
 */
function createNotificationItem(notif) {
    // Determinar clases y estilos según el estado
    var itemClass = 'notification-item';
    var badgeClass = 'badge-secondary';
    var statusText = '';
    var statusIcon = '';
    
    switch(notif.estado) {
        case 'atendiendo':
            itemClass += ' llamando';
            badgeClass = 'badge-success';
            statusText = '🔔 ATENDIENDO';
            statusIcon = '<i class="zmdi zmdi-check-circle"></i>';
            break;
        case 'en_espera':
            badgeClass = 'badge-warning';
            statusText = '⏳ EN ESPERA';
            statusIcon = '<i class="zmdi zmdi-time"></i>';
            if (notif.es_proximo) {
                itemClass += ' border-warning';
                statusText = '👉 PRÓXIMO TURNO';
            }
            break;
        case 'reservado':
            badgeClass = 'badge-info';
            statusText = '📋 RESERVADO';
            statusIcon = '<i class="zmdi zmdi-bookmark"></i>';
            break;
        default:
            statusText = '⚪ ' + notif.estado.toUpperCase();
    }
    
    return `
        <div class="${itemClass} new" data-turno-id="${notif.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge ${badgeClass} turno-badge">${notif.turno}</span>
                <small class="text-muted">${statusIcon} ${notif.hora}</small>
            </div>
            <h6 class="font-weight-bold text-dark mb-1">
                ${notif.cliente}
            </h6>
            <p class="mb-1 small text-dark">
                <i class="zmdi zmdi-calendar"></i> ${notif.servicio}
            </p>
            <span class="badge text-dark ${badgeClass.replace('badge-', 'badge-outline-')}">${statusText}</span>
        </div>
    `;
}

/**
 * Mostrar mensaje cuando no hay notificaciones
 */
function showEmptyNotifications() {
    var container = $('#notifications-container');
    container.html(`
        <div class="notifications-empty">
            <i class="zmdi zmdi-check-circle" style="font-size: 48px; color: #28a745;"></i>
            <h5 class="mt-3 mb-2">Todo en orden</h5>
            <p class="mb-0 text-muted">No hay turnos pendientes en este momento</p>
        </div>
    `);
}

/**
 * Verificar cambios en el turno actual y mostrar alerta
 */
function checkTurnoActualChanges(turnoActual) {
    if (!turnoActual) {
        ultimoTurnoAtendiendo = null;
        return;
    }
    
    // Si hay un nuevo turno siendo atendido
    if (!ultimoTurnoAtendiendo || ultimoTurnoAtendiendo.id !== turnoActual.id) {
        ultimoTurnoAtendiendo = turnoActual;
        showTurnoActualAlert(turnoActual);
    }
}

/**
 * Verificar cambios en el próximo turno
 */
function checkProximoTurnoChanges(proximoTurno) {
    if (!proximoTurno) {
        ultimoProximoTurno = null;
        return;
    }
    
    // Si hay un nuevo próximo turno
    if (!ultimoProximoTurno || ultimoProximoTurno.id !== proximoTurno.id) {
        ultimoProximoTurno = proximoTurno;
        showProximoTurnoAlert(proximoTurno);
    }
}

/**
 * Mostrar alerta grande del turno actual siendo atendido
 */
function showTurnoActualAlert(turno) {
    Swal.fire({
        title: '🔔 ATENDIENDO AHORA',
        html: `
            <div class="text-center p-3">
                <div class="mb-4">
                    <span class="badge badge-success" style="font-size: 2.5rem; padding: 1.5rem 3rem; border-radius: 15px;">
                        ${turno.turno}
                    </span>
                </div>
                <h3 class="mb-3">${turno.cliente}</h3>
                <div class="alert alert-light text-dark">
                    <i class="zmdi zmdi-calendar"></i> ${turno.servicio}
                </div>
            </div>
        `,
        icon: 'success',
        iconColor: '#28a745',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#28a745',
        timer: 6000,
        timerProgressBar: true,
        toast: false,
        position: 'center',
        showClass: {
            popup: 'animate__animated animate__bounceIn'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut'
        }
    });
    
    // También mostrar notificación pequeña persistente
    showToastNotification('Atendiendo: ' + turno.turno, turno.cliente, 'success');
}

/**
 * Mostrar alerta del próximo turno
 */
function showProximoTurnoAlert(turno) {
    showToastNotification('Próximo turno: ' + turno.turno, turno.cliente, 'warning');
}

/**
 * Mostrar notificación toast (pequeña y persistente)
 */
function showToastNotification(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
}

/**
 * Manejar el login
 */
function handleLogin() {
    var username = $('#username').val();
    var password = $('#password').val();
    
    if (!username || !password) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, completa todos los campos',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Iniciando sesión...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición AJAX
    $.ajax({
        url: baseUrl + 'welcome/login',
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
                    title: '¡Éxito!',
                    text: response.message,
                    confirmButtonColor: '#667eea',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#667eea'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar tu solicitud',
                confirmButtonColor: '#667eea'
            });
        }
    });
}

// Cerrar modal al hacer login exitoso
$('#loginModal').on('hidden.bs.modal', function() {
    $('#loginForm')[0].reset();
});
