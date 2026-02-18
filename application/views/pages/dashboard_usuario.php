<!--Start Dashboard Usuario Content-->
<div class="container-fluid">
    
    <!-- Header Section -->
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-white mb-0">
                <i class="zmdi zmdi-account-circle"></i> Bienvenido, 
                <?php echo isset($user['nombre']) ? $user['nombre'] : 'Usuario'; ?>
            </h2>
            <p class="text-white-50">Panel de control personal</p>
        </div>
    </div>

    <!-- Cards de Estadísticas Personales -->
    <div class="row">
        <div class="col-12 col-lg-6 col-xl-3">
            <div class="card gradient-deepblue">
                <div class="card-body">
                    <h5 class="text-white mb-0">
                        <span id="misTurnosTotales">0</span>
                        <span class="float-right"><i class="zmdi zmdi-calendar"></i></span>
                    </h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar bg-white" style="width:100%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Mis Turnos Totales</p>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6 col-xl-3">
            <div class="card gradient-scooter">
                <div class="card-body">
                    <h5 class="text-white mb-0">
                        <span id="turnosPendientesUsuario">0</span>
                        <span class="float-right"><i class="zmdi zmdi-time"></i></span>
                    </h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar bg-white" style="width:100%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Turnos Pendientes</p>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6 col-xl-3">
            <div class="card gradient-ohhappiness">
                <div class="card-body">
                    <h5 class="text-white mb-0">
                        <span id="turnosCompletadosUsuario">0</span>
                        <span class="float-right"><i class="zmdi zmdi-check-circle"></i></span>
                    </h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar bg-white" style="width:100%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Turnos Completados</p>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6 col-xl-3">
            <div class="card gradient-bloody">
                <div class="card-body">
                    <h5 class="text-white mb-0">
                        <span id="turnosCanceladosUsuario">0</span>
                        <span class="float-right"><i class="zmdi zmdi-close-circle"></i></span>
                    </h5>
                    <div class="progress my-3" style="height:3px;">
                        <div class="progress-bar bg-white" style="width:100%"></div>
                    </div>
                    <p class="mb-0 text-white small-font">Turnos Cancelados</p>
                </div>
            </div>
        </div>
    </div><!--End Row-->

    <!-- Próximo Turno y Acciones Rápidas -->
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="zmdi zmdi-calendar-check"></i> Próximo Turno
                </div>
                <div class="card-body" id="proximoTurnoCard">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="zmdi zmdi-plus-circle"></i> Acciones Rápidas
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action" class="menu-link" data-page="turnos">
                            <i class="zmdi zmdi-calendar-note text-success"></i> Reservar Nuevo Turno
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="verMisTurnos()">
                            <i class="zmdi zmdi-view-list text-primary"></i> Ver Mis Turnos
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="verPerfil()">
                            <i class="zmdi zmdi-account text-info"></i> Mi Perfil
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="verHistorial()">
                            <i class="zmdi zmdi-time-restore text-warning"></i> Historial
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--End Row-->

    <!-- Mis Turnos Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-assignment"></i> Mis Últimos Turnos
                    <div class="card-action">
                        <button class="btn btn-sm btn-primary" onclick="loadDashboardUsuario()">
                            <i class="zmdi zmdi-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Número Turno</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableMisTurnos">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!--End Row-->

    <!-- Información Personal -->
    <div class="row mt-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="zmdi zmdi-account-box"></i> Mi Información
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            <?php 
                            $avatar = 'https://ui-avatars.com/api/?name=Usuario&background=random&size=150';
                            if (isset($user['nombre'])) {
                                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre']) . '&background=random&size=150';
                            }
                            ?>
                            <img src="<?php echo $avatar; ?>" 
                                 class="rounded-circle img-thumbnail" 
                                 alt="Avatar"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="col-8">
                            <h5 class="mb-2">
                                <?php echo isset($user['nombre']) ? $user['nombre'] : 'Usuario'; ?>
                            </h5>
                            <p class="mb-1">
                                <i class="zmdi zmdi-account-circle text-primary"></i> 
                                <strong>Usuario:</strong> <?php echo isset($user['usuario']) ? $user['usuario'] : 'N/A'; ?>
                            </p>
                            <p class="mb-1">
                                <i class="zmdi zmdi-card text-info"></i> 
                                <strong>Carnet:</strong> <?php echo isset($user['carnet']) ? $user['carnet'] : 'N/A'; ?>
                            </p>
                            <p class="mb-1">
                                <i class="zmdi zmdi-email text-success"></i> 
                                <strong>Email:</strong> <?php echo isset($user['email']) ? $user['email'] : 'N/A'; ?>
                            </p>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="verPerfil()">
                                <i class="zmdi zmdi-edit"></i> Editar Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="zmdi zmdi-info"></i> Información Importante
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="zmdi zmdi-info-outline"></i> Instrucciones</h6>
                        <ul class="mb-0 pl-3">
                            <li>Puedes reservar turnos con anticipación</li>
                            <li>Recibirás notificaciones cuando tu turno esté próximo</li>
                            <li>Puedes cancelar tus turnos hasta 1 hora antes</li>
                            <li>Mantén tu información actualizada</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <h6><i class="zmdi zmdi-time"></i> Horario de Atención</h6>
                        <p class="mb-0">
                            <strong>Lunes a Viernes:</strong> 8:00 AM - 5:00 PM<br>
                            <strong>8 turnos disponibles por día</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div><!--End Row-->

