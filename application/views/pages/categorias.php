<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white;">
                <h4 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Gestión de Categorías</h4>
                <button class="btn btn-light btn-sm" onclick="newCategoria()">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
                </button>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="categoriaSearch" class="form-control" placeholder="🔍 Buscar categoría...">
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-info" style="font-size: 14px; padding: 10px;">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            <span id="totalCategorias">0</span> categorías
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #3D3D3D; color: white;">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Nombre</th>
                            <th width="35%">Descripción</th>
                            <th width="10%" class="text-center">Productos</th>
                            <th width="10%" class="text-center">Estado</th>
                            <th width="15%" class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyCategorias"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Categoría -->
<div class="modal fade" id="modalCategoria" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCategoria">
                <div class="modal-header" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white;">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdCategoria" id="IdCategoria">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><i class="bi bi-tag"></i> Nombre de la Categoría *</label>
                            <input type="text" name="NombreCategoria" class="form-control" placeholder="Ej: Automotriz, Motos, Camiones" required>
                            <small class="text-muted">Este nombre aparecerá en los filtros y reportes</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label><i class="bi bi-file-text"></i> Descripción</label>
                            <textarea name="DescripcionCategoria" class="form-control" rows="3" placeholder="Descripción detallada de la categoría (opcional)"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Estadísticas -->
<div class="modal fade" id="modalEstadisticas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-bar-chart me-2"></i>Estadísticas de Categoría
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="estadisticasContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table img {
    border: 2px solid #f0f0f0;
}

.badge {
    font-size: 11px;
    padding: 5px 10px;
}

.form-control:focus {
    border-color: #FF6B35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #E85A2A 0%, #FF6B35 100%);
}

.text-right {
    text-align: right;
}

.table tbody tr {
    transition: all 0.3s;
}

.table tbody tr:hover {
    background-color: rgba(255, 107, 53, 0.05);
}

.stats-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    border-left: 4px solid #FF6B35;
}

.stats-card h3 {
    color: #FF6B35;
    margin-bottom: 5px;
}

.stats-card p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 14px;
}
</style>

<script>
var baseUrlCategorias = typeof baseUrl !== 'undefined' ? baseUrl : '';

