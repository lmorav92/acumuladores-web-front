<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Barberías</h4>
                <button class="btn btn-success btn-sm" onclick="newBarberia()">+ Nueva Barbería</button>
            </div>

            <div class="card-body">
                <input type="text" id="barberiaSearch" class="form-control mb-3" placeholder="Buscar barbería...">

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyBarberias"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBarberia">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formBarberia">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Barbería</h5>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdBarberia" id="IdBarberia">

                    <div class="mb-3">
                        <label>Nombre *</label>
                        <input type="text" name="NombreBarberia" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Dirección *</label>
                        <textarea name="Direccion" class="form-control" rows="3" required></textarea>
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
var baseUrlBarberia = typeof baseUrl !== 'undefined' ? baseUrl : '';

function loadBarberias() {
    $.ajax({
        url: baseUrlBarberia + 'barberia/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '';
            if(data.barberias && data.barberias.length > 0) {
                data.barberias.forEach(b => {
                    html += `
                <tr>
                    <td><strong>${b.IdBarberia}</strong></td>
                    <td><strong>${b.NombreBarberia}</strong></td>
                    <td>${b.Direccion || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='editBarberia(${JSON.stringify(b)})' title="Editar">
                            <i class="zmdi zmdi-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBarberia(${b.IdBarberia})" title="Eliminar">
                            <i class="zmdi zmdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay barberías registradas</td></tr>';
            }
            $('#tbodyBarberias').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            $('#tbodyBarberias').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar datos</td></tr>');
        }
    });
}

function newBarberia() {
    $('#formBarberia')[0].reset();
    $('#IdBarberia').val('');
    $('#modalTitle').text('Nueva Barbería');
    $('#modalBarberia').modal('show');
}

function editBarberia(b) {
    Object.keys(b).forEach(k => {
        $(`[name="${k}"]`).val(b[k]);
    });
    $('#modalTitle').text('Editar Barbería');
    $('#modalBarberia').modal('show');
}

function deleteBarberia(id) {
    Swal.fire({
        title: '¿Eliminar barbería?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlBarberia + 'barberia/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire('Eliminado', '', 'success');
                    loadBarberias();
                }
            });
        }
    });
}

$('#formBarberia').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: baseUrlBarberia + 'barberia/save',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#modalBarberia').modal('hide');
                Swal.fire('Guardado', res.message, 'success');
                loadBarberias();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
});

$('#barberiaSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    $('#tbodyBarberias tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
    });
});

loadBarberias();
</script>
