<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Categorías de Servicios</h4>
                <button class="btn btn-success btn-sm" onclick="newCategoria()">+ Nueva Categoría</button>
            </div>

            <div class="card-body">
                <input type="text" id="categoriaSearch" class="form-control mb-3" placeholder="Buscar categoría...">

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyCategorias"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategoria">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCategoria">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Categoría</h5>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdCategoriaPelado" id="IdCategoriaPelado">

                    <div class="mb-3">
                        <label>Nombre *</label>
                        <input type="text" name="NombrePelado" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="DescripcionPelado" class="form-control" rows="3"></textarea>
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

<script>
var baseUrlCategoria = typeof baseUrl !== 'undefined' ? baseUrl : '';

function loadCategorias() {
    $.ajax({
        url: baseUrlCategoria + 'categoriapelado/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if(data.categorias && data.categorias.length > 0) {
                data.categorias.forEach(c => {
                    html += `
                <tr>
                    <td><strong>${c.IdCategoriaPelado}</strong></td>
                    <td><strong>${c.NombrePelado}</strong></td>
                    <td>${c.DescripcionPelado || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='editCategoria(${JSON.stringify(c)})' title="Editar">
                            <i class="zmdi zmdi-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategoria(${c.IdCategoriaPelado})" title="Eliminar">
                            <i class="zmdi zmdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay categorías registradas</td></tr>';
            }
            $('#tbodyCategorias').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            $('#tbodyCategorias').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar datos</td></tr>');
        }
    });
}

function newCategoria() {
    $('#formCategoria')[0].reset();
    $('#IdCategoriaPelado').val('');
    $('#modalTitle').text('Nueva Categoría');
    $('#modalCategoria').modal('show');
}

function editCategoria(c) {
    Object.keys(c).forEach(k => {
        $(`[name="${k}"]`).val(c[k]);
    });
    $('#modalTitle').text('Editar Categoría');
    $('#modalCategoria').modal('show');
}

function deleteCategoria(id) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlCategoria + 'categoriapelado/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire('Eliminado', '', 'success');
                    loadCategorias();
                }
            });
        }
    });
}

$('#formCategoria').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: baseUrlCategoria + 'categoriapelado/save',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#modalCategoria').modal('hide');
                Swal.fire('Guardado', res.message, 'success');
                loadCategorias();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
});

$('#categoriaSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    $('#tbodyCategorias tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
    });
});

loadCategorias();
</script>
