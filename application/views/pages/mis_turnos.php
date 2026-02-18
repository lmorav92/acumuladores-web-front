<!--Start Mis Turnos-->
<div class="container-fluid">
    
    <!-- Header -->
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-white mb-0">Mis Turnos</h2>
            <p class="text-white-50">Lista de todos tus turnos reservados</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-filter-list"></i> Filtros
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Estado</label>
                            <select id="filtroEstado" class="form-control">
                                <option value="">Todos</option>
                                <option value="reservado">Reservado</option>
                                <option value="en_espera">En Espera</option>
                                <option value="atendiendo">Atendiendo</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Desde</label>
                            <input type="date" id="filtroFechaDesde" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Hasta</label>
                            <input type="date" id="filtroFechaHasta" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" onclick="cargarMisTurnos()">
                                <i class="zmdi zmdi-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Turnos -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-view-list"></i> Mis Turnos
                    <button class="btn btn-sm btn-success float-right" onclick="loadPage('turnos')">
                        <i class="zmdi zmdi-plus"></i> Reservar Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablaMisTurnos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Turno</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaMisTurnos">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="zmdi zmdi-time zmdi-hc-3x text-muted"></i>
                                        <p>Cargando turnos...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    cargarMisTurnos();
    
    // Auto-actualizar cada 30 segundos
    setInterval(cargarMisTurnos, 30000);
});

function cargarMisTurnos() {
    const filtros = {
        estado: $('#filtroEstado').val(),
        fecha_desde: $('#filtroFechaDesde').val(),
        fecha_hasta: $('#filtroFechaHasta').val()
    };
    
    $.ajax({
        url: '<?= base_url("turnos/mis_turnos_lista") ?>',
        type: 'GET',
        data: filtros,
        dataType: 'json',
        success: function(res) {
            if (res.success && res.turnos) {
                mostrarTurnos(res.turnos);
            } else {
                $('#listaMisTurnos').html(`
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="zmdi zmdi-info zmdi-hc-2x"></i>
                            <p>No hay turnos para mostrar</p>
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#listaMisTurnos').html(`
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <i class="zmdi zmdi-alert-triangle zmdi-hc-2x"></i>
                        <p>Error al cargar turnos</p>
                    </td>
                </tr>
            `);
        }
    });
}

function mostrarTurnos(turnos) {
    let html = '';
    
    if (turnos.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="zmdi zmdi-info zmdi-hc-2x"></i>
                    <p>No hay turnos registrados</p>
                </td>
            </tr>
        `;
    } else {
        turnos.forEach((t, index) => {
            const estadoBadge = getEstadoBadge(t.EstadoTurno);
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${t.FechaTurno}</td>
                    <td>${t.HorarioTurno}</td>
                    <td><strong>#${t.NumeroTurno}</strong></td>
                    <td>${t.NombreCompleto}</td>
                    <td><span class="badge ${estadoBadge}">${t.EstadoDescripcion}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verDetalleTurno(${t.IdTurno})" 
                                title="Ver detalles">
                            <i class="zmdi zmdi-eye"></i>
                        </button>
                        ${t.EstadoTurno === 'reservado' || t.EstadoTurno === 'en_espera' ? `
                            <button class="btn btn-sm btn-danger" onclick="cancelarTurno(${t.IdTurno})"
                                    title="Cancelar turno">
                                <i class="zmdi zmdi-close"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });
    }
    
    $('#listaMisTurnos').html(html);
}

function getEstadoBadge(estado) {
    const badges = {
        'reservado': 'badge-warning',
        'en_espera': 'badge-info',
        'atendiendo': 'badge-primary',
        'finalizado': 'badge-secondary',
        'cancelado': 'badge-danger'
    };
    return badges[estado] || 'badge-secondary';
}

function cancelarTurno(idTurno) {
    Swal.fire({
        title: '¿Cancelar turno?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("turnos/cancelar/") ?>' + idTurno,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Cancelado!', 'El turno ha sido cancelado', 'success');
                        cargarMisTurnos();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}
</script>

<!--End Mis Turnos-->
