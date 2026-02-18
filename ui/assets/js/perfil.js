/**
 * JavaScript para la página de Perfil
 */

// Variables globales
let datosOriginales = {};
var baseUrlPerfil = typeof baseUrl !== 'undefined' ? baseUrl : '';
// Inicialización
$(document).ready(function() {
    console.log('Perfil JS cargado');
    console.log('BaseUrl disponible:', baseUrlPerfil);
    
    // Guardar datos originales del usuario
    guardarDatosOriginales();
    
    // Inicializar eventos
    initEventos();
});

/**
 * Guardar datos originales para poder cancelar
 */
function guardarDatosOriginales() {
    datosOriginales = {
        nombre: $('#nombre').val(),
        apellidos: $('#apellidos').val(),
        carnet: $('#carnet').val(),
        email: $('#email').val(),
        telefono: $('#telefono').val(),
        direccion: $('#direccion').val()
    };
}

/**
 * Inicializar eventos
 */
function initEventos() {
    console.log('Inicializando eventos del perfil...');
    
    // Usar delegación de eventos para que funcione incluso si el contenido se carga dinámicamente
    // Primero, remover cualquier evento previo para evitar duplicados
    $(document).off('submit', '#formUsuario');
    $(document).off('submit', '#formPreferencias');
    $(document).off('change', '#temaInterfaz');
    
    // Envío formulario de usuario usando delegación
    $(document).on('submit', '#formUsuario', function(e) {
        e.preventDefault();
        console.log('Formulario de usuario enviado');
        actualizarUsuario();
    });
    
    // Envío formulario de preferencias usando delegación
    $(document).on('submit', '#formPreferencias', function(e) {
        e.preventDefault();
        console.log('Formulario de preferencias enviado');
        actualizarPreferencias();
    });
    
    // Cambio de tema en tiempo real
    $(document).on('change', '#temaInterfaz', function() {
        const nuevoTema = $(this).val();
        if (confirm('¿Deseas aplicar este tema ahora?')) {
            aplicarTemaTemporal(nuevoTema);
        }
    });
    
    console.log('Eventos inicializados correctamente');
}

/**
 * Actualizar información del usuario
 */