function loadCategorias() {
    console.log("Cargando categorías con productos...");
    
    $.ajax({
        url: baseUrlCategorias + 'categorias/listWithProducts',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Respuesta:", data);
            let html = '';
            
            if(data.success && data.categorias && data.categorias.length > 0) {
                data.categorias.forEach((c, index) => {
                    const cantidadProductos = parseInt(c.CantidadProductos) || 0;
                    const disponibles = parseInt(c.ProductosDisponibles) || 0;
                    const agotados = parseInt(c.ProductosAgotados) || 0;
                    const estadoCategoria = c.EstadoCategoria || 'ACTIVO';
                    
                    const estadoClass = estadoCategoria === 'ACTIVO' ? 'badge-success' : 'badge-secondary';
                    const estadoTexto = estadoCategoria === 'ACTIVO' ? 'Activa' : 'Inactiva';
                    
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong style="color: #FF6B35;">${c.NombreCategoria}</strong></td>
                    <td>${c.DescripcionCategoria || '<span class="text-muted">Sin descripción</span>'}</td>
                    <td class="text-center">
                        ${cantidadProductos > 0 ? `
                            <button class="btn btn-sm btn-info" onclick="verEstadisticas(${c.IdCategoria}, '${c.NombreCategoria}')" title="Ver detalles">
                                <i class="bi bi-box-seam"></i> ${cantidadProductos}
                            </button>
                        ` : `
                            <span class="badge badge-secondary">0</span>
                        `}
                    </td>
                    <td class="text-center">
                        <span class="badge ${estadoClass}">${estadoTexto}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" onclick='editCategoria(${JSON.stringify(c).replace(/'/g, "&apos;")})' title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${cantidadProductos === 0 ? `
                            <button class="btn btn-sm btn-danger" onclick="deleteCategoria(${c.IdCategoria})" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : `
                            <button class="btn btn-sm btn-warning" onclick="cambiarEstado(${c.IdCategoria}, '${estadoCategoria === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO'}')" title="${estadoCategoria === 'ACTIVO' ? 'Desactivar' : 'Activar'}">
                                <i class="bi bi-toggle-${estadoCategoria === 'ACTIVO' ? 'on' : 'off'}"></i>
                            </button>
                        `}
                    </td>
                </tr>`;
                });
                
                $('#totalCategorias').text(data.categorias.length);
            } else {
                html = '<tr><td colspan="6" class="text-center">No hay categorías registradas</td></tr>';
                $('#totalCategorias').text(0);
            }
            
            $('#tbodyCategorias').html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            console.log("Respuesta:", xhr.responseText);
            $('#tbodyCategorias').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos</td></tr>');
        }
    });
}

function newCategoria() {
    $('#formCategoria')[0].reset();
    $('#IdCategoria').val('');
    $('#modalTitle').html('<i class="bi bi-grid-3x3-gap me-2"></i>Nueva Categoría');
    $('#modalCategoria').modal('show');
}

function editCategoria(c) {
    Object.keys(c).forEach(k => {
        $(`[name="${k}"]`).val(c[k]);
    });
    $('#modalTitle').html('<i class="bi bi-pencil me-2"></i>Editar Categoría');
    $('#modalCategoria').modal('show');
}

function deleteCategoria(id) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF6B35',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlCategorias + 'categorias/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: res.message,
                        confirmButtonColor: '#FF6B35'
                    });
                    loadCategorias();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                        confirmButtonColor: '#FF6B35'
                    });
                }
            });
        }
    });
}

function cambiarEstado(id, nuevoEstado) {
    const textoAccion = nuevoEstado === 'ACTIVO' ? 'activar' : 'desactivar';
    
    Swal.fire({
        title: `¿${textoAccion.charAt(0).toUpperCase() + textoAccion.slice(1)} categoría?`,
        text: `La categoría se ${textoAccion}á`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B35',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrlCategorias + 'categorias/cambiarEstado', {
                IdCategoria: id,
                EstadoCategoria: nuevoEstado
            }, function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        text: res.message,
                        confirmButtonColor: '#FF6B35'
                    });
                    loadCategorias();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message,
                        confirmButtonColor: '#FF6B35'
                    });
                }
            }, 'json');
        }
    });
}

function verEstadisticas(id, nombre) {
    $('#modalEstadisticas').modal('show');
    $('#estadisticasContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>
    `);
    
    $.getJSON(baseUrlCategorias + 'categorias/getById/' + id, function(res) {
        if (res.success && res.categoria) {
            const cat = res.categoria;
            const stats = cat.estadisticas || {};
            
            const html = `
                <h4 class="mb-4"><i class="bi bi-grid-3x3-gap me-2"></i>${nombre}</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3>${stats.TotalProductos || 0}</h3>
                            <p><i class="bi bi-box-seam me-1"></i>Total de Productos</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3>${stats.StockTotal || 0}</h3>
                            <p><i class="bi bi-boxes me-1"></i>Stock Total</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3 class="text-success">${stats.Disponibles || 0}</h3>
                            <p><i class="bi bi-check-circle me-1"></i>Productos Disponibles</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3 class="text-danger">${stats.Agotados || 0}</h3>
                            <p><i class="bi bi-x-circle me-1"></i>Productos Agotados</p>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="stats-card">
                            <h3>S/. ${parseFloat(stats.ValorInventario || 0).toFixed(2)}</h3>
                            <p><i class="bi bi-cash-coin me-1"></i>Valor Total del Inventario</p>
                        </div>
                    </div>
                </div>
            `;
            
            $('#estadisticasContent').html(html);
        }
    });
}

$('#formCategoria').submit(function(e) {
    e.preventDefault();
    
    $.post(baseUrlCategorias + 'categorias/save', $(this).serialize(), function(res) {
        if (res.success) {
            $('#modalCategoria').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: res.message,
                confirmButtonColor: '#FF6B35'
            });
            loadCategorias();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.message,
                confirmButtonColor: '#FF6B35'
            });
        }
    }, 'json');
});

$('#categoriaSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    
    if (v === '') {
        $('#tbodyCategorias tr').show();
    } else {
        $('#tbodyCategorias tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
        });
    }
    
    // Actualizar contador
    const visibleCount = $('#tbodyCategorias tr:visible').length;
    $('#totalCategorias').text(visibleCount);
});

$(document).ready(function() {
    loadCategorias();
});
</script>
