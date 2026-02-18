<!-- Content wrapper -->
<?php 
// Verificar si los datos vienen del servidor o necesitamos cargarlos dinámicamente
$carga_dinamica = !isset($usuario) || empty($usuario);
if ($carga_dinamica) {
    // Inicializar variables vacías para evitar errores PHP
    $usuario = array(
        'id_usuario' => '',
        'username' => '',
        'rol_original' => '',
        'id_cliente' => '',
        'nombre' => '',
        'apellidos' => '',
        'nombre_completo' => '',
        'carnet' => '',
        'email' => '',
        'telefono' => '',
        'direccion' => '',
        'avatar' => ''
    );
    $preferencias = array(
        'id_preferencia' => '',
        'tema_interfaz' => 'theme1',
        'idioma_preferido' => 'es',
        'notificaciones_email' => 1,
        'notificaciones_push' => 1
    );
    $estadisticas = array(
        'total_turnos' => 0,
        'turnos_completados' => 0,
        'turnos_pendientes' => 0,
        'turnos_cancelados' => 0,
        'ultimo_turno' => null
    );
}
?>
    <div class="container-fluid" id="perfilContainer">
        <!-- Breadcrumb -->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Mi Perfil</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb -->

        <div class="row">
            <!-- COLUMNA IZQUIERDA - INFORMACIÓN DEL USUARIO -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header  text-primary">
                        <i class="fa fa-user"></i> Información Personal
                    </div>
                    <div class="card-body">
                        <!-- Avatar del usuario -->
                        <div class="text-center mb-4">
                            <div class="avatar-container" style="position: relative; display: inline-block;">
                                <?php 
                                $avatar = 'https://ui-avatars.com/api/?name=Usuario&background=random&size=150';
                                if (isset($usuario['avatar']) && !empty($usuario['avatar'])) {
                                    $avatar = $usuario['avatar'];
                                } elseif (isset($usuario['nombre_completo'])) {
                                    $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($usuario['nombre_completo']) . '&background=random&size=150';
                                }
                                ?>
                                <img src="<?php echo $avatar; ?>" 
                                     class="rounded-circle" 
                                     alt="Avatar" 
                                     id="avatarPreview"
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #007bff;">
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        onclick="cambiarAvatar()"
                                        style="position: absolute; bottom: 0; right: 0; border-radius: 50%; width: 35px; height: 35px; padding: 0;">
                                    <i class="fa fa-camera"></i>
                                </button>
                            </div>
                            <h5 class="mt-3 mb-0"><?php echo isset($usuario['nombre_completo']) ? $usuario['nombre_completo'] : 'Usuario'; ?></h5>
                            <p class="text-white">
                                <span class="badge badge-<?php echo ($usuario['rol_original'] == 'Administrador') ? 'danger' : 'info'; ?>">
                                    <?php echo isset($usuario['rol_original']) ? $usuario['rol_original'] : 'Usuario'; ?>
                                </span>
                            </p>
                        </div>

                        <!-- Formulario de información del usuario -->
                        <form id="formUsuario">
                            <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo isset($usuario['user_id']) ? $usuario['user_id'] : ''; ?>">
                            <input type="hidden" id="idCliente" name="idCliente" value="<?php echo isset($usuario['id_cliente']) ? $usuario['id_cliente'] : ''; ?>">
                            
                            <div class="form-group">
                                <label for="userName">Usuario</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="userName" 
                                       name="userName"
                                       value="<?php echo isset($usuario['username']) ? $usuario['username'] : ''; ?>"
                                       readonly>
                                <small class="form-text text-white">El nombre de usuario no puede modificarse</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nombre">Nombre *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre"
                                           value="<?php echo isset($usuario['nombre']) ? $usuario['nombre'] : ''; ?>"
                                           required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="apellidos">Apellidos *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="apellidos" 
                                           name="apellidos"
                                           value="<?php echo isset($usuario['apellidos']) ? $usuario['apellidos'] : ''; ?>"
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="carnet">Carnet/CI *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="carnet" 
                                       name="carnet"
                                       value="<?php echo isset($usuario['carnet']) ? $usuario['carnet'] : ''; ?>"
                                       required
                                       maxlength="11">
                            </div>

                            <div class="form-group">
                                <label for="email">Correo Electrónico *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email"
                                       value="<?php echo isset($usuario['email']) ? $usuario['email'] : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono"
                                       value="<?php echo isset($usuario['telefono']) ? $usuario['telefono'] : ''; ?>"
                                       maxlength="20">
                            </div>

                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <textarea class="form-control" 
                                          id="direccion" 
                                          name="direccion"
                                          rows="2"><?php echo isset($usuario['direccion']) ? $usuario['direccion'] : ''; ?></textarea>
                            </div>
 <div class="form-group text-center">
                                <button type="button" class="btn btn-danger" onclick="cancelarEdicion()">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                           
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA - PREFERENCIAS DEL USUARIO -->
            <div class="col-lg-6">
                <!-- Card de Preferencias -->
                <div class="card">
                    <div class="card-header  text-primary">
                        <i class="fa fa-cog"></i> Preferencias del Sistema
                    </div>
                    <div class="card-body">
                        <form id="formPreferencias">
                            <input type="hidden" id="idPreferencia" name="idPreferencia" value="<?php echo isset($preferencias['id_preferencia']) ? $preferencias['id_preferencia'] : ''; ?>">
                            
                            <div class="form-group">
                                <label for="temaInterfaz">Tema de Interfaz</label>
                                <select class="form-control" id="temaInterfaz" name="temaInterfaz">
                                    <option value="theme1" <?php echo (isset($preferencias['tema_interfaz']) && $preferencias['tema_interfaz'] == 'theme1') ? 'selected' : ''; ?>>Tema 1 (Claro)</option>
                                    <option value="theme2" <?php echo (isset($preferencias['tema_interfaz']) && $preferencias['tema_interfaz'] == 'theme2') ? 'selected' : ''; ?>>Tema 2 (Oscuro)</option>
                                    <option value="theme3" <?php echo (isset($preferencias['tema_interfaz']) && $preferencias['tema_interfaz'] == 'theme3') ? 'selected' : ''; ?>>Tema 3 (Azul)</option>
                                    <option value="theme4" <?php echo (isset($preferencias['tema_interfaz']) && $preferencias['tema_interfaz'] == 'theme4') ? 'selected' : ''; ?>>Tema 4 (Verde)</option>
                                </select>
                                <small class="form-text text-white">Selecciona el tema visual de la aplicación</small>
                            </div>

                            <div class="form-group">
                                <label for="idiomaPreferido">Idioma Preferido</label>
                                <select class="form-control" id="idiomaPreferido" name="idiomaPreferido">
                                    <option value="es" <?php echo (isset($preferencias['idioma_preferido']) && $preferencias['idioma_preferido'] == 'es') ? 'selected' : ''; ?>>Español</option>
                                    <option value="en" <?php echo (isset($preferencias['idioma_preferido']) && $preferencias['idioma_preferido'] == 'en') ? 'selected' : ''; ?>>English</option>
                                    <option value="fr" <?php echo (isset($preferencias['idioma_preferido']) && $preferencias['idioma_preferido'] == 'fr') ? 'selected' : ''; ?>>Français</option>
                                    <option value="pt" <?php echo (isset($preferencias['idioma_preferido']) && $preferencias['idioma_preferido'] == 'pt') ? 'selected' : ''; ?>>Português</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="notificacionesEmail" 
                                           name="notificacionesEmail"
                                           <?php echo (isset($preferencias['notificaciones_email']) && $preferencias['notificaciones_email'] == 1) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="notificacionesEmail">
                                        <i class="fa fa-envelope"></i> Notificaciones por Email
                                    </label>
                                </div>
                                <small class="form-text text-white">Recibir notificaciones de turnos por correo electrónico</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="notificacionesPush" 
                                           name="notificacionesPush"
                                           <?php echo (isset($preferencias['notificaciones_push']) && $preferencias['notificaciones_push'] == 1) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="notificacionesPush">
                                        <i class="fa fa-bell"></i> Notificaciones Push
                                    </label>
                                </div>
                                <small class="form-text text-white">Recibir notificaciones en tiempo real</small>
                            </div>

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Guardar Preferencias
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card de Seguridad -->
                <div class="card mt-3">
                    <div class="card-header text-primary">
                        <i class="fa fa-shield"></i> Seguridad
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <button type="button" class="list-group-item list-group-item-action" onclick="cambiarPassword()">
                                <i class="fa fa-lock text-primary"></i> Cambiar Contraseña
                            </button>
                            <button type="button" class="list-group-item list-group-item-action" onclick="verSesionesActivas()">
                                <i class="fa fa-laptop text-info"></i> Sesiones Activas
                            </button>
                            <button type="button" class="list-group-item list-group-item-action" onclick="verHistorialAcceso()">
                                <i class="fa fa-history text-warning"></i> Historial de Acceso
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card de Estadísticas -->
                <div class="card mt-3">
                    <div class="card-header  text-primary">
                        <i class="fa fa-chart-bar"></i> Mis Estadísticas
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-primary"><?php echo isset($estadisticas['total_turnos']) ? $estadisticas['total_turnos'] : 0; ?></h3>
                                    <small class="text-white">Total Turnos</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-success"><?php echo isset($estadisticas['turnos_completados']) ? $estadisticas['turnos_completados'] : 0; ?></h3>
                                    <small class="text-white">Completados</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-warning"><?php echo isset($estadisticas['turnos_pendientes']) ? $estadisticas['turnos_pendientes'] : 0; ?></h3>
                                    <small class="text-white">Pendientes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="mb-0 text-danger"><?php echo isset($estadisticas['turnos_cancelados']) ? $estadisticas['turnos_cancelados'] : 0; ?></h3>
                                    <small class="text-white">Cancelados</small>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($estadisticas['ultimo_turno']) && $estadisticas['ultimo_turno']): ?>
                        <div class="mt-3 text-center">
                            <small class="text-white">Último turno: <?php echo date('d/m/Y', strtotime($estadisticas['ultimo_turno'])); ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End container-fluid-->