function actualizarUsuario() {
    console.log('=== actualizarUsuario INICIADO ===');
    
    const formData = {
        idUsuario: $('#idUsuario').val(),
        idCliente: $('#idCliente').val(),
        nombre: $('#nombre').val().trim(),
        apellidos: $('#apellidos').val().trim(),
        carnet: $('#carnet').val().trim(),
        email: $('#email').val().trim(),
        telefono: $('#telefono').val().trim(),
        direccion: $('#direccion').val().trim()
    };
    
    console.log('Datos del formulario:', formData);
    
    // Validación básica
    if (!formData.nombre || !formData.apellidos || !formData.carnet || !formData.email) {
        console.log('Validación fallida: campos requeridos vacíos');
        Swal.fire({
            icon: 'warning',
            title: 'Campos Requeridos',
            text: 'Por favor completa todos los campos obligatorios',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Validar formato de carnet (11 dígitos)
    if (formData.carnet.length !== 11 || !/^\d+$/.test(formData.carnet)) {
        console.log('Validación fallida: carnet inválido', formData.carnet);
        Swal.fire({
            icon: 'warning',
            title: 'Carnet Inválido',
            text: 'El carnet debe tener exactamente 11 dígitos',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        Swal.fire({
            icon: 'warning',
            title: 'Email Inválido',
            text: 'Por favor ingresa un email válido',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Mostrar loading
    console.log('Validaciones pasadas. Mostrando loading y enviando petición AJAX...');
    console.log('URL:', baseUrlPerfil + 'perfil/actualizar_usuario');
    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición AJAX
    console.log('Enviando datos:', formData);
    $.ajax({
        url: baseUrlPerfil + 'perfil/actualizar_usuario',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Actualizar datos originales
                    guardarDatosOriginales();
                    
                    // Actualizar nombre en el navbar si cambió
                    if (response.data && response.data.nombre_completo) {
                        $('.user-title').text(response.data.nombre_completo);
                        
                        // Actualizar avatar si el nombre cambió
                        const nuevoAvatar = 'https://ui-avatars.com/api/?name=' + 
                                          encodeURIComponent(response.data.nombre_completo) + 
                                          '&background=random&size=150';
                        $('#userAvatar').attr('src', nuevoAvatar);
                        $('#avatarPreview').attr('src', nuevoAvatar);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

/**
 * Actualizar preferencias del usuario
 */
function actualizarPreferencias() {
    const formData = {
        idPreferencia: $('#idPreferencia').val(),
        temaInterfaz: $('#temaInterfaz').val(),
        idiomaPreferido: $('#idiomaPreferido').val(),
        notificacionesEmail: $('#notificacionesEmail').is(':checked') ? 1 : 0,
        notificacionesPush: $('#notificacionesPush').is(':checked') ? 1 : 0
    };
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando preferencias...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición AJAX
    $.ajax({
        url: baseUrlPerfil + 'perfil/actualizar_preferencias',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Aplicar el tema seleccionado
                    aplicarTemaTemporal(formData.temaInterfaz);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

/**
 * Cambiar avatar del usuario
 */
function cambiarAvatar() {
    Swal.fire({
        title: 'Cambiar Avatar',
        html: `
            <div class="form-group text-left">
                <label for="avatar_url_input">URL del Avatar:</label>
                <input type="url" 
                       id="avatar_url_input" 
                       class="form-control" 
                       placeholder="https://ejemplo.com/avatar.jpg">
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">O genera uno aleatorio con tus iniciales</small><br>
                <button type="button" 
                        class="btn btn-sm btn-secondary mt-2" 
                        onclick="generarAvatarAleatorio()">
                    <i class="fa fa-random"></i> Generar Avatar Aleatorio
                </button>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        width: '500px',
        preConfirm: () => {
            const url = document.getElementById('avatar_url_input').value;
            if (!url) {
                Swal.showValidationMessage('Por favor ingresa una URL válida');
                return false;
            }
            return url;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            actualizarAvatar(result.value);
        }
    });
}

/**
 * Generar avatar aleatorio con iniciales
 */
function generarAvatarAleatorio() {
    const nombre = $('#nombre').val();
    const apellidos = $('#apellidos').val();
    const nombreCompleto = (nombre + ' ' + apellidos).trim();
    
    if (!nombreCompleto) {
        Swal.showValidationMessage('Primero completa tu nombre y apellidos en el formulario');
        return;
    }
    
    const colores = ['007bff', '28a745', 'dc3545', 'ffc107', '17a2b8', '6c757d', '6f42c1', 'e83e8c'];
    const colorAleatorio = colores[Math.floor(Math.random() * colores.length)];
    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(nombreCompleto)}&background=${colorAleatorio}&color=fff&size=150`;
    
    document.getElementById('avatar_url_input').value = avatarUrl;
}

/**
 * Actualizar avatar en el servidor
 */
function actualizarAvatar(avatarUrl) {
    // Mostrar loading
    Swal.fire({
        title: 'Actualizando avatar...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: baseUrlPerfil + 'perfil/actualizar_avatar',
        type: 'POST',
        data: { avatar_url: avatarUrl },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
                
                // Actualizar imagen del avatar en la página
                $('#avatarPreview').attr('src', response.avatar_url);
                if ($('#userAvatar').length) {
                    $('#userAvatar').attr('src', response.avatar_url);
                }
                $('.dropdown-menu .avatar img').attr('src', response.avatar_url);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

/**
 * Cancelar edición y restaurar datos originales
 */
function cancelarEdicion() {
    Swal.fire({
        title: '¿Descartar cambios?',
        text: 'Se perderán todos los cambios no guardados',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, descartar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#nombre').val(datosOriginales.nombre);
            $('#apellidos').val(datosOriginales.apellidos);
            $('#carnet').val(datosOriginales.carnet);
            $('#email').val(datosOriginales.email);
            $('#telefono').val(datosOriginales.telefono);
            $('#direccion').val(datosOriginales.direccion);
            
            Swal.fire({
                icon: 'info',
                title: 'Cambios Descartados',
                text: 'Se han restaurado los datos originales',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

/**
 * Ver sesiones activas
 */
function verSesionesActivas() {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando sesiones...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: baseUrlPerfil + 'perfil/sesiones_activas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            console.log(response);
            if (response.success && response.data) {
                let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
                html += '<thead class="thead-light text-dark"><tr><th>Fecha Inicio</th><th>IP</th><th>Navegador</th><th>Acción</th></tr></thead><tbody>';
                
                if (response.data.length > 0) {
                    response.data.forEach(function(sesion) {
                        html += '<tr>';
                        html += '<td class="text-dark"><small>' + (sesion.FechaInicio || 'N/A') + '</small></td>';
                        html += '<td class="text-dark"><small>' + (sesion.IpSesion || 'N/A') + '</small></td>';
                        html += '<td class="text-dark"><small>' + (sesion.NavegadorSesion || 'N/A') + '</small></td>';
                        html += '<td class="text-dark"><button class="btn btn-sm btn-danger" onclick="cerrarSesion(' + sesion.IdSesion + ')"><i class="fa fa-times"></i> Cerrar</button></td>';
                        html += '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="4" class="text-center text-muted">No hay sesiones activas</td></tr>';
                }
                
                html += '</tbody></table></div>';
                
                Swal.fire({
                    title: '<i class="fa fa-laptop"></i> Sesiones Activas',
                    html: html,
                    width: '1000px',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'swal-wide'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar las sesiones',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

/**
 * Cerrar una sesión específica
 */
function cerrarSesion(idSesion) {
    Swal.fire({
        title: '¿Cerrar esta sesión?',
        text: 'Esta acción cerrará la sesión seleccionada',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrlPerfil + 'perfil/cerrar_sesion',
                type: 'POST',
                data: { id_sesion: idSesion },
                dataType: 'json',
                success: function(response) {
					console.log(response);
					
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            confirmButtonText: 'Aceptar',
                            timer: 2000
                        }).then(() => {
                            verSesionesActivas(); // Recargar lista
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo conectar con el servidor',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

/**
 * Ver historial de acceso
 */
function verHistorialAcceso() {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando historial...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: baseUrlPerfil + 'perfil/historial_acceso',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success && response.data) {
                let html = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;"><table class="table table-sm table-hover">';
                html += '<thead class="thead-light text-dark"><tr><th>Fecha y Hora</th><th>Tipo</th><th>IP</th><th>Navegador</th></tr></thead><tbody>';
                
                if (response.data.length > 0) {
                    response.data.forEach(function(log) {
                        let tipoBadge = 'info';
                        if (log.TipoAcceso === 'login') tipoBadge = 'success';
                        else if (log.TipoAcceso === 'logout') tipoBadge = 'warning';
                        else if (log.TipoAcceso === 'intento_fallido') tipoBadge = 'danger';
                        
                        html += '<tr>';
                        html += '<td class="text-dark"><small>' + (log.FechaHoraAcceso || 'N/A') + '</small></td>';
                        html += '<td class="text-dark"><span class="badge badge-' + tipoBadge + '">' + (log.TipoAcceso || 'N/A') + '</span></td>';
                        html += '<td class="text-dark"><small>' + (log.IpAcceso || 'N/A') + '</small></td>';
                        html += '<td class="text-dark"><small>' + (log.NavegadorAcceso || 'N/A') + '</small></td>';
                        html += '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="4" class="text-center text-muted">No hay historial disponible</td></tr>';
                }
                
                html += '</tbody></table></div>';
                
                Swal.fire({
                    title: '<i class="fa fa-history"></i> Historial de Acceso (Últimos 20)',
                    html: html,
                    width: '1000px',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'swal-wide'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el historial',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

/**
 * Aplicar tema temporal (preview)
 */
function aplicarTemaTemporal(tema) {
    $('body').removeClass('theme1 theme2 theme3 theme4');
    $('body').addClass(tema);
}

/**
 * Cambiar contraseña
 */
function cambiarPassword() {
    Swal.fire({
        title: '<i class="fa fa-lock"></i> Cambiar Contraseña',
        html: `
            <div class="form-group text-left">
                <label for="password_actual">Contraseña Actual:</label>
                <input id="password_actual" 
                       type="password" 
                       class="form-control" 
                       placeholder="Ingresa tu contraseña actual">
            </div>
            <div class="form-group text-left">
                <label for="password_nueva">Nueva Contraseña:</label>
                <input id="password_nueva" 
                       type="password" 
                       class="form-control" 
                       placeholder="Mínimo 6 caracteres">
            </div>
            <div class="form-group text-left">
                <label for="password_confirmar">Confirmar Contraseña:</label>
                <input id="password_confirmar" 
                       type="password" 
                       class="form-control" 
                       placeholder="Repite la nueva contraseña">
            </div>
        `,
        width: '500px',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-check"></i> Cambiar',
        cancelButtonText: '<i class="fa fa-times"></i> Cancelar',
        preConfirm: () => {
            const password_actual = document.getElementById('password_actual').value;
            const password_nueva = document.getElementById('password_nueva').value;
            const password_confirmar = document.getElementById('password_confirmar').value;
            
            if (!password_actual || !password_nueva || !password_confirmar) {
                Swal.showValidationMessage('Por favor completa todos los campos');
                return false;
            }
            
            if (password_nueva !== password_confirmar) {
                Swal.showValidationMessage('Las contraseñas no coinciden');
                return false;
            }
            
            if (password_nueva.length < 6) {
                Swal.showValidationMessage('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            return {
                password_actual: password_actual,
                password_nueva: password_nueva
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar solicitud AJAX
            $.ajax({
                url: baseUrlPerfil + 'welcome/cambiar_password',
                type: 'POST',
                data: result.value,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}