</div>

<style>
/* Estilos para Dashboard Usuario */
.gradient-deepblue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-scooter {
    background: linear-gradient(135deg, #17ead9 0%, #6078ea 100%);
}

.gradient-bloody {
    background: linear-gradient(135deg, #f54ea2 0%, #ff7676 100%);
}

.gradient-ohhappiness {
    background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
}

.card-body h5 {
    font-size: 32px;
    font-weight: 600;
}

.small-font {
    font-size: 13px;
    letter-spacing: 0.5px;
}

.list-group-item-action {
    cursor: pointer;
    transition: all 0.3s;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.list-group-item-action i {
    font-size: 20px;
    margin-right: 10px;
}

.proximo-turno-destacado {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
}

.proximo-turno-destacado h2 {
    font-size: 3rem;
    font-weight: bold;
    margin: 1rem 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    cursor: pointer;
}
</style>

<script>
// Variables globales
var idClienteActual = <?php echo isset($user['id_cliente']) ? $user['id_cliente'] : 0; ?>;

// Función principal para cargar dashboard de usuario
function loadDashboardUsuario() {
    console.log("Cargando dashboard de usuario...");
    console.log("ID Cliente:", idClienteActual);
    
    if (!idClienteActual || idClienteActual === 0) {
        console.error('No se pudo obtener el ID del cliente');
        return;
    }
    
    loadEstadisticasUsuario();
    loadProximoTurno();
    loadMisTurnosRecientes();
}

// Cargar estadísticas del usuario
function loadEstadisticasUsuario() {
    console.log("Cargando estadísticas personales...");
    
    $.ajax({
        url: baseUrl + 'dashboard_usuario/mis_estadisticas',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Estadísticas recibidas:', data);
            
            if(data.success && data.stats) {
                // Actualizar las cards con animación
                $('#misTurnosTotales').text(data.stats.total_turnos);
                $('#turnosPendientesUsuario').text(data.stats.turnos_pendientes);
                $('#turnosCompletadosUsuario').text(data.stats.turnos_completados);
                $('#turnosCanceladosUsuario').text(data.stats.turnos_cancelados);
                
                console.log('Estadísticas actualizadas correctamente');
            } else {
                console.error('Error en respuesta:', data.message);
                if (data.debug) {
                    console.log('Debug info:', data.debug);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX al cargar estadísticas:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            
            // Mostrar valores en 0 en caso de error
            $('#misTurnosTotales').text('0');
            $('#turnosPendientesUsuario').text('0');
            $('#turnosCompletadosUsuario').text('0');
            $('#turnosCanceladosUsuario').text('0');
        }
    });
}

// Cargar próximo turno
function loadProximoTurno() {
    $.ajax({
        url: baseUrl + 'dashboard_usuario/proximo_turno',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Próximo turno:', data);
            let html = '';
            
            if(data.success && data.turno) {
                const t = data.turno;
                let estadoBadge = 'badge-warning';
                let estadoTexto = t.EstadoTurno;
                
                if(t.EstadoTurno == 'reservado') {
                    estadoBadge = 'badge-primary';
                    estadoTexto = 'Reservado';
                } else if(t.EstadoTurno == 'en_espera') {
                    estadoBadge = 'badge-info';
                    estadoTexto = 'En Espera';
                } else if(t.EstadoTurno == 'atendiendo') {
                    estadoBadge = 'badge-success';
                    estadoTexto = 'Atendiendo';
                }
                
                html = `
                    <div class="proximo-turno-destacado">
                        <h5 class="mb-3"><i class="zmdi zmdi-calendar-check"></i> Tu Próximo Turno</h5>
                        <h2 class="mb-3">Turno #${t.NumeroTurno}</h2>
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="mb-0"><i class="zmdi zmdi-calendar"></i> Fecha</p>
                                <h5>${t.FechaTurno}</h5>
                            </div>
                            <div class="col-6">
                                <p class="mb-0"><i class="zmdi zmdi-time"></i> Horario</p>
                                <h5>${t.HorarioTurno}</h5>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">
                            <span class="badge ${estadoBadge} p-2" style="font-size: 1rem;">
                                ${estadoTexto}
                            </span>
                        </p>
                        <button class="btn btn-light btn-lg mt-3" onclick="verDetallesTurno(${t.IdTurno})">
                            <i class="zmdi zmdi-eye"></i> Ver Detalles
                        </button>
                    </div>
                `;
            } else {
                html = `
                   <div class="text-center py-5">
    <i class="zmdi zmdi-calendar text-muted" style="font-size: 5rem;"></i>
    <h4 class="mt-3 text-muted">No tienes turnos próximos</h4>
    <p class="text-muted">¿Deseas reservar uno?</p>
    <button class="btn btn-primary btn-lg menu-link" data-page="turnos" onclick="loadPage('turnos')">
        <i class="zmdi zmdi-plus-circle"></i> Reservar Turno
    </button>
</div>
                `;
            }
            
            $('#proximoTurnoCard').html(html);
        },
        error: function(xhr) {
            console.error('Error al cargar próximo turno:', xhr.responseText);
            $('#proximoTurnoCard').html(`
                <div class="text-center py-5">
                    <i class="zmdi zmdi-alert-circle text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-danger">Error al cargar información</p>
                </div>
            `);
        }
    });
}

// Cargar mis turnos recientes
function loadMisTurnosRecientes() {
    $.ajax({
        url: baseUrl + 'dashboard_usuario/mis_turnos',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Mis turnos:', data);
            let html = '';
            
            if(data.success && data.turnos && data.turnos.length > 0) {
                data.turnos.forEach(t => {
                    let badgeClass = 'badge-warning';
                    let estadoText = t.EstadoTurno;
                    let iconoAccion = '';
                    
                    if(t.EstadoTurno == 'finalizado') {
                        badgeClass = 'badge-success';
                        estadoText = 'Finalizado';
                    } else if(t.EstadoTurno == 'cancelado') {
                        badgeClass = 'badge-danger';
                        estadoText = 'Cancelado';
                    } else if(t.EstadoTurno == 'en_espera') {
                        badgeClass = 'badge-info';
                        estadoText = 'En Espera';
                        iconoAccion = `<button class="btn btn-sm btn-danger" onclick="cancelarTurno(${t.IdTurno})">
                            <i class="zmdi zmdi-close"></i> Cancelar
                        </button>`;
                    } else if(t.EstadoTurno == 'atendiendo') {
                        badgeClass = 'badge-primary';
                        estadoText = 'Atendiendo';
                    } else if(t.EstadoTurno == 'reservado') {
                        badgeClass = 'badge-warning';
                        estadoText = 'Reservado';
                        iconoAccion = `<button class="btn btn-sm btn-danger" onclick="cancelarTurno(${t.IdTurno})">
                            <i class="zmdi zmdi-close"></i> Cancelar
                        </button>`;
                    }
                    
                    html += `
                        <tr onclick="verDetallesTurno(${t.IdTurno})">
                            <td>#${t.IdTurno}</td>
                            <td>${t.FechaTurno}</td>
                            <td>${t.HorarioTurno || 'N/A'}</td>
                            <td><span class="badge badge-secondary">${t.NumeroTurno}</span></td>
                            <td><span class="badge ${badgeClass}">${estadoText}</span></td>
                            <td onclick="event.stopPropagation();">${iconoAccion}</td>
                        </tr>`;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="6" class="text-center">
                            <p class="text-muted my-3">No tienes turnos registrados</p>
                            <button class="btn btn-primary" class="menu-link" data-page="turnos">
                                <i class="zmdi zmdi-plus"></i> Reservar Mi Primer Turno
                            </button>
                        </td>
                    </tr>`;
            }
            
            $('#tableMisTurnos').html(html);
        },
        error: function(xhr) {
            console.error('Error al cargar turnos:', xhr.responseText);
            $('#tableMisTurnos').html(`
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error al cargar tus turnos
                    </td>
                </tr>`);
        }
    });
}

// Funciones de acciones
function reservarTurno() {
    loadPage('reservar_turno');
}

function verMisTurnos() {
    loadPage('mis_turnos');
}

function verHistorial() {
    loadPage('historial_turnos');
}

function verDetallesTurno(idTurno) {
    Swal.fire({
        title: 'Detalles del Turno #' + idTurno,
        html: '<p>Cargando información...</p>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: baseUrl + 'turnos/detalle/' + idTurno,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                const t = data.turno;
                Swal.fire({
                    title: 'Turno #' + idTurno,
                    html: `
                        <div class="text-left">
                            <p><strong>Fecha:</strong> ${t.FechaTurno}</p>
                            <p><strong>Horario:</strong> ${t.HorarioTurno}</p>
                            <p><strong>Número:</strong> ${t.NumeroTurno}</p>
                            <p><strong>Estado:</strong> <span class="badge badge-info">${t.EstadoTurno}</span></p>
                        </div>
                    `,
                    confirmButtonText: 'Cerrar'
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información', 'error');
        }
    });
}

function cancelarTurno(idTurno) {
    Swal.fire({
        title: '¿Cancelar Turno?',
        text: '¿Estás seguro de que deseas cancelar este turno?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl + 'turnos/cancelar/' + idTurno,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.success) {
                        Swal.fire('Cancelado', data.message, 'success');
                        loadDashboardUsuario();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cancelar el turno', 'error');
                }
            });
        }
    });
}

// Inicializar dashboard cuando el documento esté listo
// Y también cuando se cargue dinámicamente
(function() {
    console.log("Inicializando dashboard...");
    
    // Esperar a que Chart.js esté disponible
    if (typeof Chart !== 'undefined') {
        loadDashboardUsuario();
    } else {
        console.log('Esperando a que Chart.js se cargue...');
        setTimeout(function() {
            if (typeof Chart !== 'undefined') {
                loadDashboardUsuario();
            }
        }, 500);
    }
    
    // Auto-refresh cada 5 minutos
    setInterval(loadDashboardUsuario, 300000);
})();
</script>

<!--End Dashboard Usuario Content-->
