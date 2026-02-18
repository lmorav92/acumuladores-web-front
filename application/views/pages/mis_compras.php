<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="zmdi zmdi-shopping-basket"></i> Mis Compras</h4>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select id="filterEstado" class="form-control">
                            <option value="">Todos los Estados</option>
                            <option value="por_comprobar">Pendiente</option>
                            <option value="Pagado">Pagado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filterFechaInicio" class="form-control" placeholder="Fecha Inicio">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filterFechaFin" class="form-control" placeholder="Fecha Fin">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-block" onclick="filtrarCompras()">
                            <i class="zmdi zmdi-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <!-- Lista de Compras -->
                <div id="comprasContainer">
                    <!-- Se cargarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle de Compra -->
<div class="modal fade" id="modalDetalleCompra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="zmdi zmdi-receipt"></i> Detalle de Compra
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalleCompraContent">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.compra-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.compra-card:hover {
    box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

.compra-card.estado-pagado {
    border-left-color: #28a745;
}

.compra-card.estado-pendiente {
    border-left-color: #ffc107;
}

.compra-card.estado-cancelado {
    border-left-color: #dc3545;
}

.compra-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
}

.compra-numero {
    font-size: 18px;
    font-weight: bold;
    color: #1A1A1A;
}

.compra-fecha {
    color: #666;
    font-size: 14px;
}

.estado-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.estado-badge.pagado {
    background: #28a745;
    color: white;
}

.estado-badge.pendiente {
    background: #ffc107;
    color: #1A1A1A;
}

.estado-badge.cancelado {
    background: #dc3545;
    color: white;
}

.compra-items {
    margin: 15px 0;
}

.item-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.item-nombre {
    flex: 1;
    font-weight: 500;
}

.item-cantidad {
    width: 80px;
    text-align: center;
    color: #666;
}

.item-precio {
    width: 100px;
    text-align: right;
    font-weight: bold;
    color: #D4AF37;
}

.compra-total {
    text-align: right;
    padding-top: 15px;
    margin-top: 15px;
    border-top: 2px solid #D4AF37;
}

.compra-total h5 {
    color: #D4AF37;
    font-weight: bold;
}

.compra-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 80px;
    color: #ccc;
    margin-bottom: 20px;
}

.timeline-item {
    padding: 15px;
    border-left: 3px solid #D4AF37;
    margin-left: 10px;
    position: relative;
}

.timeline-item::before {
    content: '';
    width: 15px;
    height: 15px;
    background: #D4AF37;
    border-radius: 50%;
    position: absolute;
    left: -9px;
    top: 15px;
}

.timeline-item.active::before {
    background: #28a745;
    box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2);
}
</style>

<script>
var comprasData = [];

