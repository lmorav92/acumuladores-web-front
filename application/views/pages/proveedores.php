<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Proveedores</h4>
                <button class="btn btn-success btn-sm" onclick="newProveedor()">+ Nuevo Proveedor</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="proveedorSearch" class="form-control" placeholder="Buscar por nombre, RUC, contacto, email o teléfono...">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="tableProveedores">
                        <thead>
                            <tr>
                                <th>Nombre Proveedor</th>
                                <th>RUC</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProveedores">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Proveedor -->
<div class="modal fade" id="modalProveedor" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formProveedor">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleProveedor">Proveedor</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_proveedor" id="id_proveedor">

                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <label>Nombre del Proveedor <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required maxlength="150">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>RUC</label>
                            <input type="text" name="ruc" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Contacto</label>
                            <input type="text" name="contacto" class="form-control" maxlength="100" placeholder="Nombre del representante">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="ACTIVO">Activo</option>
                                <option value="INACTIVO">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" maxlength="250">
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
function loadProveedores() {
    console.log("Cargando lista de proveedores...");

    $.ajax({
        url: '<?= base_url("proveedores/list") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if (data.success && data.proveedores && data.proveedores.length > 0) {
                data.proveedores.forEach(p => {
                    const badgeEstado = p.EstadoProveedor === 'ACTIVO'
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';

                    html += `
                        <tr>
                            <td>${p.NombreProveedor}</td>
                            <td>${p.RUC || 'N/A'}</td>
                            <td>${p.Contacto || 'N/A'}</td>
                            <td>${p.Email || 'N/A'}</td>
                            <td>${p.Telefono || 'N/A'}</td>
                            <td>${badgeEstado}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editProveedor(${JSON.stringify(p)})'>
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProveedor(${p.IdProveedor})">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else if (!data.success) {
                console.error('Error del servidor:', data.message, data.error_detail || '');
                html = `<tr><td colspan="7" class="text-center text-danger">Error: ${data.message}</td></tr>`;
            } else {
                html = '<tr><td colspan="7" class="text-center">No hay proveedores registrados</td></tr>';
            }
            $('#tbodyProveedores').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyProveedores').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function deleteProveedor(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará el proveedor permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("proveedores/delete/") ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Eliminado', 'Proveedor borrado con éxito', 'success');
                        loadProveedores();
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

function newProveedor() {
    $('#formProveedor')[0].reset();
    $('#id_proveedor').val('');
    $('#modalTitleProveedor').text('Nuevo Proveedor');
    $('#modalProveedor').modal('show');
}

function editProveedor(p) {
    $('#id_proveedor').val(p.IdProveedor);
    $('[name="nombre"]').val(p.NombreProveedor);
    $('[name="ruc"]').val(p.RUC);
    $('[name="contacto"]').val(p.Contacto);
    $('[name="telefono"]').val(p.Telefono);
    $('[name="email"]').val(p.Email);
    $('[name="direccion"]').val(p.Direccion);
    $('[name="estado"]').val(p.EstadoProveedor);
    $('#modalTitleProveedor').text('Editar Proveedor');
    $('#modalProveedor').modal('show');
}

// Inicializar módulo
(function() {
    console.log("Iniciando módulo Proveedores...");

    loadProveedores();

    $('#proveedorSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyProveedores tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('#formProveedor').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("proveedores/save") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalProveedor').modal('hide');
                if (res.success) {
                    Swal.fire('Éxito', 'Registro guardado con éxito', 'success');
                    loadProveedores();
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                $('#modalProveedor').modal('hide');
                Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
            }
        });
    });
})();
</script>
