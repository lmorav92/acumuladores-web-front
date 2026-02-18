<!--Start Historial de Turnos-->
<div class="container-fluid">
    
    <!-- Header -->
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-white mb-0">Historial de Turnos</h2>
            <p class="text-white-50">Registro completo de todos los turnos</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Turnos</h6>
                    <h2 id="statTotal">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Finalizados</h6>
                    <h2 id="statFinalizados">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Cancelados</h6>
                    <h2 id="statCancelados">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Tasa Asistencia</h6>
                    <h2 id="statTasa">0%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-filter-list"></i> Filtros Avanzados
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Período</label>
                            <select id="filtroPeriodo" class="form-control" onchange="cambiarPeriodo()">
                                <option value="mes_actual">Este Mes</option>
                                <option value="mes_anterior">Mes Anterior</option>
                                <option value="trimestre">Último Trimestre</option>
                                <option value="semestre">Último Semestre</option>
                                <option value="anio">Este Año</option>
                                <option value="personalizado">Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="campoFechaDesde" style="display:none;">
                            <label>Desde</label>
                            <input type="date" id="filtroFechaDesde" class="form-control">
                        </div>
                        <div class="col-md-2" id="campoFechaHasta" style="display:none;">
                            <label>Hasta</label>
                            <input type="date" id="filtroFechaHasta" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Estado</label>
                            <select id="filtroEstadoHistorial" class="form-control">
                                <option value="">Todos</option>
                                <option value="finalizado">Finalizados</option>
                                <option value="cancelado">Cancelados</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Cliente</label>
                            <input type="text" id="filtroCliente" class="form-control" 
                                   placeholder="Buscar cliente...">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" onclick="cargarHistorial()">
                                <i class="zmdi zmdi-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-time-restore"></i> Historial
                    <div class="float-right">
                        <button class="btn btn-sm btn-success" onclick="exportarExcel()">
                            <i class="zmdi zmdi-download"></i> Exportar Excel
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="exportarPDF()">
                            <i class="zmdi zmdi-file-text"></i> Exportar PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaHistorial">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Turno</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Duración</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaHistorial">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="zmdi zmdi-time zmdi-hc-3x text-muted"></i>
                                        <p>Cargando historial...</p>
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
// Asegurarse de que baseUrl esté disponible
var baseUrlHistorial = typeof baseUrl !== 'undefined' ? baseUrl : '';

$(document).ready(function() {
    console.log('Historial de turnos cargado');
    console.log('BaseUrl disponible:', baseUrlHistorial);
    cargarHistorial();
    cargarEstadisticas();
});

function cambiarPeriodo() {
    const periodo = $('#filtroPeriodo').val();
    
    if (periodo === 'personalizado') {
        $('#campoFechaDesde, #campoFechaHasta').show();
    } else {
        $('#campoFechaDesde, #campoFechaHasta').hide();
        cargarHistorial();
    }
}

