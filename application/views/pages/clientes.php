<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Clientes</h4>
                <button class="btn btn-success btn-sm" onclick="newCliente()">+ Nuevo Cliente</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="clienteSearch" class="form-control" placeholder="Buscar por nombre, RUC/DNI, email o teléfono...">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="tableClientes">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre Completo / Razón Social</th>
                                <th>RUC / DNI</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClientes">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formCliente">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Cliente</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_cliente" id="id_cliente">

                    <div class="row">
                        <!-- Tipo de Cliente -->
                        <div class="col-md-6 mb-2">
                            <label>Tipo de Cliente <span class="text-danger">*</span></label>
                            <select name="tipo_cliente" id="tipo_cliente" class="form-control" onchange="toggleTipoCliente(this.value)">
                                <option value="PERSONA">Persona</option>
                                <option value="EMPRESA">Empresa</option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-2">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="ACTIVO">Activo</option>
                                <option value="INACTIVO">Inactivo</option>
                            </select>
                        </div>

                        <!-- Campos PERSONA -->
                        <div id="campos_persona">
                            <div class="row w-100 mx-0">
                                <div class="col-md-6 mb-2">
                                    <label>Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Campos EMPRESA -->
                        <div id="campos_empresa" style="display:none;">
                            <div class="row w-100 mx-0">
                                <div class="col-md-12 mb-2">
                                    <label>Razón Social <span class="text-danger">*</span></label>
                                    <input type="text" name="razon_social" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Campos comunes -->
                        <div class="col-md-6 mb-2">
                            <label>RUC / DNI <span class="text-danger">*</span></label>
                            <input type="text" name="ruc_dni" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Avatar (URL)</label>
                            <input type="text" name="avatar" class="form-control" placeholder="http://...">
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
function toggleTipoCliente(tipo) {
    if (tipo === 'EMPRESA') {
        $('#campos_empresa').show();
        $('#campos_persona').hide();
        $('[name="nombre"]').removeAttr('required');
        $('[name="apellidos"]').removeAttr('required');
        $('[name="razon_social"]').attr('required', true);
    } else {
        $('#campos_persona').show();
        $('#campos_empresa').hide();
        $('[name="nombre"]').attr('required', true);
        $('[name="apellidos"]').attr('required', true);
        $('[name="razon_social"]').removeAttr('required');
    }
}

function loadClientes() {
    console.log("Cargando lista de clientes...");

    $.ajax({
        url: '<?= base_url("clientes/list") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if (data.success && data.clientes && data.clientes.length > 0) {
                data.clientes.forEach(c => {
                    const nombreMostrar = c.TipoCliente === 'EMPRESA'
                        ? (c.RazonSocial || c.NombreCompleto)
                        : c.NombreCompleto;

                    const badgeTipo = c.TipoCliente === 'EMPRESA'
                        ? '<span class="badge badge-info">Empresa</span>'
                        : '<span class="badge badge-secondary">Persona</span>';

                    const badgeEstado = c.EstadoCliente === 'ACTIVO'
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';

                    html += `
                        <tr>
                            <td>${badgeTipo}</td>
                            <td>${nombreMostrar}</td>
                            <td>${c.RUC_DNI || 'N/A'}</td>
                            <td>${c.Email || 'N/A'}</td>
                            <td>${c.Telefono || 'N/A'}</td>
                            <td>${badgeEstado}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editCliente(${JSON.stringify(c)})'>
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCliente(${c.IdCliente})">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else if (!data.success) {
                console.error('Error del servidor:', data.message, data.error_detail || '');
                html = `<tr><td colspan="7" class="text-center text-danger">Error: ${data.message}</td></tr>`;
            } else {
                html = '<tr><td colspan="7" class="text-center">No hay clientes registrados</td></tr>';
            }
            $('#tbodyClientes').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyClientes').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function deleteCliente(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará el cliente permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("clientes/delete/") ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Eliminado', 'Cliente borrado con éxito', 'success');
                        loadClientes();
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo eliminar', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al eliminar', 'error');
                }
            });
        }
    });
}

function newCliente() {
    $('#formCliente')[0].reset();
    $('#id_cliente').val('');
    $('#modalTitle').text('Nuevo Cliente');
    toggleTipoCliente('PERSONA');
    $('#modalCliente').modal('show');
}

function editCliente(c) {
    $('#id_cliente').val(c.IdCliente);
    $('[name="tipo_cliente"]').val(c.TipoCliente);
    toggleTipoCliente(c.TipoCliente);
    $('[name="nombre"]').val(c.NombreCliente);
    $('[name="apellidos"]').val(c.ApellidosCliente);
    $('[name="razon_social"]').val(c.RazonSocial);
    $('[name="ruc_dni"]').val(c.RUC_DNI);
    $('[name="email"]').val(c.Email);
    $('[name="telefono"]').val(c.Telefono);
    $('[name="direccion"]').val(c.DireccionCliente);
    $('[name="fecha_nacimiento"]').val(c.FechaNacimiento);
    $('[name="avatar"]').val(c.Avatar);
    $('[name="estado"]').val(c.EstadoCliente);
    $('#modalTitle').text('Editar Cliente');
    $('#modalCliente').modal('show');
}

// Inicializar módulo
(function() {
    console.log("Iniciando módulo Clientes...");

    loadClientes();

    $('#clienteSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyClientes tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('#formCliente').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("clientes/save") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalCliente').modal('hide');
                if (res.success) {
                    Swal.fire('Éxito', 'Registro guardado con éxito', 'success');
                    loadClientes();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                $('#modalCliente').modal('hide');
                Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
            }
        });
    });
})();
</script>
