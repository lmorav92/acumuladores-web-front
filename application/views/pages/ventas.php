<div class="row">
    <!-- Estadísticas -->
    <div class="col-lg-3 col-md-6">
        <div class="card gradient-deepblue text-white">
            <div class="card-body">
                <h5 class="mb-0"><span id="stat_total_ventas">0</span></h5>
                <small>Total Ventas</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card gradient-orange text-white">
            <div class="card-body">
                <h5 class="mb-0"><span id="stat_pendientes">0</span></h5>
                <small>Pendientes</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card gradient-purple text-white">
            <div class="card-body">
                <h5 class="mb-0"><span id="stat_por_comprobar">0</span></h5>
                <small>Por Comprobar</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card gradient-green text-white">
            <div class="card-body">
                <h5 class="mb-0">$<span id="stat_ingresos">0</span></h5>
                <small>Total Ingresos</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Gestión de Ventas</h4>
                <button class="btn btn-success btn-sm" onclick="newVenta()">+ Nueva Venta</button>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="ventaSearch" class="form-control" placeholder="Buscar...">
                    </div>
                    <div class="col-md-3">
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="por_comprobar">Por Comprobar</option>
                            <option value="Pagado">Pagado</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="date" id="fecha_inicio" class="form-control">
                            <input type="date" id="fecha_fin" class="form-control">
                            <button class="btn btn-primary" onclick="filtrarPorFecha()">Filtrar</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyVentas"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Venta -->
<div class="modal fade" id="modalVenta">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formVenta">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Venta</h5>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="IdOrden" id="IdOrden">
                    <input type="hidden" name="IdPelado" value="1">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Cliente *</label>
                            <select name="IdCliente" id="IdCliente" class="form-control" required>
                                <option value="">Seleccionar cliente...</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Producto *</label>
                            <select name="IdProducto" id="IdProducto" class="form-control" required>
                                <option value="">Seleccionar producto...</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Cantidad *</label>
                            <input type="number" name="Cantidad" id="Cantidad" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Precio Unitario</label>
                            <input type="text" id="PrecioUnitario" class="form-control" readonly>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Total</label>
                            <input type="text" id="TotalVenta" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Estado *</label>
                            <select name="EstadoOrden" class="form-control" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="por_comprobar">Por Comprobar</option>
                                <option value="Pagado">Pagado</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Stock Disponible</label>
                            <input type="text" id="StockDisponible" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Compra -->
<div class="modal fade" id="modalConfirmarCompra">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="zmdi zmdi-check-circle"></i> Confirmar Compra</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="infoConfirmacion"></div>
                
                <div class="alert alert-info mt-3">
                    <i class="zmdi zmdi-info"></i> 
                    <strong>Esta acción:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Cambiará el estado a <strong>"Pagado"</strong></li>
                        <li>Registrará la confirmación en el historial</li>
                        <li>El stock ya fue descontado previamente</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarCompra">
                    <i class="zmdi zmdi-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var baseUrlVentas = typeof baseUrl !== 'undefined' ? baseUrl : '';
var ventasData = [];
var ventaParaConfirmar = null;

function loadEstadisticas() {
    $.getJSON(baseUrlVentas + 'ventas/estadisticas', function(res) {
        if (res.success) {
            $('#stat_total_ventas').text(res.estadisticas.total_ventas || 0);
            $('#stat_pendientes').text(res.estadisticas.ventas_pendientes || 0);
            $('#stat_por_comprobar').text(res.estadisticas.ventas_por_comprobar || 0);
            $('#stat_ingresos').text(parseFloat(res.estadisticas.total_ingresos || 0).toFixed(2));
        }
    });
}

function loadVentas() {
    $.ajax({
        url: baseUrlVentas + 'ventas/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            ventasData = data.ventas || [];
            renderVentas(ventasData);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            $('#tbodyVentas').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar ventas.</td></tr>');
        }
    });
}

