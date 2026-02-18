<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white;">
                <h4 class="mb-0"><i class="bi bi-battery-charging me-2"></i>Gestión de Acumuladores</h4>
                <button class="btn btn-light btn-sm" onclick="newProducto()">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </button>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="productoSearch" class="form-control" placeholder="🔍 Buscar por nombre, código, marca o modelo...">
                    </div>
                    <div class="col-md-2">
                        <select id="filtroCategoria" class="form-control">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="DISPONIBLE">Disponible</option>
                            <option value="AGOTADO">Agotado</option>
                            <option value="DESCONTINUADO">Descontinuado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-secondary btn-block" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle me-1"></i> Limpiar Filtros
                        </button>
                    </div>
                    <div class="col-md-2 text-right">
                        <span class="badge badge-info" style="font-size: 14px; padding: 10px;">
                            <i class="bi bi-box-seam me-1"></i>
                            <span id="totalProductos">0</span> productos
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #3D3D3D; color: white;">
                        <tr>
                            <th>Código</th>                            
                            <th>Producto</th>
                            <th>Especificaciones</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Precio Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyProductos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="formProducto" enctype="multipart/form-data">
                <div class="modal-header" style="background: linear-gradient(135deg, #FF6B35 0%, #E85A2A 100%); color: white;">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bi bi-battery-charging me-2"></i>Nuevo Acumulador
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdProducto" id="IdProducto">
                    <input type="hidden" name="ImagenActual" id="ImagenActual">

                    <ul class="nav nav-tabs" id="productoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info">Información General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="specs-tab" data-toggle="tab" href="#specs">Especificaciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="precios-tab" data-toggle="tab" href="#precios">Precios e Inventario</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Tab 1: Información General -->
                        <div class="tab-pane fade show active" id="info">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-upc-scan"></i> Código del Producto *</label>
                                    <div class="input-group">
                                        <input type="text" name="CodigoProducto" id="CodigoProducto" class="form-control" placeholder="Se genera automático">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="generarCodigo()" title="Generar código automático">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Ejemplo: BAT-AUTO-001</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-tag"></i> Categoría *</label>
                                    <select name="IdCategoria" id="IdCategoria" class="form-control" required onchange="generarCodigo()"></select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label><i class="bi bi-card-text"></i> Nombre del Producto *</label>
                                    <input type="text" name="NombreProducto" class="form-control" placeholder="Ej: Batería ETNA 12V 45Ah" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label><i class="bi bi-file-text"></i> Descripción</label>
                                    <textarea name="DescripcionProducto" class="form-control" rows="3" placeholder="Descripción detallada del producto"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-building"></i> Proveedor</label>
                                    <select name="IdProveedor" id="IdProveedor" class="form-control"></select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-award"></i> Marca *</label>
                                    <input type="text" name="Marca" class="form-control" placeholder="Ej: ETNA" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-diagram-3"></i> Modelo</label>
                                    <input type="text" name="Modelo" class="form-control" placeholder="Ej: EM-45">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label><i class="bi bi-image"></i> Imagen del Producto</label>
                                    <input type="file" name="imagen_producto" id="imagen_producto" class="form-control" accept="image/*">
                                    <small class="text-muted">JPG, PNG, GIF, WEBP. Máx 2MB</small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div id="preview_imagen" style="display:none; text-align:center; padding:15px; background:#f8f9fa; border-radius:8px;">
                                        <img id="img_preview" src="" style="max-width: 250px; max-height: 250px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Especificaciones -->
                        <div class="tab-pane fade" id="specs">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-lightning-charge"></i> Voltaje (V)</label>
                                    <input type="number" step="0.01" name="Voltaje" class="form-control" placeholder="12.00">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-battery-full"></i> Amperaje (Ah)</label>
                                    <input type="number" step="0.01" name="Amperaje" class="form-control" placeholder="45.00">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-gear"></i> Tipo de Acumulador</label>
                                    <select name="TipoAcumulador" class="form-control">
                                        <option value="PLOMO_ACIDO">Plomo Ácido</option>
                                        <option value="AGM">AGM (Absorbed Glass Mat)</option>
                                        <option value="GEL">GEL</option>
                                        <option value="LITIO">Litio</option>
                                        <option value="CALCIO">Calcio</option>
                                        <option value="ARRANQUE">Arranque</option>
                                        <option value="CICLADO_PROFUNDO">Ciclado Profundo</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-truck"></i> Aplicación</label>
                                    <input type="text" name="Aplicacion" class="form-control" placeholder="Ej: Auto sedán, Camión, Solar">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-calendar-check"></i> Garantía (meses)</label>
                                    <input type="number" name="Garantia" class="form-control" placeholder="12">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label><i class="bi bi-box"></i> Peso (Kg)</label>
                                    <input type="number" step="0.01" name="Peso" class="form-control" placeholder="11.5">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label><i class="bi bi-rulers"></i> Dimensiones (LxWxH cm)</label>
                                    <input type="text" name="Dimensiones" class="form-control" placeholder="24x17x19">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label><i class="bi bi-upc"></i> Código de Barras</label>
                                    <input type="text" name="CodigoBarras" class="form-control" placeholder="7751234560001">
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Precios e Inventario -->
                        <div class="tab-pane fade" id="precios">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-cash-coin"></i> Precio de Costo * (S/.)</label>
                                    <input type="number" step="0.01" name="PrecioCosto" id="PrecioCosto" class="form-control" placeholder="180.00" required onchange="calcularGanancia()">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-tag-fill"></i> Precio de Venta * (S/.)</label>
                                    <input type="number" step="0.01" name="PrecioVenta" id="PrecioVenta" class="form-control" placeholder="250.00" required onchange="calcularGanancia()">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-graph-up"></i> % Ganancia</label>
                                    <input type="text" id="PorcentajeGanancia" class="form-control" readonly style="background-color: #f0f0f0;">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-box-seam"></i> Stock Actual *</label>
                                    <input type="number" name="Stock" class="form-control" value="0" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-arrow-down-circle"></i> Stock Mínimo</label>
                                    <input type="number" name="StockMinimo" class="form-control" value="5">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label><i class="bi bi-arrow-up-circle"></i> Stock Máximo</label>
                                    <input type="number" name="StockMaximo" class="form-control" value="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Imagen -->