// Cargar compras
function cargarCompras(filtros = {}) {
    $('#comprasContainer').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3">Cargando compras...</p>
        </div>
    `);

    $.ajax({
        url: baseUrl + 'ventas/mis_compras',
        type: 'GET',
        data: filtros,
        dataType: 'json',
        success: function(data) {
            if (!data.success || !data.compras) {
                $('#comprasContainer').html(`
                    <div class="empty-state">
                        <i class="zmdi zmdi-shopping-basket"></i>
                        <h4>No tienes compras registradas</h4>
                        <p class="text-muted">Cuando realices compras aparecerán aquí</p>
                        <a href="javascript:void(0);" class="btn btn-primary menu-link" data-page="tienda">
                            <i class="zmdi zmdi-store"></i> Ir a la Tienda
                        </a>
                    </div>
                `);
                return;
            }

            comprasData = data.compras;
            mostrarCompras(comprasData);
        },
        error: function() {
            $('#comprasContainer').html(`
                <div class="alert alert-danger">
                    <i class="zmdi zmdi-alert-circle"></i> Error al cargar las compras
                </div>
            `);
        }
    });
}

// Mostrar compras
function mostrarCompras(compras) {
    if (compras.length === 0) {
        $('#comprasContainer').html(`
            <div class="empty-state">
                <i class="zmdi zmdi-search"></i>
                <h4>No se encontraron compras</h4>
                <p class="text-muted">Intenta con otros filtros</p>
            </div>
        `);
        return;
    }

    let html = '';
    compras.forEach(compra => {
        const estadoClass = compra.EstadoOrden === 'Pagado' ? 'pagado' : 
                           compra.EstadoOrden === 'por_comprobar' ? 'pendiente' : 'cancelado';
        
        const estadoTexto = compra.EstadoOrden === 'Pagado' ? 'Pagado' : 
                           compra.EstadoOrden === 'por_comprobar' ? 'Pendiente' : 'Cancelado';

        html += `
        <div class="compra-card estado-${estadoClass}">
            <div class="compra-header">
                <div>
                    <div class="compra-numero">Orden #${compra.IdOrden}</div>
                    <div class="compra-fecha">
                        <i class="zmdi zmdi-calendar"></i> ${formatearFecha(compra.CreatedDate)}
                    </div>
                </div>
                <span class="estado-badge ${estadoClass}">${estadoTexto}</span>
            </div>
            
            <div class="compra-items">
                <div class="item-row">
                    <div class="item-nombre">
                        <i class="zmdi zmdi-shopping-basket"></i> ${compra.NombreProducto}
                        <small class="d-block text-muted">${compra.NombreCategoria || 'Sin categoría'}</small>
                    </div>
                    <div class="item-cantidad">
                        Cant: ${compra.Cantidad}
                    </div>
                    <div class="item-precio">
                        $${parseFloat(compra.PrecioProducto).toFixed(2)}
                    </div>
                </div>
            </div>
            
            <div class="compra-total">
                <h5>Total: $${(compra.Cantidad * parseFloat(compra.PrecioProducto)).toFixed(2)}</h5>
            </div>
            
            <div class="compra-actions">
                <button class="btn btn-sm btn-primary" onclick="verDetalle(${compra.IdOrden})">
                    <i class="zmdi zmdi-eye"></i> Ver Detalle
                </button>
                ${compra.EstadoOrden === 'por_comprobar' ? `
                    <button class="btn btn-sm btn-success" onclick="confirmarPago(${compra.IdOrden})">
                        <i class="zmdi zmdi-check"></i> Confirmar Pago
                    </button>
                ` : ''}
                ${compra.EstadoOrden === 'Pagado' ? `
                    <button class="btn btn-sm btn-info" onclick="descargarRecibo(${compra.IdOrden})">
                        <i class="zmdi zmdi-download"></i> Descargar Recibo
                    </button>
                ` : ''}
            </div>
        </div>`;
    });

    $('#comprasContainer').html(html);
}

// Filtrar compras
function filtrarCompras() {
    const filtros = {
        estado: $('#filterEstado').val(),
        fecha_inicio: $('#filterFechaInicio').val(),
        fecha_fin: $('#filterFechaFin').val()
    };

    cargarCompras(filtros);
}

// Ver detalle
function verDetalle(idOrden) {
    const compra = comprasData.find(c => c.IdOrden == idOrden);
    if (!compra) return;

    const estadoClass = compra.EstadoOrden === 'Pagado' ? 'success' : 
                       compra.EstadoOrden === 'por_comprobar' ? 'warning' : 'danger';

    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información del Pedido</h6>
                <table class="table table-sm">
                    <tr><th>Número de Orden:</th><td>#${compra.IdOrden}</td></tr>
                    <tr><th>Fecha:</th><td>${formatearFecha(compra.CreatedDate)}</td></tr>
                    <tr>
                        <th>Estado:</th>
                        <td><span class="badge badge-${estadoClass}">${compra.EstadoOrden}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Producto</h6>
                <div class="text-center mb-3">
                    <img src="${compra.UrlFoto ? baseUrl + 'ui/assets/img/productos/' + compra.UrlFoto : baseUrl + 'ui/assets/img/no-image.png'}" 
                         alt="${compra.NombreProducto}" 
                         style="max-width: 150px; border-radius: 10px;">
                </div>
                <table class="table table-sm">
                    <tr><th>Producto:</th><td>${compra.NombreProducto}</td></tr>
                    <tr><th>Categoría:</th><td>${compra.NombreCategoria || 'N/A'}</td></tr>
                    <tr><th>Cantidad:</th><td>${compra.Cantidad}</td></tr>
                    <tr><th>Precio Unit:</th><td>$${parseFloat(compra.PrecioProducto).toFixed(2)}</td></tr>
                </table>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-12">
                <h6>Resumen</h6>
                <table class="table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">$${(compra.Cantidad * parseFloat(compra.PrecioProducto)).toFixed(2)}</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Total:</th>
                        <th class="text-right">$${(compra.Cantidad * parseFloat(compra.PrecioProducto)).toFixed(2)}</th>
                    </tr>
                </table>
            </div>
        </div>
        
        ${compra.EstadoOrden === 'por_comprobar' ? `
            <div class="alert alert-warning">
                <i class="zmdi zmdi-info"></i> Tu pago está pendiente de verificación. 
                Una vez confirmado, podrás recoger tu pedido.
            </div>
        ` : compra.EstadoOrden === 'Pagado' ? `
            <div class="alert alert-success">
                <i class="zmdi zmdi-check-circle"></i> Pago confirmado. 
                Puedes recoger tu pedido en la tienda.
            </div>
        ` : ''}
    `;

    $('#detalleCompraContent').html(html);
    $('#modalDetalleCompra').modal('show');
}

// Confirmar pago (si el usuario ya pagó por QR)
function confirmarPago(idOrden) {
    Swal.fire({
        title: '¿Confirmar Pago?',
        text: 'Marca esta compra como pagada si ya realizaste el pago',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ya pagué',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí normalmente enviarías una notificación al admin
            Swal.fire({
                icon: 'info',
                title: 'Notificación Enviada',
                text: 'Se ha notificado al administrador para verificar tu pago',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
}

// Descargar recibo
function descargarRecibo(idOrden) {
    window.open(baseUrl + 'ventas/generar_recibo/' + idOrden, '_blank');
}

// Formatear fecha
function formatearFecha(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Inicializar
$(document).ready(function() {
    cargarCompras();
    
    // Event listeners para filtros
    $('#filterEstado, #filterFechaInicio, #filterFechaFin').on('change', function() {
        // Auto-filtrar cuando cambien los selectores
    });
});
</script>
