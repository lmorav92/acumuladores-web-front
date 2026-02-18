<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="zmdi zmdi-shopping-cart"></i> Mi Carrito de Compras</h4>
                <button class="btn btn-danger" id="btnVaciarCarrito">
                    <i class="zmdi zmdi-delete"></i> Vaciar Carrito
                </button>
            </div>
            <div class="card-body">
                <!-- Items del Carrito -->
                <div id="carritoItems">
                    <!-- Se cargarán dinámicamente -->
                </div>
                
                <!-- Resumen del Carrito -->
                <div class="row mt-4">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Resumen del Pedido</h5>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="subtotal">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items:</span>
                                    <strong id="totalItems">0</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Total:</h5>
                                    <h5 class="text-primary" id="total">$0.00</h5>
                                </div>
                                <button class="btn btn-primary btn-block" id="btnProcesarCompra">
                                    <i class="zmdi zmdi-check-circle"></i> Procesar Compra
                                </button>
                                <a href="javascript:void(0);" class="btn btn-secondary btn-block menu-link" data-page="tienda">
                                    <i class="zmdi zmdi-arrow-left"></i> Seguir Comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Compra -->
<div class="modal fade" id="modalConfirmarCompra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: 2px solid #D4AF37; border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #D4AF37 0%, #F4E5C2 100%);">
                <h5 class="modal-title" style="color: #1A1A1A;">
                    <i class="zmdi zmdi-check-circle"></i> Confirmar Compra
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="resumenCompra"></div>
                
                <div class="mt-4">
                    <h6>Método de Pago</h6>
                    <div class="form-group">
                        <select class="form-control" id="metodoPago">
                            <option value="qr">Pago por QR</option>
                            <option value="efectivo">Efectivo en tienda</option>
                        </select>
                    </div>
                </div>
                
                <div id="qrPagoContainer" class="text-center mt-3" style="display:none;">
                    <div class="qr-container" style="padding: 20px; background: white; border: 3px solid #D4AF37; border-radius: 10px; display: inline-block;">
                        <img src="" id="qrImage" alt="QR de Pago" style="width:250px; height:250px;">
                    </div>
                    <div class="payment-info mt-3" style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                        <p><strong>Tarjeta:</strong> <span id="cardInfo">**** **** **** 1234</span></p>
                        <p><strong>WhatsApp:</strong> <span id="whatsappInfo">+34 123 456 789</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarPedido">
                    <i class="zmdi zmdi-check"></i> Confirmar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cart-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.cart-item:hover {
    box-shadow: 0 4px 20px rgba(212, 175, 55, 0.2);
    transform: translateY(-2px);
}

.cart-item-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-control button {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #D4AF37;
    background: white;
    color: #D4AF37;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-control button:hover {
    background: #D4AF37;
    color: white;
}

.quantity-control input {
    width: 60px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 5px;
}

.btn-remove-item {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-remove-item:hover {
    background: #c82333;
    transform: scale(1.05);
}

.empty-cart {
    text-align: center;
    padding: 60px 20px;
}

.empty-cart i {
    font-size: 80px;
    color: #ccc;
    margin-bottom: 20px;
}
</style>

<script>
var carritoActual = [];

// Cargar carrito
function cargarCarrito() {
    const carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    carritoActual = carrito;
    
    if (carrito.length === 0) {
        $('#carritoItems').html(`
            <div class="empty-cart">
                <i class="zmdi zmdi-shopping-cart"></i>
                <h4>Tu carrito está vacío</h4>
                <p class="text-muted">Agrega productos para comenzar tu compra</p>
                <a href="javascript:void(0);" class="btn btn-primary menu-link" data-page="tienda">
                    <i class="zmdi zmdi-store"></i> Ir a la Tienda
                </a>
            </div>
        `);
        actualizarResumen();
        return;
    }
    
    let html = '';
    carrito.forEach((item, index) => {
        html += `
        <div class="cart-item" data-index="${index}">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="${item.imagen}" class="cart-item-image" alt="${item.nombre}">
                </div>
                <div class="col-md-3">
                    <h6 class="mb-1">${item.nombre}</h6>
                    <small class="text-muted">${item.categoria}</small>
                </div>
                <div class="col-md-2">
                    <strong class="text-primary">$${parseFloat(item.precio).toFixed(2)}</strong>
                </div>
                <div class="col-md-3">
                    <div class="quantity-control">
                        <button onclick="cambiarCantidad(${index}, -1)">-</button>
                        <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
                               onchange="actualizarCantidad(${index}, this.value)" readonly>
                        <button onclick="cambiarCantidad(${index}, 1)">+</button>
                    </div>
                    <small class="text-muted d-block mt-1">Stock: ${item.stock}</small>
                </div>
                <div class="col-md-2 text-right">
                    <div class="mb-2">
                        <strong>$${(item.precio * item.cantidad).toFixed(2)}</strong>
                    </div>
                    <button class="btn-remove-item" onclick="eliminarItem(${index})">
                        <i class="zmdi zmdi-delete"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>`;
    });
    
    $('#carritoItems').html(html);
    actualizarResumen();
}

// Cambiar cantidad
function cambiarCantidad(index, cambio) {
    const nuevaCantidad = carritoActual[index].cantidad + cambio;
    
    if (nuevaCantidad < 1 || nuevaCantidad > carritoActual[index].stock) {
        Swal.fire('Atención', 'Cantidad no válida', 'warning');
        return;
    }
    
    carritoActual[index].cantidad = nuevaCantidad;
    localStorage.setItem('carrito', JSON.stringify(carritoActual));
    cargarCarrito();
    actualizarContadorCarrito();
}

// Actualizar cantidad directamente
function actualizarCantidad(index, valor) {
    const cantidad = parseInt(valor);
    
    if (cantidad < 1 || cantidad > carritoActual[index].stock) {
        Swal.fire('Atención', 'Cantidad no válida', 'warning');
        cargarCarrito();
        return;
    }
    
    carritoActual[index].cantidad = cantidad;
    localStorage.setItem('carrito', JSON.stringify(carritoActual));
    cargarCarrito();
    actualizarContadorCarrito();
}

// Eliminar item
function eliminarItem(index) {
    Swal.fire({
        title: '¿Eliminar producto?',
        text: '¿Estás seguro de eliminar este producto del carrito?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            carritoActual.splice(index, 1);
            localStorage.setItem('carrito', JSON.stringify(carritoActual));
            cargarCarrito();
            actualizarContadorCarrito();
            Swal.fire('Eliminado', 'Producto eliminado del carrito', 'success');
        }
    });
}