function cargarHistorial() {
    console.log('Cargando historial...');
    
    const filtros = {
        periodo: $('#filtroPeriodo').val(),
        fecha_desde: $('#filtroFechaDesde').val(),
        fecha_hasta: $('#filtroFechaHasta').val(),
        estado: $('#filtroEstadoHistorial').val(),
        cliente: $('#filtroCliente').val()
    };
    
    console.log('Filtros:', filtros);
    
    $.ajax({
        url: baseUrlHistorial + 'turnos/historial_lista',
        type: 'GET',
        data: filtros,
        dataType: 'json',
        success: function(res) {
            console.log('Respuesta historial:', res);
            if (res.success && res.turnos) {
                mostrarHistorial(res.turnos);
            } else {
                console.error('Error en respuesta:', res);
                $('#listaHistorial').html(`
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            <i class="zmdi zmdi-alert-circle zmdi-hc-2x"></i>
                            <p>Error al cargar el historial</p>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Response:', xhr.responseText);
            $('#listaHistorial').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="zmdi zmdi-alert-circle zmdi-hc-2x"></i>
                        <p>Error de conexión: ${error}</p>
                    </td>
                </tr>
            `);
        }
    });
}

function cargarEstadisticas() {
    console.log('Cargando estadísticas...');
    
    $.ajax({
        url: baseUrlHistorial + 'turnos/estadisticas',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            console.log('Respuesta estadísticas:', res);
            if (res.success && res.estadisticas) {
                const stats = res.estadisticas;
                $('#statTotal').text(stats.total || 0);
                $('#statFinalizados').text(stats.finalizados || 0);
                $('#statCancelados').text(stats.cancelados || 0);
                $('#statTasa').text((stats.tasa_asistencia || 0) + '%');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
        }
    });
}

function mostrarHistorial(turnos) {
    let html = '';
    
    if (turnos.length === 0) {
        html = `
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="zmdi zmdi-info zmdi-hc-2x"></i>
                    <p>No hay registros en el historial</p>
                </td>
            </tr>
        `;
    } else {
        turnos.forEach((t, index) => {
            const estadoBadge = t.EstadoTurno === 'finalizado' ? 'badge-success' : 'badge-danger';
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${t.FechaTurno}</td>
                    <td>${t.HorarioTurno || 'N/A'}</td>
                    <td><strong>#${t.NumeroTurno}</strong></td>
                    <td>${t.NombreCompleto || t.NombreCliente + ' ' + t.ApellidosCliente}</td>
                    <td><span class="badge ${estadoBadge}">${t.EstadoDescripcion || t.EstadoTurno}</span></td>
                    <td>${t.Duracion || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verDetalleTurno(${t.IdTurno})">
                            <i class="zmdi zmdi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#listaHistorial').html(html);
}

function verDetalleTurno(idTurno) {
    console.log('Ver detalle del turno:', idTurno);
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo información del turno',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: baseUrlHistorial + 'turnos/detalle/' + idTurno,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            Swal.close();
            
            if (res.success && res.turno) {
                const t = res.turno;
                
                // Determinar badge del estado
                let estadoBadge = 'info';
                if (t.EstadoTurno === 'finalizado') estadoBadge = 'success';
                else if (t.EstadoTurno === 'cancelado') estadoBadge = 'danger';
                else if (t.EstadoTurno === 'en_espera') estadoBadge = 'warning';
                
                Swal.fire({
                    title: `<i class="zmdi zmdi-calendar-check"></i> Turno #${t.NumeroTurno}`,
                    html: `
                        <div class="text-left">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="text-primary">Información del Turno</h5>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Fecha:</strong> ${t.FechaTurno}</p>
                                    <p><strong>Horario:</strong> ${t.HorarioTurno || 'N/A'}</p>
                                    <p><strong>Número Turno:</strong> <span class="badge badge-secondary">#${t.NumeroTurno}</span></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Estado:</strong> <span class="badge badge-${estadoBadge}">${t.EstadoDescripcion || t.EstadoTurno}</span></p>
                                    <p><strong>Duración:</strong> ${t.Duracion || 'N/A'}</p>
                                    <p><strong>ID:</strong> ${t.IdTurno}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="text-primary">Información del Cliente</h5>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Nombre:</strong> ${t.NombreCompleto || (t.NombreCliente + ' ' + t.ApellidosCliente)}</p>
                                    <p><strong>Carnet:</strong> ${t.CarnetCliente || 'N/A'}</p>
                                    <p><strong>Email:</strong> ${t.Email || 'N/A'}</p>
                                    <p><strong>Teléfono:</strong> ${t.Telefono || 'N/A'}</p>
                                </div>
                            </div>
                            ${t.Observaciones ? `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="text-primary">Observaciones</h5>
                                    <hr>
                                    <p>${t.Observaciones}</p>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'text-dark'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la información del turno',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error al obtener detalle:', error);
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

function exportarExcel() {
    const filtros = {
        periodo: $('#filtroPeriodo').val(),
        fecha_desde: $('#filtroFechaDesde').val(),
        fecha_hasta: $('#filtroFechaHasta').val(),
        estado: $('#filtroEstadoHistorial').val(),
        cliente: $('#filtroCliente').val()
    };
    
    // Convertir filtros a query string
    const queryString = $.param(filtros);
    window.location.href = baseUrlHistorial + 'turnos/exportar_excel?' + queryString;
}

function exportarPDF() {
    const filtros = {
        periodo: $('#filtroPeriodo').val(),
        fecha_desde: $('#filtroFechaDesde').val(),
        fecha_hasta: $('#filtroFechaHasta').val(),
        estado: $('#filtroEstadoHistorial').val(),
        cliente: $('#filtroCliente').val()
    };
    
    // Convertir filtros a query string
    const queryString = $.param(filtros);
    window.location.href = baseUrlHistorial + 'turnos/exportar_pdf?' + queryString;
}
</script>

<!--End Historial de Turnos-->
