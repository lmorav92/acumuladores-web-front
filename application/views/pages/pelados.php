<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Pelados</h4>
                <button class="btn btn-success btn-sm" onclick="newServicio()">+ Nuevo Pelado</button>
            </div>

            <div class="card-body">
                <input type="text" id="servicioSearch" class="form-control mb-3" placeholder="Buscar pelado...">

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyServicios"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalServicio">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formServicio" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Servicio</h5>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdPelado" id="IdPelado">
                    <input type="hidden" name="UrlFotoActual" id="UrlFotoActual">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Nombre del Servicio *</label>
                            <input type="text" name="NombrePelado" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Categoría *</label>
                            <select name="IdCategoriaPelado" id="IdCategoriaPelado" class="form-control" required></select>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Descripción</label>
                            <textarea name="DescripcionPelado" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Precio *</label>
                            <input type="number" step="0.01" name="PrecioPelado" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Foto del Servicio</label>
                            <input type="file" name="foto_servicio" id="foto_servicio" class="form-control" accept="image/*">
                            <small class="text-muted">JPG, PNG, GIF. Máx 2MB</small>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div id="preview_foto" style="display:none; text-align:center;">
                                <img id="img_preview" src="" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver imagen -->
<div class="modal fade" id="modalVerImagen" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagenModal" src="" style="max-width: 400px; max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
var baseUrlServicios = typeof baseUrl !== 'undefined' ? baseUrl : '';

// Preview de imagen al seleccionar
$('#foto_servicio').on('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#img_preview').attr('src', e.target.result);
            $('#preview_foto').show();
        }
        reader.readAsDataURL(file);
    } else {
        $('#preview_foto').hide();
    }
});

function verImagen(imgSrc) {
    $('#imagenModal').attr('src', imgSrc);
    $('#modalVerImagen').modal('show');
}

function loadServicios() {
    console.log("Cargando lista de servicios...");
    
    $.ajax({
        url: baseUrlServicios + 'pelado/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            let html = '';
            if(data.servicios && data.servicios.length > 0) {
                data.servicios.forEach(s => {
                    const imgSrc = s.UrlFoto ? 
                        baseUrlServicios + 'ui/assets/img/servicios/' + s.UrlFoto : 
                        baseUrlServicios + 'ui/assets/img/no-image.png';
                    
                    html += `
                <tr>
                    <td><img src="${imgSrc}" width="50" height="50" style="object-fit:cover; border-radius:5px;" onerror="this.src='${baseUrlServicios}ui/assets/img/no-image.png'" onclick="verImagen('${imgSrc}')"></td>
                    <td><strong>${s.NombrePelado}</strong></td>
                    <td>${s.DescripcionPelado || ''}</td>
                    <td><strong>$${parseFloat(s.PrecioPelado).toFixed(2)}</strong></td>
                    <td><span class="badge badge-info">${s.NombreCategoria || ''}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='editServicio(${JSON.stringify(s)})' title="Editar">
                            <i class="zmdi zmdi-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteServicio(${s.IdPelado})" title="Eliminar">
                            <i class="zmdi zmdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">No hay servicios registrados</td></tr>';
            }
            $('#tbodyServicios').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
            $('#tbodyServicios').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos. Revisa la consola.</td></tr>');
        }
    });
}

function loadCategoriasServicios() {
    $.getJSON(baseUrlServicios + 'categoriapelado/list', function(res) {
        let options = '<option value="">Seleccionar...</option>';
        if (res.categorias) {
            res.categorias.forEach(c => {
                options += `<option value="${c.IdCategoriaPelado}">${c.NombrePelado}</option>`;
            });
        }
        $('#IdCategoriaPelado').html(options);
    });
}

function newServicio() {
    $('#formServicio')[0].reset();
    $('#IdPelado').val('');
    $('#UrlFotoActual').val('');
    $('#preview_foto').hide();
    $('#modalTitle').text('Nuevo Servicio');
    loadCategoriasServicios();
    $('#modalServicio').modal('show');
}

function editServicio(s) {
    Object.keys(s).forEach(k => {
        if (k !== 'UrlFoto') {
            $(`[name="${k}"]`).val(s[k]);
        }
    });
    
    $('#UrlFotoActual').val(s.UrlFoto || '');
    
    if (s.UrlFoto) {
        $('#img_preview').attr('src', baseUrlServicios + 'ui/assets/img/servicios/' + s.UrlFoto);
        $('#preview_foto').show();
    } else {
        $('#preview_foto').hide();
    }
    
    loadCategoriasServicios();
    setTimeout(() => $('#IdCategoriaPelado').val(s.IdCategoriaPelado), 300);
    $('#modalTitle').text('Editar Servicio');
    $('#modalServicio').modal('show');
}

function deleteServicio(id) {
    Swal.fire({
        title: '¿Eliminar servicio?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlServicios + 'pelado/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire('Eliminado', '', 'success');
                    loadServicios();
                }
            });
        }
    });
}

$('#formServicio').submit(function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    
    $.ajax({
        url: baseUrlServicios + 'pelado/save',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#modalServicio').modal('hide');
                Swal.fire('Guardado', res.message || 'Servicio guardado correctamente', 'success');
                loadServicios();
            } else {
                Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Error al procesar la solicitud', 'error');
        }
    });
});

$('#servicioSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    $('#tbodyServicios tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
    });
});

loadServicios();
</script>

<style>
.table img {
    border: 2px solid #f0f0f0;
}

.badge {
    font-size: 11px;
    padding: 4px 8px;
}

#preview_foto {
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>
