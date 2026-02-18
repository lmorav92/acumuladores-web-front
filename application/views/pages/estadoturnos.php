<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Estado de Turnos</h4>
                <button class="btn btn-success btn-sm" onclick="newEstadoTurno()">+ Nuevo Estado</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="estadoSearch" class="form-control" placeholder="Buscar por turno o descripción...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="tableEstados">
                        <thead>
                            <tr>
                                <th>ID Estado</th>
                                <th>ID Turno</th>
                                <th>Cliente</th>
                                <th>Descripción</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyEstados">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstadoTurno" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formEstadoTurno">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Estado de Turno</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_estado_turno" id="id_estado_turno">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Turno <span class="text-danger">*</span></label>
                            <select name="id_turno" id="id_turno" class="form-control" required>
                                <option value="">Seleccionar turno...</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Descripción del Estado <span class="text-danger">*</span></label>
                            <textarea name="descripcion" class="form-control" rows="3" required placeholder="Ejemplo: Turno atendido satisfactoriamente..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Lógica AJAX para Estados de Turno
function loadEstadosTurnos() {
    console.log("Cargando lista de estados de turnos...");
    
    $.ajax({
        url: '<?= base_url("estadoturnos/list") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if(data.estados && data.estados.length > 0) {
                data.estados.forEach(e => {
                    html += `
                        <tr>
                            <td>${e.IdEstadoTurno}</td>
                            <td>${e.IdTurno}</td>
                            <td>${e.NombreCompleto || 'N/A'}</td>
                            <td>${e.DescripcionEstadoTurno}</td>
                            <td>${e.CreatedDateEstadoTurno}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editEstadoTurno(${JSON.stringify(e)})'>
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteEstadoTurno(${e.IdEstadoTurno})">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No hay estados de turnos registrados</td></tr>';
            }
            $('#tbodyEstados').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyEstados').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function loadTurnosSelect() {
    $.ajax({
        url: '<?= base_url("estadoturnos/getTurnos") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">Seleccionar turno...</option>';
            if(data.turnos && data.turnos.length > 0) {
                data.turnos.forEach(t => {
                    options += `<option value="${t.IdTurno}">Turno #${t.IdTurno} - ${t.NombreCompleto} (${t.FechaTurno})</option>`;
                });
            }
            $('#id_turno').html(options);
        }
    });
}

function deleteEstadoTurno(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará el estado del turno",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("estadoturnos/delete/") ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Eliminado', 'Estado borrado con éxito', 'success');
                        loadEstadosTurnos();
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo eliminar', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Respuesta:', xhr.responseText);
                    Swal.fire('Error', 'Ocurrió un error al eliminar', 'error');
                }
            });
        }
    });
}

function newEstadoTurno() {
    $('#formEstadoTurno')[0].reset();
    $('#id_estado_turno').val('');
    $('#modalTitle').text('Nuevo Estado de Turno');
    loadTurnosSelect();
    $('#modalEstadoTurno').modal('show');
}

function editEstadoTurno(e) {
    $('#id_estado_turno').val(e.IdEstadoTurno);
    $('[name="descripcion"]').val(e.DescripcionEstadoTurno);
    loadTurnosSelect();
    setTimeout(() => {
        $('#id_turno').val(e.IdTurno);
    }, 500);
    $('#modalTitle').text('Editar Estado de Turno');
    $('#modalEstadoTurno').modal('show');
}

// Inicializar
(function() {
    console.log("Iniciando módulo Estados de Turnos...");
    
    loadEstadosTurnos();

    $('#estadoSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyEstados tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $('#formEstadoTurno').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("estadoturnos/save") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalEstadoTurno').modal('hide');
                if(res.success) {
                    Swal.fire('Éxito', 'Registro insertado con éxito', 'success');
                    loadEstadosTurnos();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                $('#modalEstadoTurno').modal('hide');
                Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
            }
        });
    });
})();
</script>
