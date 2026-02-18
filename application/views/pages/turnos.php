<!--Start Gestión de Turnos-->
<div class="container-fluid">
    
    <!-- Header -->
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-white mb-0">Gestión de Turnos</h2>
            <p class="text-white-50">Calendario y reservas de turnos</p>
        </div>
    </div>

    <!-- Controles del Calendario -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="zmdi zmdi-calendar"></i> 
                        <span id="mesActualTitulo">Enero 2025</span>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarMes(-1)">
                            <i class="zmdi zmdi-chevron-left"></i> Anterior
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="irHoy()">
                            Hoy
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="cambiarMes(1)">
                            Siguiente <i class="zmdi zmdi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Calendario -->
                    <div class="calendario-container">
                        <div class="calendario-header">
                            <div class="dia-nombre">Dom</div>
                            <div class="dia-nombre">Lun</div>
                            <div class="dia-nombre">Mar</div>
                            <div class="dia-nombre">Mié</div>
                            <div class="dia-nombre">Jue</div>
                            <div class="dia-nombre">Vie</div>
                            <div class="dia-nombre">Sáb</div>
                        </div>
                        <div class="calendario-dias" id="calendarioDias">
                            <!-- Días del mes se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Turnos del Día Seleccionado -->
    <div class="row mt-3" id="seccionTurnos" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="zmdi zmdi-time"></i> 
                    Turnos Disponibles - <span id="fechaSeleccionada">--</span>
                </div>
                <div class="card-body">
                    <div class="row" id="listaTurnos">
                        <!-- Turnos se cargarán aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal para Reservar Turno - ACTUALIZADO -->
<div class="modal fade" id="modalReservarTurno" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formReservarTurno">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="zmdi zmdi-calendar-check"></i> 
                        Reservar Turno
                    </h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="fecha_turno" id="fecha_turno">
                    <input type="hidden" name="numero_turno" id="numero_turno">
                    <input type="hidden" name="hora_inicio" id="hora_inicio">
                    <input type="hidden" name="hora_fin" id="hora_fin">
                    <input type="hidden" name="horario_descripcion" id="horario_descripcion">

                    <!-- Información del Turno -->
                    <div class="alert alert-info p-2">
                        <h5><i class="zmdi zmdi-info"></i> Información del Turno</h5>
                        <p class="mb-1"><strong>Fecha:</strong> <span id="info_fecha">--</span></p>
                        <p class="mb-1"><strong>Horario:</strong> <span id="info_horario">--</span></p>
                        <p class="mb-0"><strong>Turno:</strong> #<span id="info_numero">--</span></p>
                    </div>

                    <!-- Selección de Cliente -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select name="id_cliente" id="id_cliente" class="form-control" required>
                                    <option value="">Seleccionar cliente...</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" onclick="abrirModalNuevoCliente()">
                                        <i class="zmdi zmdi-account-add"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- NUEVO: Selección de Barbero -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                Barbero <span class="text-danger">*</span>
                            </label>
                            <select name="id_barbero" id="id_barbero" class="form-control" required>
                                <option value="">Seleccionar barbero...</option>
                            </select>
                            <small class="text-muted" id="especialidad_barbero"></small>
                        </div>

                        <!-- NUEVO: Selección de Servicio -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">
                                Servicio <span class="text-danger">*</span>
                            </label>
                            <select name="id_servicio" id="id_servicio" class="form-control" required>
                                <option value="">Seleccionar servicio...</option>
                            </select>
                            <small class="text-muted" id="precio_servicio"></small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3" 
                                placeholder="Notas adicionales del turno (opcional)..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="zmdi zmdi-check"></i> Reservar Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles del Turno - ACTUALIZADO -->
<div class="modal fade" id="modalDetallesTurno" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="zmdi zmdi-file-text"></i> 
                    Detalles del Turno
                </h5>
            </div>
            <div class="modal-body">
                <div id="contenidoDetalles" class="text-center">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="btnAtender" style="display:none;">
                    <i class="zmdi zmdi-assignment-account"></i> Marcar como Atendiendo
                </button>
                <button type="button" class="btn btn-success" id="btnFinalizar" style="display:none;">
                    <i class="zmdi zmdi-check-circle"></i> Finalizar
                </button>
                <button type="button" class="btn btn-secondary" id="btnCancelar" style="display:none;">
                    <i class="zmdi zmdi-close-circle"></i> Cancelar Turno
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>