function renderVentas(ventas) {
    let html = '';
    if(ventas && ventas.length > 0) {
        ventas.forEach((v, index) => {
            let badgeClass = 'badge-secondary';
            let estadoTexto = v.EstadoOrden;
            
            switch(v.EstadoOrden) {
                case 'Pagado':
                    badgeClass = 'badge-success';
                    break;
                case 'Pendiente':
                    badgeClass = 'badge-warning';
                    break;
                case 'por_comprobar':
                    badgeClass = 'badge-info';
                    estadoTexto = 'Por Comprobar';
                    break;
            }
            
            html += `
            <tr>
                <td>${v.IdOrden}</td>
                <td>${v.CreatedDate || ''}</td>
                <td>${v.NombreCliente || ''} ${v.ApellidosCliente || ''}</td>
                <td><strong>${v.NombreProducto || ''}</strong></td>
                <td>${v.Cantidad}</td>
                <td>$${parseFloat(v.PrecioProducto).toFixed(2)}</td>
                <td><strong>$${parseFloat(v.Total).toFixed(2)}</strong></td>
                <td><span class="badge ${badgeClass}">${estadoTexto}</span></td>
                <td>
                    ${v.EstadoOrden === 'por_comprobar' ? `
                    <button class="btn btn-sm btn-success" onclick='confirmarCompra(${JSON.stringify(v)})' title="Confirmar pago">
                        <i class="zmdi zmdi-check-circle"></i> 
                    </button>` : ''}
                    
                    ${v.EstadoOrden === 'Pendiente' ? `
                    <button class="btn btn-sm btn-success" onclick="cambiarEstado(${v.IdOrden}, 'Pagado')" title="Marcar como pagado">
                        <i class="zmdi zmdi-check"></i>
                    </button>` : ''}
                    
                    <button class="btn btn-sm btn-info" onclick='verDetalleVenta(${JSON.stringify(v)})' title="Ver detalle">
                        <i class="zmdi zmdi-eye"></i>
                    </button>
                    
                    ${v.EstadoOrden !== 'Pagado' ? `
                    <button class="btn btn-sm btn-danger" onclick="deleteVenta(${v.IdOrden})" title="Eliminar">
                        <i class="zmdi zmdi-delete"></i>
                    </button>` : ''}
                </td>
            </tr>`;
        });
    } else {
        html = '<tr><td colspan="9" class="text-center">No hay ventas registradas</td></tr>';
    }
    $('#tbodyVentas').html(html);
}

function confirmarCompra(venta) {
    ventaParaConfirmar = venta;
    
    $('#infoConfirmacion').html(`
        <div class="text-center mb-3">
            <i class="zmdi zmdi-shopping-cart" style="font-size: 48px; color: #28a745;"></i>
        </div>
        <h6><strong>Venta #${venta.IdOrden}</strong></h6>
        <hr>
        <p><strong>Cliente:</strong> ${venta.NombreCliente} ${venta.ApellidosCliente}</p>
        <p><strong>Producto:</strong> ${venta.NombreProducto}</p>
        <p><strong>Cantidad:</strong> ${venta.Cantidad}</p>
        <p><strong>Total:</strong> <span class="text-success">$${parseFloat(venta.Total).toFixed(2)}</span></p>
        <p><strong>Fecha:</strong> ${venta.CreatedDate}</p>
    `);
    
    $('#modalConfirmarCompra').modal('show');
}

$('#btnConfirmarCompra').on('click', function() {
    if (!ventaParaConfirmar) return;
    
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="zmdi zmdi-spinner zmdi-hc-spin"></i> Procesando...');
    
    $.ajax({
        url: baseUrlVentas + 'ventas/confirmar_compra',
        type: 'POST',
        data: {
            IdOrden: ventaParaConfirmar.IdOrden,
            IdProducto: ventaParaConfirmar.TB_PRODUCTO_IdProducto || ventaParaConfirmar.IdProducto
        },
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="zmdi zmdi-check"></i> Confirmar Pago');
            
            if (res.success) {
                $('#modalConfirmarCompra').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Compra Confirmada!',
                    html: res.message,
                    confirmButtonColor: '#28a745'
                });
                loadVentas();
                loadEstadisticas();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="zmdi zmdi-check"></i> Confirmar Pago');
            Swal.fire('Error', 'No se pudo confirmar la compra', 'error');
        }
    });
});

function loadClientes() {
    $.getJSON(baseUrlVentas + 'clientes/list', function(res) {
        let options = '<option value="">Seleccionar cliente...</option>';
        if (res.clientes) {
            res.clientes.forEach(c => {
                options += `<option value="${c.IdCliente}">${c.NombreCliente} ${c.ApellidosCliente}</option>`;
            });
        }
        $('#IdCliente').html(options);
    });
}

function loadProductosVenta() {
    $.getJSON(baseUrlVentas + 'productos/list', function(res) {
        let options = '<option value="">Seleccionar producto...</option>';
        if (res.productos) {
            res.productos.forEach(p => {
                if (p.Stock > 0) {
                    options += `<option value="${p.IdProducto}" data-precio="${p.PrecioProducto}" data-stock="${p.Stock}">${p.NombreProducto} - Stock: ${p.Stock}</option>`;
                }
            });
        }
        $('#IdProducto').html(options);
    });
}

$('#IdProducto').on('change', function() {
    const selected = $(this).find(':selected');
    const precio = selected.data('precio') || 0;
    const stock = selected.data('stock') || 0;
    
    $('#PrecioUnitario').val('$' + parseFloat(precio).toFixed(2));
    $('#StockDisponible').val(stock);
    calcularTotal();
});

$('#Cantidad').on('input', function() {
    calcularTotal();
});

function calcularTotal() {
    const cantidad = parseInt($('#Cantidad').val()) || 0;
    const precioText = $('#PrecioUnitario').val().replace('$', '');
    const precio = parseFloat(precioText) || 0;
    const total = cantidad * precio;
    $('#TotalVenta').val('$' + total.toFixed(2));
}

