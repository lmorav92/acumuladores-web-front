<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Clientes</h4>
                <button class="btn btn-success btn-sm" onclick="newCliente()">+ Nuevo Cliente</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="clienteSearch" class="form-control" placeholder="Buscar por nombre, carnet, email o teléfono...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="tableClientes">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Carnet</th>
                                <th>Email</th>
                                <th>Teléfono</th>
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
                        <div class="col-md-6 mb-2">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Carnet <span class="text-danger">*</span></label>
                            <input type="text" name="carnet" class="form-control" maxlength="11" required>
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
// Lógica AJAX para Clientes
function loadClientes() {
    console.log("Cargando lista de clientes...");
    
    $.ajax({
        url: '<?= base_url("clientes/list") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if(data.clientes && data.clientes.length > 0) {
                data.clientes.forEach(c => {
                    html += `
                        <tr>
                            <td>${c.NombreCompleto}</td>
                            <td>${c.CarnetCliente}</td>
                            <td>${c.Email || 'N/A'}</td>
                            <td>${c.Telefono || 'N/A'}</td>
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
            } else {
                html = '<tr><td colspan="5" class="text-center">No hay clientes registrados</td></tr>';
            }
            $('#tbodyClientes').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyClientes').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function deleteCliente(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará el cliente y todos sus datos relacionados",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("clientes/delete/") ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Eliminado', 'Cliente borrado con éxito', 'success');
                        loadClientes();
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

function newCliente() {
    $('#formCliente')[0].reset();
    $('#id_cliente').val('');
    $('#modalTitle').text('Nuevo Cliente');
    $('#modalCliente').modal('show');
}

function editCliente(c) {
    $('#id_cliente').val(c.IdCliente);
    $('[name="nombre"]').val(c.NombreCliente);
    $('[name="apellidos"]').val(c.ApellidosCliente);
    $('[name="carnet"]').val(c.CarnetCliente);
    $('[name="email"]').val(c.Email);
    $('[name="telefono"]').val(c.Telefono);
    $('[name="direccion"]').val(c.DireccionCliente);
    $('[name="avatar"]').val(c.Avatar);
    $('#modalTitle').text('Editar Cliente');
    $('#modalCliente').modal('show');
}

// Inicializar
(function() {
    console.log("Iniciando módulo Clientes...");
    
    loadClientes();

    $('#clienteSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyClientes tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
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
                if(res.success) {
                    Swal.fire('Éxito', 'Registro insertado con éxito', 'success');
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