<script>
// Variables globales
let mesActual = new Date().getMonth();
let añoActual = new Date().getFullYear();
let fechaSeleccionada = null;

// Nombres de meses
const nombresMeses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

// Inicializar
$(document).ready(function() {
    console.log("Inicializando gestión de turnos...");
    cargarClientes();
	cargarBarberos(); // NUEVO
    cargarServicios(); // NUEVO
    generarCalendario();
    
    // Handler del formulario de reserva
    $('#formReservarTurno').on('submit', function(e) {
        e.preventDefault();
        reservarTurno();
    });

	 // NUEVO: Actualizar especialidad al seleccionar barbero
    $('#id_barbero').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const especialidad = selectedOption.data('especialidad');
        if (especialidad) {
            $('#especialidad_barbero').text('Especialidad: ' + especialidad);
        } else {
            $('#especialidad_barbero').text('');
        }
    });
    
    // NUEVO: Actualizar precio al seleccionar servicio
    $('#id_servicio').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const precio = selectedOption.data('precio');
        if (precio) {
            $('#precio_servicio').text('Precio: $' + parseFloat(precio).toFixed(2));
        } else {
            $('#precio_servicio').text('');
        }
    });
});

// NUEVO: Cargar barberos
function cargarBarberos() {
    $.ajax({
        url: '<?= base_url("turnos/lista_barberos") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.barberos) {
                let options = '<option value="">Seleccionar barbero...</option>';
                
                data.barberos.forEach(function(b) {
                    options += `<option value="${b.IdBarbero}" data-especialidad="${b.Especialidad || ''}">${b.NombreCompleto}${b.Especialidad ? ' - ' + b.Especialidad : ''}</option>`;
                });
                
                $('#id_barbero').html(options);
            }
        },
        error: function() {
            $('#id_barbero').html('<option value="">Error al cargar barberos</option>');
        }
    });
}

// NUEVO: Cargar servicios
function cargarServicios() {
    $.ajax({
        url: '<?= base_url("turnos/lista_servicios") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.servicios) {
                let options = '<option value="">Seleccionar servicio...</option>';
                
                data.servicios.forEach(function(s) {
                    options += `<option value="${s.IdPelado}" data-precio="${s.PrecioPelado}">${s.NombrePelado} - $${parseFloat(s.PrecioPelado).toFixed(2)}</option>`;
                });
                
                $('#id_servicio').html(options);
            }
        },
        error: function() {
            $('#id_servicio').html('<option value="">Error al cargar servicios</option>');
        }
    });
}