<div class="modal fade" id="modalVerImagen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Producto</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagenModal" src="" style="max-width: 100%; max-height: 500px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<style>
.table img {
    border: 2px solid #f0f0f0;
    cursor: pointer;
    transition: transform 0.2s;
}

.table img:hover {
    transform: scale(1.1);
    border-color: #FF6B35;
}

.badge {
    font-size: 11px;
    padding: 5px 10px;
}

#preview_imagen {
    margin-top: 10px;
}

.nav-tabs .nav-link {
    color: #3D3D3D;
    font-weight: 600;
}

.nav-tabs .nav-link.active {
    background-color: #FF6B35;
    color: white;
    border-color: #FF6B35;
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

/* Estilos para filtros */
#filtroCategoria, #filtroEstado {
    border: 2px solid #ddd;
    font-weight: 500;
}

#filtroCategoria:focus, #filtroEstado:focus {
    border-color: #FF6B35;
}

#totalProductos {
    font-weight: bold;
    font-size: 16px;
}

.btn-block {
    width: 100%;
}

.text-right {
    text-align: right;
}
</style>

<script>
var baseUrlProductos = typeof baseUrl !== 'undefined' ? baseUrl : '';
let productosFiltrados = null;
// Preview de imagen
$('#imagen_producto').on('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#img_preview').attr('src', e.target.result);
            $('#preview_imagen').show();
        }
        reader.readAsDataURL(file);
    } else {
        $('#preview_imagen').hide();
    }
});

// Ver imagen en modal
function verImagen(imgSrc) {
    $('#imagenModal').attr('src', imgSrc);
    $('#modalVerImagen').modal('show');
}

// Calcular porcentaje de ganancia
function calcularGanancia() {
    const costo = parseFloat($('#PrecioCosto').val()) || 0;
    const venta = parseFloat($('#PrecioVenta').val()) || 0;
    
    if (costo > 0 && venta > 0) {
        const ganancia = ((venta - costo) / costo) * 100;
        $('#PorcentajeGanancia').val(ganancia.toFixed(2) + '%');
    } else {
        $('#PorcentajeGanancia').val('');
    }
}