<!-- End content-wrapper-->

<!-- Scripts del perfil -->
<script src="<?= base_url('ui/assets/js/perfil.js') ?>"></script>
<?php if ($carga_dinamica): ?>
<script>
// Si los datos no vinieron del servidor, cargarlos dinámicamente
$(document).ready(function() {
    console.log('Perfil: Cargando datos del usuario dinámicamente...');
    cargarDatosUsuario();
});

function cargarDatosUsuario() {
    $.ajax({
        url: baseUrl + 'perfil/get_usuario_info',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Datos del usuario cargados:', response);
            if (response.success && response.data) {
                const usuario = response.data;
                const preferencias = response.preferencias || {};
                const estadisticas = response.estadisticas || {};
                
                // Actualizar campos del formulario
                $('#idUsuario').val(usuario.id_usuario || '');
                $('#idCliente').val(usuario.id_cliente || '');
                $('#userName').val(usuario.username || '');
                $('#nombre').val(usuario.nombre || '');
                $('#apellidos').val(usuario.apellidos || '');
                $('#carnet').val(usuario.carnet || '');
                $('#email').val(usuario.email || '');
                $('#telefono').val(usuario.telefono || '');
                $('#direccion').val(usuario.direccion || '');
                
                // Actualizar nombre completo en el encabezado
                $('h5.mt-3.mb-0').text(usuario.nombre_completo || 'Usuario');
                
                // Actualizar rol
                const rolBadge = (usuario.rol_original == 'Administrador') ? 'danger' : 'info';
                $('.badge').removeClass('badge-danger badge-info').addClass('badge-' + rolBadge);
                $('.badge').text(usuario.rol_original || 'Usuario');
                
                // Actualizar avatar
                let avatarUrl = 'https://ui-avatars.com/api/?name=Usuario&background=random&size=150';
                if (usuario.avatar && usuario.avatar !== '') {
                    avatarUrl = usuario.avatar;
                } else if (usuario.nombre_completo) {
                    avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(usuario.nombre_completo) + '&background=random&size=150';
                }
                $('#avatarPreview').attr('src', avatarUrl);
                
                // Cargar preferencias
                $('#idPreferencia').val(preferencias.id_preferencia || '');
                $('#temaInterfaz').val(preferencias.tema_interfaz || 'theme1');
                $('#idiomaPreferido').val(preferencias.idioma_preferido || 'es');
                $('#notificacionesEmail').prop('checked', preferencias.notificaciones_email == 1);
                $('#notificacionesPush').prop('checked', preferencias.notificaciones_push == 1);
                
                // Actualizar estadísticas
                actualizarEstadisticas(estadisticas);

				console.log("funcion",typeof guardarDatosOriginales === 'function');
				
                
                // Guardar datos originales
                if (typeof guardarDatosOriginales === 'function') {
                    guardarDatosOriginales();
                }
                
                console.log('Perfil cargado completamente');
                console.log('FormUsuario existe:', $('#formUsuario').length);
                console.log('FormPreferencias existe:', $('#formPreferencias').length);
            } else {
                console.error('Error al cargar datos del usuario');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los datos del perfil',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar usuario:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

function cargarPreferencias() {
    // Las preferencias se cargan junto con el usuario
    console.log('Preferencias cargadas');
}

function actualizarEstadisticas(estadisticas) {
    // Actualizar los números en las tarjetas de estadísticas
    $('.col-6:nth-of-type(1) .text-primary').text(estadisticas.total_turnos || 0);
    $('.col-6:nth-of-type(2) .text-success').text(estadisticas.turnos_completados || 0);
    $('.col-6:nth-of-type(3) .text-warning').text(estadisticas.turnos_pendientes || 0);
    $('.col-6:nth-of-type(4) .text-danger').text(estadisticas.turnos_cancelados || 0);
    
    // Actualizar fecha del último turno si existe
    if (estadisticas.ultimo_turno) {
        const fecha = new Date(estadisticas.ultimo_turno);
        const fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        if ($('.mt-3.text-center small').length === 0) {
            $('.card-body .row').after('<div class="mt-3 text-center"><small class="text-white">Último turno: ' + fechaFormateada + '</small></div>');
        } else {
            $('.mt-3.text-center small').html('Último turno: ' + fechaFormateada);
        }
    }
    
    console.log('Estadísticas actualizadas:', estadisticas);
}

function cargarEstadisticas(idCliente) {
    // Las estadísticas ya se cargan junto con get_usuario_info
    console.log('Estadísticas cargadas para cliente:', idCliente);
}
</script>
<?php endif; ?>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.custom-switch .custom-control-label::before {
    background-color: #dee2e6;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.bg-light {
    transition: all 0.3s ease;
}

.bg-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