// Generar calendario
function generarCalendario() {
    const primerDia = new Date(añoActual, mesActual, 1);
    const ultimoDia = new Date(añoActual, mesActual + 1, 0);
    const diasMes = ultimoDia.getDate();
    const diaSemanaInicio = primerDia.getDay();
    
    // Obtener fecha actual para comparación
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Resetear hora para comparar solo fechas
    
    // Actualizar título
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $('#mesActualTitulo').text(`${meses[mesActual]} ${añoActual}`);
    
    let html = '';
    
    // Días vacíos del inicio
    for(let i = 0; i < diaSemanaInicio; i++) {
        html += '<div class="dia-celda vacio"></div>';
    }
    
    // Días del mes
    for(let dia = 1; dia <= diasMes; dia++) {
        const fecha = new Date(añoActual, mesActual, dia);
        fecha.setHours(0, 0, 0, 0);
        const fechaStr = `${añoActual}-${String(mesActual + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        
        // Verificar si es día pasado
        const esPasado = fecha < hoy;
        
        // Determinar si es hoy
        const esHoy = fecha.getTime() === hoy.getTime();
        
        let clases = 'dia-celda';
        if (esHoy) clases += ' hoy';
        if (esPasado) clases += ' pasado'; // Nueva clase para días pasados
        
        html += `
            <div class="${clases}" 
                 data-fecha="${fechaStr}" 
                 ${esPasado ? 'style="cursor: not-allowed; opacity: 0.5;"' : 'onclick="seleccionarDia(\'' + fechaStr + '\')"'}>
                <div class="dia-numero">${dia}</div>
                <div class="dia-info" id="info-${fechaStr}">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                </div>
            </div>
        `;
    }
    
    $('#calendarioDias').html(html);
    
    // Cargar información de turnos
    cargarInfoTurnos();
}

// Cargar información de turnos del mes
function cargarInfoTurnos() {
    const primerDia = `${añoActual}-${String(mesActual + 1).padStart(2, '0')}-01`;
    const ultimoDia = new Date(añoActual, mesActual + 1, 0);
    const ultimoDiaStr = `${añoActual}-${String(mesActual + 1).padStart(2, '0')}-${String(ultimoDia.getDate()).padStart(2, '0')}`;
    
    $.ajax({
        url: '<?= base_url("turnos/resumen_mes") ?>',
        type: 'GET',
        data: {
            fecha_inicio: primerDia,
            fecha_fin: ultimoDiaStr
        },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.dias) {
                data.dias.forEach(function(dia) {
                    const infoHtml = `
                        <span class="badge badge-turno badge-success">${dia.Disponibles} disp.</span>
                        <span class="badge badge-turno badge-warning">${dia.Reservados} res.</span>
                    `;
                    $(`#info-${dia.Fecha}`).html(infoHtml);
                });
            }
        },
        error: function() {
            $('.dia-info .spinner-border').remove();
        }
    });
}

// Seleccionar un día del calendario
function seleccionarDia(fecha) {
    // Validar que no sea una fecha pasada
    const fechaSeleccionadaObj = new Date(fecha + 'T00:00:00');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    if (fechaSeleccionadaObj < hoy) {
        Swal.fire({
            icon: 'warning',
            title: 'Fecha no válida',
            text: 'No puedes reservar turnos en fechas pasadas'
        });
        return;
    }
    
    fechaSeleccionada = fecha;
    
    // Remover selección anterior
    $('.dia-celda').removeClass('seleccionado');
    
    // Marcar como seleccionado
    $(`[data-fecha="${fecha}"]`).addClass('seleccionado');
    
    // Mostrar sección de turnos y cargar
    $('#seccionTurnos').show();
    $('#fechaSeleccionada').text(formatearFecha(fecha));
    cargarTurnosDia(fecha);
}

