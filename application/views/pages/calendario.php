<!--Start Gestión de Turnos-->
<div class="container-fluid">
    
    <!-- Header -->
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-white mb-0">Turnos</h2>
            <p class="text-white-50">Calendario de turnos</p>
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

<!-- Modal para Reservar Turno -->
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

<!-- Modal para Ver Detalles del Turno -->
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

<style>
/* Estilos del Calendario */
.calendario-container {
    background: #fff;
    padding: 20px;
}

.calendario-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
}

.dia-nombre {
    text-align: center;
    font-weight: 600;
    color: #555;
    padding: 10px;
    font-size: 14px;
}

.calendario-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    min-height: 400px;
}

.dia-celda {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px;
    min-height: 100px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    position: relative;
}

.dia-celda:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    transform: translateY(-2px);
}

.dia-celda.otro-mes {
    background: #f8f9fa;
    color: #ccc;
}

.dia-celda.hoy {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
}

.dia-celda.seleccionado {
    border: 2px solid #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
	
}

.dia-numero {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 5px;
	color: #ccc;
}

.dia-celda.hoy .dia-numero {
    color: white;
}

.dia-info {
    font-size: 11px;
    margin-top: 8px;
	color: #ccc;
}

.badge-turno {
    font-size: 10px;
    padding: 2px 6px;
    margin: 2px;
    display: inline-block;
}

/* Estilos de las Cards de Turnos */
.turno-card {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.turno-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 5px;
    height: 100%;
    background: #28a745;
}

.turno-card.disponible {
    border-color: #28a745;
    background: linear-gradient(to right, rgba(40, 167, 69, 0.05), transparent);
}

.turno-card.disponible::before {
    background: #28a745;
}

.turno-card.reservado {
    border-color: #ffc107;
    background: linear-gradient(to right, rgba(255, 193, 7, 0.05), transparent);
}

.turno-card.reservado::before {
    background: #ffc107;
}

.turno-card.en_espera {
    border-color: #17a2b8;
    background: linear-gradient(to right, rgba(23, 162, 184, 0.05), transparent);
}

.turno-card.en_espera::before {
    background: #17a2b8;
}

.turno-card.atendiendo {
    border-color: #007bff;
    background: linear-gradient(to right, rgba(0, 123, 255, 0.05), transparent);
}

.turno-card.atendiendo::before {
    background: #007bff;
}

.turno-card.finalizado {
    border-color: #6c757d;
    background: linear-gradient(to right, rgba(108, 117, 125, 0.05), transparent);
    opacity: 0.7;
}

.turno-card.finalizado::before {
    background: #6c757d;
}

.turno-card.cancelado {
    border-color: #dc3545;
    background: linear-gradient(to right, rgba(220, 53, 69, 0.05), transparent);
    opacity: 0.7;
}

.turno-card.cancelado::before {
    background: #dc3545;
}

.turno-card:hover:not(.finalizado):not(.cancelado) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.turno-numero {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.turno-horario {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.turno-cliente {
    font-size: 13px;
    color: #333;
    margin-top: 10px;
    font-weight: 500;
}

.turno-estado-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .calendario-dias {
        gap: 5px;
    }
    
    .dia-celda {
        min-height: 60px;
        padding: 5px;
    }
    
    .dia-numero {
        font-size: 14px;
    }
    
    .dia-info {
        font-size: 9px;
    }
}
</style>

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
    generarCalendario();
    
    // Handler del formulario de reserva
    $('#formReservarTurno').on('submit', function(e) {
        e.preventDefault();
        reservarTurno();
    });
});