// Actualizar resumen
function actualizarResumen() {
    const total = carritoActual.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const items = carritoActual.reduce((sum, item) => sum + item.cantidad, 0);
    
    $('#subtotal').text('$' + total.toFixed(2));
    $('#total').text('$' + total.toFixed(2));
    $('#totalItems').text(items);
}

// Vaciar carrito
$('#btnVaciarCarrito').on('click', function() {
    if (carritoActual.length === 0) {
        Swal.fire('Atención', 'El carrito ya está vacío', 'info');
        return;
    }
    
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: '¿Estás seguro de eliminar todos los productos?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('carrito');
            carritoActual = [];
            cargarCarrito();
            actualizarContadorCarrito();
            Swal.fire('Carrito vaciado', 'Se eliminaron todos los productos', 'success');
        }
    });
});

// Procesar compra
$('#btnProcesarCompra').on('click', function() {
    if (carritoActual.length === 0) {
        Swal.fire('Atención', 'El carrito está vacío', 'warning');
        return;
    }
    
    // Generar resumen
    let resumen = '<table class="table table-sm">';
    resumen += '<thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
    
    carritoActual.forEach(item => {
        resumen += `
            <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${(item.precio * item.cantidad).toFixed(2)}</td>
            </tr>
        `;
    });
    
    const total = carritoActual.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    resumen += `</tbody><tfoot><tr><th colspan="3">Total</th><th>$${total.toFixed(2)}</th></tr></tfoot></table>`;
    
    $('#resumenCompra').html(resumen);
    $('#modalConfirmarCompra').modal('show');
});

// Cambiar método de pago
$('#metodoPago').on('change', function() {
    if ($(this).val() === 'qr') {
        $('#qrImage').attr('src', baseUrl + 'ui/assets/img/transfermovil_qr.png');
        $('#qrPagoContainer').slideDown();
    } else {
        $('#qrPagoContainer').slideUp();
    }
});

// Confirmar pedido
$('#btnConfirmarPedido').on('click', function() {
    const metodoPago = $('#metodoPago').val();
    
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos
    const pedido = {
        items: carritoActual,
        metodo_pago: metodoPago,
        total: carritoActual.reduce((sum, item) => sum + (item.precio * item.cantidad), 0)
    };
    
    $.ajax({
        url: baseUrl + 'ventas/registrar_pedido_carrito',
        type: 'POST',
        data: { pedido: JSON.stringify(pedido) },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#modalConfirmarCompra').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Pedido Registrado!',
                    html: `
                        <p><strong>Número de Pedido:</strong> #${response.id_pedido}</p>
                        <p><strong>Total:</strong> $${response.total}</p>
                        <p class="text-info mt-3">
                            ${metodoPago === 'qr' ? 
                                'Tu pedido está pendiente de confirmación de pago' : 
                                'Puedes recoger tu pedido en la tienda'}
                        </p>
                    `,
                    confirmButtonColor: '#D4AF37'
                }).then(() => {
                    // Limpiar carrito
                    localStorage.removeItem('carrito');
                    carritoActual = [];
                    actualizarContadorCarrito();
                    
                    // Ir a mis compras
                    loadPage('mis_compras');
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo procesar el pedido', 'error');
        }
    });
});

// Cargar al iniciar
$(document).ready(function() {
    cargarCarrito();
});
</script>