// Cargar turnos de un día específico
// Cargar turnos de un día específico - VERSIÓN CORREGIDA
function cargarTurnosDia(fecha) {
    $('#listaTurnos').html('<div class="col-12 text-center"><div class="spinner-border" role="status"></div></div>');
    
    $.ajax({
        url: '<?= base_url("turnos/turnos_dia") ?>',
        type: 'GET',
        data: { fecha: fecha },
        dataType: 'json',
        success: function(data) {
            console.log("Turnos recibidos:", data);
            
            if (data.turnos && data.turnos.length > 0) {
                let html = '';
                
                data.turnos.forEach(function(turno) {
                    console.log(`Turno #${turno.NumeroTurno}:`, {
                        Disponible: turno.Disponible,
                        Estado: turno.Estado,
                        EsMio: turno.EsMio,
                        IdTurno: turno.IdTurno
                    });
                    
                    // Determinar disponibilidad y estado
                    const disponible = turno.Disponible == 1;
                    const esMio = turno.EsMio === true || turno.EsMio === 1;
                    
                    // Determinar el estado real
                    let estado, estadoTexto, badgeClass, clickHandler;
                    
                    if (disponible) {
                        // TURNO DISPONIBLE
                        estado = 'disponible';
                        estadoTexto = 'Disponible';
                        badgeClass = 'badge-success';
                        clickHandler = `onclick="abrirModalReserva('${fecha}', ${turno.NumeroTurno}, '${turno.HorarioDescripcion}', '${turno.HoraInicio}', '${turno.HoraFin}')"`;
                    } else if (esMio) {
                        // ES MI TURNO
                        estado = turno.Estado;
                        estadoTexto = formatearEstado(turno.Estado);
                        badgeClass = getBadgeClass(turno.Estado);
                        clickHandler = `onclick="verDetallesTurno(${turno.IdTurno})"`;
                    } else {
                        // TURNO OCUPADO POR OTRO USUARIO
                        estado = 'ocupado';
                        estadoTexto = 'No disponible';
                        badgeClass = 'badge-secondary';
                        clickHandler = ''; // Sin acción
                    }
                    
                    // Construir la tarjeta
                    html += `
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="turno-card ${estado}" ${clickHandler} ${!disponible && !esMio ? 'style="cursor: not-allowed; opacity: 0.6;"' : ''}>
                                <span class="turno-estado-badge badge ${badgeClass}">${estadoTexto}</span>
                                <div class="turno-numero text-dark">#${turno.NumeroTurno}</div>
                                <div class="turno-horario">
                                    <i class="zmdi zmdi-time"></i> ${turno.HoraInicio.substring(0,5)} - ${turno.HoraFin.substring(0,5)}
                                </div>
                                ${esMio ? `
                                    <div class="turno-cliente">
                                        <i class="zmdi zmdi-account-circle"></i> <strong>Mi turno</strong>
                                    </div>
                                ` : ''}
                                ${!disponible && !esMio ? `
                                    <div class="turno-cliente text-muted">
                                        <i class="zmdi zmdi-lock"></i> Ocupado
                                    </div>
                                ` : ''}
                            </div>
                        </div>`;
                });
                
                $('#listaTurnos').html(html);
            } else {
                $('#listaTurnos').html('<div class="col-12"><div class="alert alert-info">No hay información de turnos para este día</div></div>');
            }
        },
        error: function(xhr) {
            console.error('Error al cargar turnos:', xhr.responseText);
            $('#listaTurnos').html('<div class="col-12"><div class="alert alert-danger">Error al cargar turnos</div></div>');
        }
    });
}

// Función auxiliar para formatear estados
function formatearEstado(estado) {
    const estados = {
        'reservado': 'Reservado',
        'en_espera': 'En Espera',
        'atendiendo': 'Atendiendo',
        'finalizado': 'Finalizado',
        'cancelado': 'Cancelado',
        'disponible': 'Disponible',
        'ocupado': 'Ocupado'
    };
    
    return estados[estado] || estado.replace('_', ' ').toUpperCase();
}

// Función auxiliar para obtener clase de badge según estado
function getBadgeClass(estado) {
    const clases = {
        'disponible': 'badge-success',
        'reservado': 'badge-warning',
        'en_espera': 'badge-info',
        'atendiendo': 'badge-primary',
        'finalizado': 'badge-secondary',
        'cancelado': 'badge-danger',
        'ocupado': 'badge-secondary'
    };
    
    return clases[estado] || 'badge-secondary';
}

// Abrir modal para reservar turno
function abrirModalReserva(fecha, numeroTurno, horario, horaInicio, horaFin) {
    // Limpiar formulario
    $('#formReservarTurno')[0].reset();
    
    // Llenar datos del turno
    $('#fecha_turno').val(fecha);
    $('#numero_turno').val(numeroTurno);
    $('#hora_inicio').val(horaInicio);
    $('#hora_fin').val(horaFin);
    $('#horario_descripcion').val(horario);
    
    // Mostrar información en el modal
    const fechaObj = new Date(fecha + 'T00:00:00');
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    $('#info_fecha').text(fechaObj.toLocaleDateString('es-ES', opciones));
    $('#info_horario').text(`${horaInicio.substring(0,5)} - ${horaFin.substring(0,5)}`);
    $('#info_numero').text(numeroTurno);
    
    // Abrir modal
    $('#modalReservarTurno').modal('show');
}

