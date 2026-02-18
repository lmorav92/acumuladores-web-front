<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Usuarios</h4>
                <button class="btn btn-success btn-sm" onclick="newUser()">+ Nuevo Usuario</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="userSearch" class="form-control" placeholder="Buscar por nombre, usuario o carnet...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="tableUsers">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUsers">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUser" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formUser">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Usuario</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" id="id_usuario">
                    <input type="hidden" name="id_cliente" id="id_cliente">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Carnet</label>
                            <input type="text" name="carnet" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Nombre de Usuario</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Password</label>
                            <input type="password" name="password" id="passInput" class="form-control">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Rol</label>
                            <select name="rol" class="form-control">
                                <option value="Usuario">Usuario</option>
                                <option value="Administrador">Administrador</option>
                            </select>
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
// Lógica AJAX con SweetAlert2
function loadUsers() {
    console.log("Cargando lista de usuarios...");
    
    $.ajax({
        url: '<?= base_url("users/list") ?>',
        type: 'GET',
        dataType: 'json', // jQuery parseará el JSON automáticamente
        success: function(data) {
            let html = '';
            if(data.usuarios && data.usuarios.length > 0) {
                data.usuarios.forEach(u => {
                    html += `
                        <tr>
                            <td>${u.NombreCompleto}</td>
                            <td>${u.UserName}</td>
                            <td><span class="badge badge-info">${u.UserRol}</span></td>
                            <td><span class="badge badge-${u.UserEstado == 'Activo' ? 'success' : 'danger'}">${u.UserEstado}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editUser(${JSON.stringify(u)})'>
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.IdUsuario})">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">No hay usuarios registrados</td></tr>';
            }
            $('#tbodyUsers').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText); // 👈 AQUÍ VERÁS EL ERROR REAL
            $('#tbodyUsers').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function deleteUser(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará el usuario y sus datos de cliente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.get('<?= base_url("users/delete/") ?>' + id, function() {
                Swal.fire('Eliminado', 'Usuario borrado con éxito', 'success');
                loadUsers();
            });
        }
    });
}

function newUser() {
    $('#formUser')[0].reset();
    $('#id_usuario').val('');
    $('#modalTitle').text('Nuevo Usuario');
    $('#passInput').attr('required', true);
    $('#modalUser').modal('show');
}

function editUser(u) {
    $('#id_usuario').val(u.IdUsuario);
    $('#id_cliente').val(u.IdCliente);
    $('[name="nombre"]').val(u.NombreCliente);
    $('[name="apellidos"]').val(u.ApellidosCliente);
    $('[name="carnet"]').val(u.CarnetCliente);
    $('[name="email"]').val(u.Email);
    $('[name="username"]').val(u.UserName);
    $('[name="rol"]').val(u.UserRol);
    $('#passInput').attr('required', false);
    $('#modalTitle').text('Editar Usuario');
    $('#modalUser').modal('show');
}

// Inicializar
(function() {
    console.log("Iniciando módulo Usuarios...");
    
    // Ejecutamos la carga
    loadUsers();

    // Configuramos el buscador
    $('#userSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyUsers tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Configuramos el envío del formulario
    $('#formUser').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.post('<?= base_url("users/save") ?>', $(this).serialize(), function(res) {
            $('#modalUser').modal('hide');
            Swal.fire('Éxito', 'Registro insertado con éxito', 'success');
            loadUsers();
        });
    });
})();

</script>
