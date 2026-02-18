<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Preferencias de Usuarios</h4>
                <button class="btn btn-success btn-sm" onclick="newPreferencia()">+ Nueva Preferencia</button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="preferenciaSearch" class="form-control" placeholder="Buscar por usuario...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="tablePreferencias">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Tema</th>
                                <th>Idioma</th>
                                <th>Notif. Email</th>
                                <th>Notif. Push</th>
                                <th>Última Act.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPreferencias">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPreferencia" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formPreferencia">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Preferencias de Usuario</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_preferencia" id="id_preferencia">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label>Usuario <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-control" required>
                                <option value="">Seleccionar usuario...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label>Tema de Interfaz <span class="text-danger">*</span></label>
                            
                            <p class="mb-2 mt-2"><strong>Gaussion Texture</strong></p>
                            <div class="d-flex flex-wrap mb-3">
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme1" value="theme1">
                                    <label class="form-check-label" for="theme1">
                                        <span class="theme-preview" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 1
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme2" value="theme2">
                                    <label class="form-check-label" for="theme2">
                                        <span class="theme-preview" style="background: linear-gradient(to right, #f83600 0%, #f9d423 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 2
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme3" value="theme3">
                                    <label class="form-check-label" for="theme3">
                                        <span class="theme-preview" style="background: linear-gradient(to top, #4481eb 0%, #04befe 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 3
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme4" value="theme4">
                                    <label class="form-check-label" for="theme4">
                                        <span class="theme-preview" style="background: linear-gradient(to top, #0ba360 0%, #3cba92 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 4
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme5" value="theme5">
                                    <label class="form-check-label" for="theme5">
                                        <span class="theme-preview" style="background: linear-gradient(to right, #ed213a 0%, #93291e 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 5
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme6" value="theme6">
                                    <label class="form-check-label" for="theme6">
                                        <span class="theme-preview" style="background: linear-gradient(to right, #b224ef 0%, #7579ff 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 6
                                    </label>
                                </div>
                            </div>

                            <p class="mb-2"><strong>Gradient Background</strong></p>
                            <div class="d-flex flex-wrap mb-3">
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme7" value="theme7">
                                    <label class="form-check-label" for="theme7">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 7
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme8" value="theme8">
                                    <label class="form-check-label" for="theme8">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 8
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme9" value="theme9">
                                    <label class="form-check-label" for="theme9">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 9
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme10" value="theme10">
                                    <label class="form-check-label" for="theme10">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 10
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme11" value="theme11">
                                    <label class="form-check-label" for="theme11">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 11
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme12" value="theme12">
                                    <label class="form-check-label" for="theme12">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 12
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme13" value="theme13">
                                    <label class="form-check-label" for="theme13">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 13
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme14" value="theme14">
                                    <label class="form-check-label" for="theme14">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 14
                                    </label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme15" value="theme15">
                                    <label class="form-check-label" for="theme15">
                                        <span class="theme-preview" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle;"></span>
                                        Tema 15
                                    </label>
                                </div>
                            </div>

                            <p class="mb-2"><strong>Dark Theme</strong></p>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="tema" id="theme16" value="theme16">
                                    <label class="form-check-label" for="theme16">
                                        <span class="theme-preview" style="background: #1A1A1A; width: 30px; height: 30px; display: inline-block; border-radius: 4px; vertical-align: middle; border: 1px solid #333;"></span>
                                        Tema 16 (Negro)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Idioma Preferido <span class="text-danger">*</span></label>
                            <select name="idioma" class="form-control" required>
                                <option value="es">Español</option>
                                <option value="en">Inglés</option>
                                <option value="fr">Francés</option>
                                <option value="pt">Portugués</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Notificaciones Email</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="notif_email" id="notif_email" value="1" checked>
                                <label class="form-check-label" for="notif_email">
                                    Activar notificaciones por correo electrónico
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Notificaciones Push</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="notif_push" id="notif_push" value="1" checked>
                                <label class="form-check-label" for="notif_push">
                                    Activar notificaciones push
                                </label>
                            </div>
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
// Usar la baseUrl global del sistema SPA
var baseUrlPreferencias = typeof baseUrl !== 'undefined' ? baseUrl : '';

