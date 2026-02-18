/* AGREGAR ESTE JAVASCRIPT AL FINAL DE TU SCRIPT EN index.php, ANTES DE </script> */

// Toggle mostrar/ocultar contraseña en registro
$('#toggleRegPassword').on('click', function() {
    const passwordField = $('#reg_password');
    const toggleIcon = $('#toggleRegIcon');
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        toggleIcon.removeClass('zmdi-eye').addClass('zmdi-eye-off');
    } else {
        passwordField.attr('type', 'password');
        toggleIcon.removeClass('zmdi-eye-off').addClass('zmdi-eye');
    }
});

// Toggle mostrar/ocultar contraseña en login
$('#togglePassword').on('click', function() {
    const passwordField = $('#password');
    const toggleIcon = $('#toggleIcon');
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        toggleIcon.removeClass('zmdi-eye').addClass('zmdi-eye-off');
    } else {
        passwordField.attr('type', 'password');
        toggleIcon.removeClass('zmdi-eye-off').addClass('zmdi-eye');
    }
});

// Abrir modal de registro desde el footer del login
$('.modal-footer a[href="#"]').on('click', function(e) {
    e.preventDefault();
    $('#loginModal').modal('hide');
    setTimeout(function() {
        $('#registerModal').modal('show');
    }, 500);
});

// Manejar el formulario de registro
$('#registerForm').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener valores
    var formData = {
        nombre: $('#reg_nombre').val().trim(),
        apellidos: $('#reg_apellidos').val().trim(),
        carnet: $('#reg_carnet').val().trim(),
        email: $('#reg_email').val().trim(),
        direccion: $('#reg_direccion').val().trim(),
        usuario: $('#reg_usuario').val().trim(),
        password: $('#reg_password').val(),
        terms: $('#terms').is(':checked')
    };
    
    // Validar campos
    if (!formData.nombre || !formData.apellidos || !formData.carnet || !formData.email || !formData.usuario || !formData.password) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos Vacíos',
            text: 'Por favor, completa todos los campos obligatorios',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Validar contraseña
    if (formData.password.length < 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña Débil',
            text: 'La contraseña debe tener al menos 6 caracteres',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Validar términos
    if (!formData.terms) {
        Swal.fire({
            icon: 'warning',
            title: 'Términos y Condiciones',
            text: 'Debes aceptar los términos y condiciones',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Validar email
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        Swal.fire({
            icon: 'warning',
            title: 'Email Inválido',
            text: 'Por favor, ingresa un email válido',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Creando cuenta...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición AJAX
    $.ajax({
        url: baseUrl + 'welcome/register',  // Deberás crear este método en el controlador
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro Exitoso!',
                    text: response.message || 'Tu cuenta ha sido creada correctamente',
                    confirmButtonColor: '#667eea'
                }).then(function() {
                    // Cerrar modal de registro
                    $('#registerModal').modal('hide');
                    
                    // Abrir modal de login
                    setTimeout(function() {
                        $('#loginModal').modal('show');
                        // Pre-llenar el usuario
                        $('#username').val(formData.usuario);
                        $('#password').focus();
                    }, 500);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el Registro',
                    text: response.message || 'No se pudo crear la cuenta',
                    confirmButtonColor: '#667eea'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en registro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.',
                confirmButtonColor: '#667eea'
            });
        }
    });
});

// Limpiar formularios al cerrar modales
$('#registerModal').on('hidden.bs.modal', function() {
    $('#registerForm')[0].reset();
});

$('#loginModal').on('hidden.bs.modal', function() {
    $('#loginForm')[0].reset();
});

// Focus en campos al abrir modales
$('#loginModal').on('shown.bs.modal', function() {
    $('#username').focus();
});

$('#registerModal').on('shown.bs.modal', function() {
    $('#reg_nombre').focus();
});