// Cargar productos con filtros
function loadProductos() {
    console.log("Cargando productos...");
    
    // Obtener valores de los filtros
    const filtroCategoria = $('#filtroCategoria').val();
    const filtroEstado = $('#filtroEstado').val();
    
    $.ajax({
        url: baseUrlProductos + 'productos/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Respuesta:", data);
            let html = '';
            
            if(data.success && data.productos && data.productos.length > 0) {
                // Aplicar filtros en el frontend
                 productosFiltrados = data.productos;
                
                if (filtroCategoria) {
                    productosFiltrados = productosFiltrados.filter(p => p.IdCategoria == filtroCategoria);
                }
                
                if (filtroEstado) {
                    productosFiltrados = productosFiltrados.filter(p => p.EstadoProducto === filtroEstado);
                }
                
                console.log("Productos filtrados:", productosFiltrados.length);
                
                if (productosFiltrados.length === 0) {
                    html = '<tr><td colspan="9" class="text-center">No hay productos que coincidan con los filtros seleccionados</td></tr>';
                } else {
                    productosFiltrados.forEach(p => {
                    const imgSrc = p.ImagenProducto ? 
                        baseUrlProductos + 'ui/assets/img/productos/' + p.ImagenProducto : 
                        baseUrlProductos + 'ui/assets/img/no-image.png';
                    
                    const estadoClass = {
                        'DISPONIBLE': 'badge-success',
                        'AGOTADO': 'badge-danger',
                        'DESCONTINUADO': 'badge-secondary'
                    };
                    
                    html += `
                <tr>
                    <td><strong>${p.CodigoProducto}</strong></td>                    
                    <td>
                        <strong>${p.NombreProducto}</strong><br>
                        <small class="text-muted">${p.Marca || ''} ${p.Modelo || ''}</small>
                    </td>
                    <td>
                        <small>
                            ${p.Voltaje ? p.Voltaje + 'V' : ''} 
                            ${p.Amperaje ? p.Amperaje + 'Ah' : ''}<br>
                            ${p.TipoAcumulador || ''}
                        </small>
                    </td>
                    <td><span class="badge badge-info">${p.NombreCategoria || ''}</span></td>
                    <td>
                        <strong style="color: ${p.Stock <= p.StockMinimo ? '#dc3545' : '#28a745'};">
                            ${p.Stock}
                        </strong>
                        ${p.Stock <= p.StockMinimo ? '<i class="bi bi-exclamation-triangle text-warning" title="Stock bajo"></i>' : ''}
                    </td>
                    <td><strong style="color: #FF6B35;">S/. ${parseFloat(p.PrecioVenta).toFixed(2)}</strong></td>
                    <td><span class="badge ${estadoClass[p.EstadoProducto]}">${p.EstadoProducto}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='editProducto(${JSON.stringify(p).replace(/'/g, "&apos;")})' title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProducto(${p.IdProducto})" title="Descontinuar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
                    });
                }
            } else {
                html = '<tr><td colspan="9" class="text-center">No hay productos registrados</td></tr>';
            }
            
            $('#tbodyProductos').html(html);
            
            // Actualizar contador
            const visibleCount = $('#tbodyProductos tr:visible').length;
            $('#totalProductos').text(productosFiltrados ? productosFiltrados.length : (data.productos ? data.productos.length : 0));
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            console.log("Respuesta:", xhr.responseText);
            $('#tbodyProductos').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar datos</td></tr>');
        }
    });
}

// Cargar categorías
function loadCategorias() {
    $.getJSON(baseUrlProductos + 'categorias/list', function(res) {
        let options = '<option value="">Seleccionar...</option>';
        let optionsFiltro = '<option value="">Todas las categorías</option>';
        
        if (res.categorias) {
            res.categorias.forEach(c => {
                options += `<option value="${c.IdCategoria}">${c.NombreCategoria}</option>`;
                optionsFiltro += `<option value="${c.IdCategoria}">${c.NombreCategoria}</option>`;
            });
        }
        
        $('#IdCategoria').html(options);
        $('#filtroCategoria').html(optionsFiltro);
    });
}

// Cargar proveedores
function loadProveedores() {
    $.getJSON(baseUrlProductos + 'proveedores/list', function(res) {
        let options = '<option value="">Sin proveedor</option>';
        if (res.proveedores) {
            res.proveedores.forEach(p => {
                options += `<option value="${p.IdProveedor}">${p.NombreProveedor}</option>`;
            });
        }
        $('#IdProveedor').html(options);
    });
}

// Generar código automático
function generarCodigo() {
    const idCategoria = $('#IdCategoria').val();
    if (!idCategoria) {
        Swal.fire('Info', 'Selecciona una categoría primero', 'info');
        return;
    }
    
    $.getJSON(baseUrlProductos + 'productos/generarCodigo?idCategoria=' + idCategoria, function(res) {
        if (res.success) {
            $('#CodigoProducto').val(res.codigo);
        }
    });
}

// Nuevo producto
function newProducto() {
    $('#formProducto')[0].reset();
    $('#IdProducto').val('');
    $('#ImagenActual').val('');
    $('#preview_imagen').hide();
    $('#PorcentajeGanancia').val('');
    $('#modalTitle').html('<i class="bi bi-battery-charging me-2"></i>Nuevo Acumulador');
    loadCategorias();
    loadProveedores();
    $('#modalProducto').modal('show');
}

// Editar producto
function editProducto(p) {
    Object.keys(p).forEach(k => {
        if (k !== 'ImagenProducto') {
            $(`[name="${k}"]`).val(p[k]);
        }
    });
    
    $('#ImagenActual').val(p.ImagenProducto || '');
    
    if (p.ImagenProducto) {
        $('#img_preview').attr('src', baseUrlProductos + 'ui/assets/img/productos/' + p.ImagenProducto);
        $('#preview_imagen').show();
    } else {
        $('#preview_imagen').hide();
    }
    
    calcularGanancia();
    loadCategorias();
    loadProveedores();
    
    setTimeout(() => {
        $('#IdCategoria').val(p.IdCategoria);
        $('#IdProveedor').val(p.IdProveedor);
    }, 300);
    
    $('#modalTitle').html('<i class="bi bi-pencil me-2"></i>Editar Producto');
    $('#modalProducto').modal('show');
}

// Eliminar producto
function deleteProducto(id) {
    Swal.fire({
        title: '¿Descontinuar producto?',
        text: 'El producto se marcará como descontinuado',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF6B35',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, descontinuar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlProductos + 'productos/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire('Eliminado', res.message, 'success');
                    loadProductos();
                }
            });
        }
    });
}

// Guardar producto
$('#formProducto').submit(function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    
    $.ajax({
        url: baseUrlProductos + 'productos/save',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#modalProducto').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: res.message,
                    confirmButtonColor: '#FF6B35'
                });
                loadProductos();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Error al procesar la solicitud', 'error');
            console.error(xhr.responseText);
        }
    });
});

// Búsqueda en tiempo real (respeta filtros)
$('#productoSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    
    if (v === '') {
        $('#tbodyProductos tr').show();
    } else {
        $('#tbodyProductos tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1);
        });
    }
    
    // Actualizar contador de productos visibles
    const visibleCount = $('#tbodyProductos tr:visible').length;
    $('#totalProductos').text(visibleCount);
});

// Limpiar filtros
function limpiarFiltros() {
    $('#filtroCategoria').val('');
    $('#filtroEstado').val('');
    $('#productoSearch').val('');
    loadProductos();
}

// Filtro por categoría
$('#filtroCategoria').on('change', function() {
    console.log("Filtro categoría cambiado:", $(this).val());
    loadProductos();
});

// Filtro por estado
$('#filtroEstado').on('change', function() {
    console.log("Filtro estado cambiado:", $(this).val());
    loadProductos();
});

// Cargar al inicio
$(document).ready(function() {
    loadProductos();
    loadCategorias();
});
</script>