// Reservar turno
function reservarTurno() {
    const formData = $('#formReservarTurno').serialize();
    
    $.ajax({
        url: '<?= base_url("turnos/reservar") ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            $('#modalReservarTurno').modal('hide');
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Turno Reservado!',
                    text: 'El turno ha sido reservado exitosamente',
                    timer: 2000
                });
                
                // Recargar turnos del día y calendario
                cargarTurnosDia(fechaSeleccionada);
                cargarInfoTurnos();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'No se pudo reservar el turno'
                });
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            $('#modalReservarTurno').modal('hide');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al reservar el turno'
            });
        }
    });
}

// Ver detalles de un turno
function verDetallesTurno(idTurno) {
    // Mostrar spinner mientras carga
    $('#contenidoDetalles').html('<div class="text-justify py-4"><div class="spinner-border" role="status"></div></div>');
    $('#modalDetallesTurno').modal('show');
	console.log("idTurno",idTurno);
	
    
    $.ajax({
        url: '<?= base_url("turnos/detalle/") ?>' + idTurno,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
			console.log(data);
            if (data.success && data.turno) {
                const t = data.turno;

                // Determinar clase del badge según estado
                let badgeClass = 'bg-warning'; // Bootstrap 5
                if (t.EstadoTurno === 'en_espera') badgeClass = 'bg-info';
                else if (t.EstadoTurno === 'atendiendo') badgeClass = 'bg-primary';
                else if (t.EstadoTurno === 'finalizado') badgeClass = 'bg-secondary';
                else if (t.EstadoTurno === 'cancelado') badgeClass = 'bg-danger';

                // Armar HTML
                const html = `
                    <div class="row">
                        <div class="col-md-6 text-dark">
                            <h5 class='text-dark'>Información del Turno</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%" class='text-dark'>Turno:</th>
                                    <td class='text-dark'>#${t.NumeroTurno}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Fecha:</th>
                                    <td class='text-dark'>${t.FechaTurno}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Horario:</th>
                                    <td class='text-dark'>${t.HorarioTurno}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Estado:</th>
                                    <td><span class="badge ${badgeClass}">${t.EstadoDescripcion}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-dark">
                            <h5 class='text-dark'>Información del Cliente</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%" class='text-dark'>Nombre:</th>
                                    <td class='text-dark'>${t.NombreCompleto}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Carnet:</th>
                                    <td class='text-dark'>${t.CarnetCliente}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Email:</th>
                                    <td class='text-dark'>${t.Email ? t.Email : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th class='text-dark'>Teléfono:</th>
                                    <td class='text-dark'>${t.Telefono ? t.Telefono : 'N/A'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;

                $('#contenidoDetalles').html(html);

                // Botones de acción
                $('#btnAtender, #btnFinalizar, #btnCancelar').hide();

                if (t.EstadoTurno === 'en_espera') {
                    $('#btnAtender').show().off('click').on('click', function() {
                        cambiarEstadoTurno(idTurno, 'atendiendo');
                    });
                    $('#btnCancelar').show().off('click').on('click', function() {
                        cambiarEstadoTurno(idTurno, 'cancelado');
                    });
                } else if (t.EstadoTurno === 'atendiendo') {
                    $('#btnFinalizar').show().off('click').on('click', function() {
                        cambiarEstadoTurno(idTurno, 'finalizado');
                    });
                } else if (t.EstadoTurno === 'reservado') {
                    $('#btnCancelar').show().off('click').on('click', function() {
                        cambiarEstadoTurno(idTurno, 'cancelado');
                    });
                }

            } else {
                $('#contenidoDetalles').html('<div class="alert alert-warning">Turno no encontrado</div>');
            }
        },
        error: function(xhr) {
            $('#contenidoDetalles').html('<div class="alert alert-danger">Error al cargar detalles</div>');
        }
    });
}


// Cambiar estado de un turno
function cambiarEstadoTurno(idTurno, nuevoEstado) {
    const mensajes = {
        'atendiendo': '¿Marcar turno como "Atendiendo"?',
        'finalizado': '¿Finalizar este turno?',
        'cancelado': '¿Cancelar este turno?'
    };
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: mensajes[nuevoEstado],
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("turnos/cambiar_estado") ?>',
                type: 'POST',
                data: {
                    id_turno: idTurno,
                    nuevo_estado: nuevoEstado
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Actualizado!', 'Estado cambiado exitosamente', 'success');
                        $('#modalDetallesTurno').modal('hide');
                        cargarTurnosDia(fechaSeleccionada);
                        cargarInfoTurnos();
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo cambiar el estado', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Ocurrió un error al cambiar el estado', 'error');
                }
            });
        }
    });
}

// Cargar clientes para el select
// Cargar clientes para el select
function cargarClientes() {
    console.log('Cargando clientes...');
    
    $.ajax({
        url: '<?= base_url("turnos/lista_clientes") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Respuesta clientes:', data);
            
            if (data.success && data.clientes) {
                let options = '<option value="">Seleccionar cliente...</option>';
                
                // Si no hay clientes, mostrar mensaje
                if (data.clientes.length === 0) {
                    options = '<option value="">No hay clientes disponibles</option>';
                } else {
                    // Agregar cada cliente al select
                    data.clientes.forEach(function(c) {
                        options += `<option value="${c.IdCliente}">${c.NombreCompleto} - ${c.CarnetCliente}</option>`;
                    });
                }
                
                $('#id_cliente').html(options);
                
                // Si solo hay un cliente (usuario normal), seleccionarlo automáticamente
                if (data.clientes.length === 1) {
                    $('#id_cliente').val(data.clientes[0].IdCliente);
                    //$('#id_cliente').prop('disabled', true); // Deshabilitar si solo hay uno
                    console.log('Cliente único seleccionado automáticamente:', data.clientes[0].NombreCompleto);
                }
                
                console.log('Clientes cargados:', data.clientes.length);
                console.log('Es admin:', data.es_admin);
            } else {
                $('#id_cliente').html('<option value="">Error al cargar clientes</option>');
                console.error('Error en respuesta:', data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX al cargar clientes:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            
            $('#id_cliente').html('<option value="">Error al cargar clientes</option>');
        }
    });
}
// Cambiar mes del calendario
function cambiarMes(direccion) {
    mesActual += direccion;
    
    if (mesActual > 11) {
        mesActual = 0;
        añoActual++;
    } else if (mesActual < 0) {
        mesActual = 11;
        añoActual--;
    }
    
    generarCalendario();
    $('#seccionTurnos').hide();
}

// Ir al día de hoy
function irHoy() {
    const hoy = new Date();
    mesActual = hoy.getMonth();
    añoActual = hoy.getFullYear();
    generarCalendario();
    
    // Seleccionar el día de hoy
    const fechaHoy = `${añoActual}-${String(mesActual + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
    setTimeout(() => {
        seleccionarDia(fechaHoy);
    }, 500);
}

// Abrir modal de nuevo cliente (integración con módulo de clientes)
function abrirModalNuevoCliente() {
    // Esta función debería abrir el modal de clientes
    // o redirigir al módulo de clientes
    Swal.fire({
        title: 'Agregar Cliente',
        text: 'Redirigiendo al módulo de clientes...',
        icon: 'info',
        timer: 1500
    }).then(() => {
        // Aquí podrías cargar el módulo de clientes o abrir su modal
        window.location.href = '<?= base_url("clientes") ?>';
    });
}
// ============================================
// FUNCIONES AUXILIARES (AGREGAR AQUÍ)
// ============================================

function formatearFecha(fecha) {
    const [año, mes, dia] = fecha.split('-');
    const fechaObj = new Date(año, mes - 1, dia);
    
    const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    const diaSemana = diasSemana[fechaObj.getDay()];
    const diaNumero = parseInt(dia, 10);
    const mesNombre = meses[parseInt(mes, 10) - 1];
    
    return `${diaSemana}, ${diaNumero} de ${mesNombre} de ${año}`;
}

function formatearFechaCorta(fecha) {
    const [año, mes, dia] = fecha.split('-');
    return `${dia}/${mes}/${año}`;
}

function esFechaPasada(fecha) {
    const fechaObj = new Date(fecha + 'T00:00:00');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    return fechaObj < hoy;
}

function formatearHora(hora) {
    if (!hora) return '';
    return hora.substring(0, 5);
}
</script>

<!--End Gestión de Turnos-->