// Lógica AJAX para Preferencias de Usuarios
function loadPreferencias() {
    console.log("Cargando lista de preferencias...");
    
    $.ajax({
        url: baseUrlPreferencias + 'preferencias/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if(data.preferencias && data.preferencias.length > 0) {
                data.preferencias.forEach(p => {
                    html += `
                        <tr>
                            <td>${p.NombreCompleto}</td>
                            <td><span class="badge badge-info">${p.TemaInterfaz || 'theme1'}</span></td>
                            <td>${p.IdiomaPreferido.toUpperCase()}</td>
                            <td>${p.NotificacionesEmail == 1 ? '<i class="zmdi zmdi-check text-success"></i>' : '<i class="zmdi zmdi-close text-danger"></i>'}</td>
                            <td>${p.NotificacionesPush == 1 ? '<i class="zmdi zmdi-check text-success"></i>' : '<i class="zmdi zmdi-close text-danger"></i>'}</td>
                            <td>${p.UpdatedDate || p.CreatedDate}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editPreferencia(${JSON.stringify(p)})'>
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deletePreferencia(${p.IdPreferencia})">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center">No hay preferencias registradas</td></tr>';
            }
            $('#tbodyPreferencias').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyPreferencias').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function loadUsuariosSelect() {
    $.ajax({
        url: baseUrlPreferencias + 'preferencias/getUsuarios',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let options = '<option value="">Seleccionar usuario...</option>';
            if(data.usuarios && data.usuarios.length > 0) {
                data.usuarios.forEach(u => {
                    options += `<option value="${u.IdUsuario}">${u.NombreCompleto} (${u.UserName})</option>`;
                });
            }
            $('#id_usuario').html(options);
        }
    });
}

function deletePreferencia(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminarán las preferencias del usuario",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrlPreferencias + 'preferencias/delete/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Eliminado', 'Preferencia borrada con éxito', 'success');
                        loadPreferencias();
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

function newPreferencia() {
    $('#formPreferencia')[0].reset();
    $('#id_preferencia').val('');
    $('#notif_email').prop('checked', true);
    $('#notif_push').prop('checked', true);
    $('#theme1').prop('checked', true); // Tema por defecto
    $('#modalTitle').text('Nueva Preferencia de Usuario');
    loadUsuariosSelect();
    $('#modalPreferencia').modal('show');
}

function editPreferencia(p) {
    $('#id_preferencia').val(p.IdPreferencia);
    $('[name="idioma"]').val(p.IdiomaPreferido);
    $('#notif_email').prop('checked', p.NotificacionesEmail == 1);
    $('#notif_push').prop('checked', p.NotificacionesPush == 1);
    
    // Seleccionar el tema correcto
    const tema = p.TemaInterfaz || 'theme1';
    $(`input[name="tema"][value="${tema}"]`).prop('checked', true);
    
    loadUsuariosSelect();
    setTimeout(() => {
        $('#id_usuario').val(p.IdUsuario);
    }, 500);
    $('#modalTitle').text('Editar Preferencias de Usuario');
    $('#modalPreferencia').modal('show');
}

// Inicializar cuando se carga la página
(function() {
    console.log("Iniciando módulo Preferencias de Usuarios...");
    console.log("baseUrl:", baseUrlPreferencias);
    
    loadPreferencias();

    $('#preferenciaSearch').off('keyup').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#tbodyPreferencias tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $('#formPreferencia').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: baseUrlPreferencias + 'preferencias/save',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalPreferencia').modal('hide');
                if(res.success) {
                    Swal.fire('Éxito', 'Registro guardado con éxito', 'success');
                    loadPreferencias();
                    
                    // Si se guardó el tema, recargar el tema en el color_switcher
                    if (typeof cargarTemaUsuario === 'function') {
                        cargarTemaUsuario();
                    }
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                $('#modalPreferencia').modal('hide');
                Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
            }
        });
    });
})();
</script>