// Generar calendario del mes
function generarCalendario() {
    const primerDia = new Date(añoActual, mesActual, 1);
    const ultimoDia = new Date(añoActual, mesActual + 1, 0);
    const diasMes = ultimoDia.getDate();
    const diaSemanaInicio = primerDia.getDay();
    
    // Actualizar título
    $('#mesActualTitulo').text(`${nombresMeses[mesActual]} ${añoActual}`);
    
    let html = '';
    let diaActual = 1;
    let totalCeldas = Math.ceil((diasMes + diaSemanaInicio) / 7) * 7;
    
    // Días del mes anterior
    const ultimoDiaMesAnterior = new Date(añoActual, mesActual, 0).getDate();
    
    for (let i = 0; i < totalCeldas; i++) {
        let dia, mes, año, claseExtra = '';
        
        if (i < diaSemanaInicio) {
            // Días del mes anterior
            dia = ultimoDiaMesAnterior - (diaSemanaInicio - i - 1);
            mes = mesActual === 0 ? 11 : mesActual - 1;
            año = mesActual === 0 ? añoActual - 1 : añoActual;
            claseExtra = 'otro-mes';
        } else if (diaActual <= diasMes) {
            // Días del mes actual
            dia = diaActual;
            mes = mesActual;
            año = añoActual;
            diaActual++;
            
            // Marcar el día de hoy
            const hoy = new Date();
            if (dia === hoy.getDate() && mes === hoy.getMonth() && año === hoy.getFullYear()) {
                claseExtra = 'hoy';
            }
        } else {
            // Días del mes siguiente
            dia = diaActual - diasMes;
            mes = mesActual === 11 ? 0 : mesActual + 1;
            año = mesActual === 11 ? añoActual + 1 : añoActual;
            claseExtra = 'otro-mes';
            diaActual++;
        }
        
        const fechaCompleta = `${año}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        
        html += `
            <div class="dia-celda ${claseExtra}" data-fecha="${fechaCompleta}" onclick="seleccionarDia('${fechaCompleta}')">
                <div class="dia-numero">${dia}</div>
                <div class="dia-info" id="info-${fechaCompleta}">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                </div>
            </div>`;
    }
    
    $('#calendarioDias').html(html);
    
    // Cargar información de turnos para cada día visible
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

// Seleccionar un día
function seleccionarDia(fecha) {
    fechaSeleccionada = fecha;
    
    // Marcar como seleccionado
    $('.dia-celda').removeClass('seleccionado');
    $(`.dia-celda[data-fecha="${fecha}"]`).addClass('seleccionado');
    
    // Mostrar sección de turnos
    $('#seccionTurnos').slideDown();
    
    // Formatear fecha para mostrar
    const partes = fecha.split('-');
    const fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);
    $('#fechaSeleccionada').text(fechaFormateada);
    
    // Cargar turnos del día
    cargarTurnosDia(fecha);
}

// Cargar turnos de un día específico
function cargarTurnosDia(fecha) {
    $('#listaTurnos').html('<div class="col-12 text-center"><div class="spinner-border" role="status"></div></div>');
    
    $.ajax({
        url: '<?= base_url("turnos/turnos_dia") ?>',
        type: 'GET',
        data: { fecha: fecha },
        dataType: 'json',
        success: function(data) {
            if (data.turnos && data.turnos.length > 0) {
                let html = '';
                
                data.turnos.forEach(function(turno) {
                    const disponible = turno.Disponible == 1;
                    const estado = disponible ? 'disponible' : turno.Estado;
                    const estadoTexto = disponible ? 'Disponible' : turno.Estado.replace('_', ' ').toUpperCase();
                    
                    let badgeClass = 'badge-success';
                    if (estado === 'reservado') badgeClass = 'badge-warning';
                    else if (estado === 'en_espera') badgeClass = 'badge-info';
                    else if (estado === 'atendiendo') badgeClass = 'badge-primary';
                    else if (estado === 'finalizado') badgeClass = 'badge-secondary';
                    else if (estado === 'cancelado') badgeClass = 'badge-danger';
                    
                    const clickHandler = disponible ? 
                        `onclick="abrirModalReserva('${fecha}', ${turno.NumeroTurno}, '${turno.HorarioDescripcion}', '${turno.HoraInicio}', '${turno.HoraFin}')"` :
                        `onclick="verDetallesTurno(${turno.IdTurno})"`;
                    
                    html += `
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="turno-card ${estado}" ${clickHandler}>
                                <span class="turno-estado-badge badge ${badgeClass}">${estadoTexto}</span>
                                <div class="turno-numero text-dark">#${turno.NumeroTurno}</div>
                                <div class="turno-horario">
                                    <i class="zmdi zmdi-time"></i> ${turno.HoraInicio.substring(0,5)} - ${turno.HoraFin.substring(0,5)}
                                </div>
                                ${!disponible ? `<div class="turno-cliente"><i class="zmdi zmdi-account"></i> ${turno.Cliente}</div>` : ''}
                            </div>
                        </div>`;
                });
                
                $('#listaTurnos').html(html);
            } else {
                $('#listaTurnos').html('<div class="col-12"><div class="alert alert-info">No hay información de turnos para este día</div></div>');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            $('#listaTurnos').html('<div class="col-12"><div class="alert alert-danger">Error al cargar turnos</div></div>');
        }
    });
}

// Abrir modal para reservar turno
// function abrirModalReserva(fecha, numeroTurno, horario, horaInicio, horaFin) {
//     // Limpiar formulario
//     $('#formReservarTurno')[0].reset();
    
//     // Llenar datos del turno
//     $('#fecha_turno').val(fecha);
//     $('#numero_turno').val(numeroTurno);
//     $('#hora_inicio').val(horaInicio);
//     $('#hora_fin').val(horaFin);
//     $('#horario_descripcion').val(horario);
    
//     // Mostrar información en el modal
//     const fechaObj = new Date(fecha + 'T00:00:00');
//     const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
//     $('#info_fecha').text(fechaObj.toLocaleDateString('es-ES', opciones));
//     $('#info_horario').text(`${horaInicio.substring(0,5)} - ${horaFin.substring(0,5)}`);
//     $('#info_numero').text(numeroTurno);
    
//     // Abrir modal
//     $('#modalReservarTurno').modal('show');
// }

// Reservar turno
// function reservarTurno() {
//     const formData = $('#formReservarTurno').serialize();
    
//     $.ajax({
//         url: '<?= base_url("turnos/reservar") ?>',
//         type: 'POST',
//         data: formData,
//         dataType: 'json',
//         success: function(res) {
//             $('#modalReservarTurno').modal('hide');
//             if (res.success) {
//                 Swal.fire({
//                     icon: 'success',
//                     title: '¡Turno Reservado!',
//                     text: 'El turno ha sido reservado exitosamente',
//                     timer: 2000
//                 });
                
//                 // Recargar turnos del día y calendario
//                 cargarTurnosDia(fechaSeleccionada);
//                 cargarInfoTurnos();
//             } else {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error',
//                     text: res.message || 'No se pudo reservar el turno'
//                 });
//             }
//         },
//         error: function(xhr) {
//             console.error('Error:', xhr.responseText);
//             $('#modalReservarTurno').modal('hide');
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error',
//                 text: 'Ocurrió un error al reservar el turno'
//             });
//         }
//     });
// }

// Ver detalles de un turno
// function verDetallesTurno(idTurno) {
//     // Mostrar spinner mientras carga
//     $('#contenidoDetalles').html('<div class="text-justify py-4"><div class="spinner-border" role="status"></div></div>');
//     $('#modalDetallesTurno').modal('show');
    
//     $.ajax({
//         url: '<?= base_url("turnos/detalle/") ?>' + idTurno,
//         type: 'GET',
//         dataType: 'json',
//         success: function(data) {
// 			console.log(data);
//             if (data.success && data.turno) {
//                 const t = data.turno;

//                 // Determinar clase del badge según estado
//                 let badgeClass = 'bg-warning'; // Bootstrap 5
//                 if (t.EstadoTurno === 'en_espera') badgeClass = 'bg-info';
//                 else if (t.EstadoTurno === 'atendiendo') badgeClass = 'bg-primary';
//                 else if (t.EstadoTurno === 'finalizado') badgeClass = 'bg-secondary';
//                 else if (t.EstadoTurno === 'cancelado') badgeClass = 'bg-danger';

//                 // Armar HTML
//                 const html = `
//                     <div class="row">
//                         <div class="col-md-6 text-dark">
//                             <h5 class='text-dark'>Información del Turno</h5>
//                             <table class="table table-sm">
//                                 <tr>
//                                     <th width="40%" class='text-dark'>Turno:</th>
//                                     <td class='text-dark'>#${t.NumeroTurno}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Fecha:</th>
//                                     <td class='text-dark'>${t.FechaTurno}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Horario:</th>
//                                     <td class='text-dark'>${t.HorarioTurno}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Estado:</th>
//                                     <td><span class="badge ${badgeClass}">${t.EstadoDescripcion}</span></td>
//                                 </tr>
//                             </table>
//                         </div>
//                         <div class="col-md-6 text-dark">
//                             <h5 class='text-dark'>Información del Cliente</h5>
//                             <table class="table table-sm">
//                                 <tr>
//                                     <th width="40%" class='text-dark'>Nombre:</th>
//                                     <td class='text-dark'>${t.NombreCompleto}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Carnet:</th>
//                                     <td class='text-dark'>${t.CarnetCliente}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Email:</th>
//                                     <td class='text-dark'>${t.Email ? t.Email : 'N/A'}</td>
//                                 </tr>
//                                 <tr>
//                                     <th class='text-dark'>Teléfono:</th>
//                                     <td class='text-dark'>${t.Telefono ? t.Telefono : 'N/A'}</td>
//                                 </tr>
//                             </table>
//                         </div>
//                     </div>
//                 `;

//                 $('#contenidoDetalles').html(html);

//                 // Botones de acción
//                 $('#btnAtender, #btnFinalizar, #btnCancelar').hide();

//                 if (t.EstadoTurno === 'en_espera') {
//                     $('#btnAtender').show().off('click').on('click', function() {
//                         cambiarEstadoTurno(idTurno, 'atendiendo');
//                     });
//                     $('#btnCancelar').show().off('click').on('click', function() {
//                         cambiarEstadoTurno(idTurno, 'cancelado');
//                     });
//                 } else if (t.EstadoTurno === 'atendiendo') {
//                     $('#btnFinalizar').show().off('click').on('click', function() {
//                         cambiarEstadoTurno(idTurno, 'finalizado');
//                     });
//                 } else if (t.EstadoTurno === 'reservado') {
//                     $('#btnCancelar').show().off('click').on('click', function() {
//                         cambiarEstadoTurno(idTurno, 'cancelado');
//                     });
//                 }

//             } else {
//                 $('#contenidoDetalles').html('<div class="alert alert-warning">Turno no encontrado</div>');
//             }
//         },
//         error: function(xhr) {
//             $('#contenidoDetalles').html('<div class="alert alert-danger">Error al cargar detalles</div>');
//         }
//     });
// }


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
function cargarClientes() {
    $.ajax({
        url: '<?= base_url("turnos/lista_clientes") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.clientes) {
                let options = '<option value="">Seleccionar cliente...</option>';
                data.clientes.forEach(function(c) {
                    options += `<option value="${c.IdCliente}">${c.NombreCompleto} - ${c.CarnetCliente}</option>`;
                });
                $('#id_cliente').html(options);
            }
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

</script>

<!--End Gestión de Turnos-->