function newVenta() {
    $('#formVenta')[0].reset();
    $('#IdOrden').val('');
    $('#modalTitle').text('Nueva Venta');
    loadClientes();
    loadProductosVenta();
    $('#modalVenta').modal('show');
}

function cambiarEstado(id, estado) {
    Swal.fire({
        title: '¿Cambiar estado?',
        text: `Marcar como ${estado}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrlVentas + 'ventas/cambiarEstado', {IdOrden: id, EstadoOrden: estado}, function(res) {
                if (res.success) {
                    Swal.fire('Actualizado', '', 'success');
                    loadVentas();
                    loadEstadisticas();
                }
            }, 'json');
        }
    });
}

function verDetalleVenta(v) {
    Swal.fire({
        title: 'Detalle de Venta #' + v.IdOrden,
        html: `
            <div class="text-left">
                <p><strong>Cliente:</strong> ${v.NombreCliente} ${v.ApellidosCliente}</p>
                <p><strong>Email:</strong> ${v.Email || 'N/A'}</p>
                <p><strong>Teléfono:</strong> ${v.Telefono || 'N/A'}</p>
                <hr>
                <p><strong>Producto:</strong> ${v.NombreProducto}</p>
                <p><strong>Cantidad:</strong> ${v.Cantidad}</p>
                <p><strong>Precio Unitario:</strong> $${parseFloat(v.PrecioProducto).toFixed(2)}</p>
                <p><strong>Total:</strong> <span class="text-success">$${parseFloat(v.Total).toFixed(2)}</span></p>
                <p><strong>Estado:</strong> <span class="badge ${v.EstadoOrden === 'Pagado' ? 'badge-success' : v.EstadoOrden === 'por_comprobar' ? 'badge-info' : 'badge-warning'}">${v.EstadoOrden === 'por_comprobar' ? 'Por Comprobar' : v.EstadoOrden}</span></p>
                <p><strong>Fecha:</strong> ${v.CreatedDate}</p>
            </div>
        `,
        width: 500
    });
}

function deleteVenta(id) {
    Swal.fire({
        title: '¿Eliminar venta?',
        text: 'Esta acción devolverá el stock si está pendiente o por comprobar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar'
    }).then(r => {
        if (r.isConfirmed) {
            $.getJSON(baseUrlVentas + 'ventas/delete/' + id, function(res) {
                if (res.success) {
                    Swal.fire('Eliminado', '', 'success');
                    loadVentas();
                    loadEstadisticas();
                }
            });
        }
    });
}

$('#formVenta').submit(function(e) {
    e.preventDefault();
    
    const stock = parseInt($('#StockDisponible').val()) || 0;
    const cantidad = parseInt($('#Cantidad').val()) || 0;
    
    if (cantidad > stock) {
        Swal.fire('Error', 'La cantidad supera el stock disponible', 'error');
        return;
    }
    
    $.post(baseUrlVentas + 'ventas/save', $(this).serialize(), function(res) {
        if (res.success) {
            $('#modalVenta').modal('hide');
            Swal.fire('Guardado', res.message, 'success');
            loadVentas();
            loadEstadisticas();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    }, 'json');
});

$('#ventaSearch').on('keyup', function() {
    let v = $(this).val().toLowerCase();
    let filtered = ventasData.filter(venta => {
        const text = `${venta.NombreCliente} ${venta.ApellidosCliente} ${venta.NombreProducto} ${venta.EstadoOrden}`.toLowerCase();
        return text.indexOf(v) > -1;
    });
    renderVentas(filtered);
});

$('#filtroEstado').on('change', function() {
    const estado = $(this).val();
    if (estado === '') {
        renderVentas(ventasData);
    } else {
        const filtered = ventasData.filter(v => v.EstadoOrden === estado);
        renderVentas(filtered);
    }
});

function filtrarPorFecha() {
    const inicio = $('#fecha_inicio').val();
    const fin = $('#fecha_fin').val();
    
    if (!inicio || !fin) {
        Swal.fire('Error', 'Seleccione ambas fechas', 'error');
        return;
    }
    
    $.getJSON(baseUrlVentas + 'ventas/byFecha', {fecha_inicio: inicio, fecha_fin: fin}, function(res) {
        if (res.success) {
            renderVentas(res.ventas);
        }
    });
}

loadVentas();
loadEstadisticas();
</script>

<style>
.gradient-deepblue { background: linear-gradient(45deg, #4099ff, #73b4ff); }
.gradient-orange { background: linear-gradient(45deg, #ff8c00, #ffb347); }
.gradient-green { background: linear-gradient(45deg, #28a745, #5cb85c); }
.gradient-purple { background: linear-gradient(45deg, #6f42c1, #9b7bd5); }
</style>
