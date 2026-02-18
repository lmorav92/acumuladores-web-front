<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
$role = isset($user['role']) ? $user['role'] : 'usuario';
?>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="zmdi zmdi-shopping-cart"></i> Tienda de Productos</h4>
                <div>
                    <input type="text" id="productoSearch" class="form-control d-inline-block" style="width: 250px;" placeholder="Buscar productos...">
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select id="filterCategoria" class="form-control">
                            <option value="">Todas las Categorías</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterPrecio" class="form-control">
                            <option value="">Todos los Precios</option>
                            <option value="0-20">$0 - $20</option>
                            <option value="20-50">$20 - $50</option>
                            <option value="50-100">$50 - $100</option>
                            <option value="100+">$100+</option>
                        </select>
                    </div>
                </div>

                <!-- Grid de Productos -->
                <div class="row" id="productosContainer">
                    <!-- Los productos se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR de Pago -->
<div class="modal fade" id="modalQRPago" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: 2px solid #D4AF37; border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #D4AF37 0%, #F4E5C2 100%); border-radius: 13px 13px 0 0;">
                <h5 class="modal-title" style="color: #1A1A1A;"><i class="zmdi zmdi-qr-code"></i> Realizar Pago</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="padding: 30px;">
                <div id="productoInfo" class="mb-4">
                    <!-- Información del producto -->
                </div>
                
                <div class="qr-container mb-4" style="padding: 20px; background: white; border: 3px solid #D4AF37; border-radius: 10px; display: inline-block;">
                    <div id="qrcode"></div>
                </div>
                
                <div class="payment-info" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #D4AF37;">
                    <h6 style="color: #D4AF37; margin-bottom: 15px;"><i class="zmdi zmdi-card"></i> Información de Pago</h6>
                    <p class="mb-2" style="color: #D4AF37;"><strong>Tarjeta:</strong> <span id="cardNumber">**** **** **** 1234</span></p>
                    <p class="mb-2" style="color: #D4AF37;"><strong>WhatsApp:</strong> <span id="whatsappNumber">+34 123 456 789</span></p>
                    <p class="mb-0 text-muted small" style="color: #D4AF37;">Escanea el código QR para realizar el pago</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnComprado">
                    <i class="zmdi zmdi-check-circle"></i> Comprado
                </button>
            </div>
        </div>
    </div>
</div>
<a href="#" id="floating-cart" class="menu-link" data-page="carrito">
    🛒 <span id="cart-count">0</span>
</a>

<!-- Incluir librería QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
/* Estilos para la Tienda */
.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    margin-bottom: 30px;
    position: relative;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 35px rgba(212, 175, 55, 0.3);
    border: 2px solid #D4AF37;
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #D4AF37 0%, #F4E5C2 50%, #D4AF37 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.product-card:hover::before {
    transform: scaleX(1);
}

.product-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #D4AF37;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);
}

.product-body {
    padding: 25px;
}

.product-title {
    font-size: 20px;
    font-weight: bold;
    color: #1A1A1A;
    margin-bottom: 10px;
    transition: color 0.3s ease;
}

.product-card:hover .product-title {
    color: #D4AF37;
}

.product-description {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
    line-height: 1.6;
}

.product-price {
    font-size: 28px;
    font-weight: bold;
    color: #D4AF37;
    margin-bottom: 20px;
}

.product-footer {
    display: flex;
    gap: 10px;
}

.btn-qr {
    flex: 1;
    background: linear-gradient(135deg, #D4AF37 0%, #F4E5C2 100%);
    border: none;
    color: #1A1A1A;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
}

.btn-qr:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    background: linear-gradient(135deg, #F4E5C2 0%, #D4AF37 100%);
}

.category-badge {
    display: inline-block;
    background: #f8f9fa;
    color: #666;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
    margin-bottom: 10px;
    border: 1px solid #dee2e6;
}

/* Animación de carga */
.product-skeleton {
    animation: pulse 1.5s ease-in-out infinite;
}

#floating-cart {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 18px;
    border-radius: 50px;
    font-size: 18px;
    text-decoration: none;
    z-index: 9999;
}
#cart-count {
    background: red;
    padding: 3px 8px;
    border-radius: 50%;
    margin-left: 5px;
}


@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Loading overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #D4AF37;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
var baseUrlProductos = typeof baseUrl !== 'undefined' ? baseUrl : '';
var productosData = [];
var productoSeleccionado = null;

// Información de pago (esto normalmente vendría de tu configuración)
const pagoInfo = {
    tarjeta: '**** **** **** 1234',
    whatsapp: '+34 123 456 789',
    nombreTitular: 'Tu Barbería'
};

// Mostrar loading overlay
function showLoading() {
    $('body').append('<div class="loading-overlay"><div class="loading-spinner"></div></div>');
}

function hideLoading() {
    $('.loading-overlay').remove();
}

// Cargar productos
function cargarProductos(filtros = {}) {
    const container = $('#productosContainer');
    
    // Mostrar skeleton loader
    container.html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3">Cargando productos...</p>
        </div>
    `);

    $.ajax({
        url: baseUrlProductos + 'productos/list',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (!data.success || !data.productos) {
                container.html('<div class="col-12 text-center text-danger"><p>No se pudieron cargar los productos.</p></div>');
                return;
            }

            let productosBase = data.productos.filter(p => p.Stock > 0);

            // Aplicar filtros
            if (filtros.categoria) {
                productosBase = productosBase.filter(p => p.NombreCategoria === filtros.categoria);
            }

            if (filtros.busqueda) {
                const b = filtros.busqueda.toLowerCase();
                productosBase = productosBase.filter(p => 
                    p.NombreProducto.toLowerCase().includes(b) || 
                    (p.DescripcionProducto && p.DescripcionProducto.toLowerCase().includes(b))
                );
            }

            if (filtros.precioMin !== undefined) {
                productosBase = productosBase.filter(p => 
                    parseFloat(p.PrecioProducto) >= filtros.precioMin && 
                    parseFloat(p.PrecioProducto) <= filtros.precioMax
                );
            }

            // Guardamos en variable global
            window.productosCargados = productosBase;

            container.empty();

            if (productosBase.length > 0) {
                let htmlAcumulado = '';
                
                productosBase.forEach(producto => {
                    const imgSrc = producto.UrlFoto ? 
                        `${baseUrlProductos}ui/assets/img/productos/${producto.UrlFoto}` : 
                        `${baseUrlProductos}ui/assets/img/no-image.png`;
                    
                    htmlAcumulado += `
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card">
                            ${producto.Stock < 10 ? '<span class="product-badge">¡Últimas unidades!</span>' : ''}
                            <img src="${imgSrc}" alt="${producto.NombreProducto}" class="product-image" onerror="this.src='${baseUrlProductos}ui/assets/img/no-image.png'">
                            <div class="product-body">
                                <span class="category-badge">${producto.NombreCategoria || 'Sin categoría'}</span>
                                <h5 class="product-title">${producto.NombreProducto}</h5>
                                <p class="product-description">${producto.DescripcionProducto || 'Sin descripción'}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="product-price">$${parseFloat(producto.PrecioProducto).toFixed(2)}</div>
                                    <div class="text-muted small">
                                        <i class="zmdi zmdi-storage"></i> Stock: ${producto.Stock}
                                    </div>
                                </div>
                                <div class="product-footer">
								<?php if ($role === 'usuario' || $role === 'Usuario'): ?>
    <button 
        class="btn btn-success btn-sm"
        onclick="agregarAlCarrito(${producto.IdProducto}); return false;">
        🛒 Agregar al carrito
    </button>
<?php endif; ?>


                                    <button class="btn btn-qr btn-sm" onclick="generarQR(${producto.IdProducto})">
                                        <i class="zmdi zmdi-qr-code"></i> Pagar con QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                
                container.append(htmlAcumulado);
            } else {
                container.html(`
                    <div class="col-12 text-center py-5">
                        <i class="zmdi zmdi-search" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">No se encontraron productos con esos criterios</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            container.html('<div class="col-12 text-center text-danger"><p>Error al conectar con el servidor.</p></div>');
        }
    });
}

function agregarAlCarrito(idProducto) {
    $.ajax({
        url: baseUrlProductos + 'carrito/save',
        type: 'POST',
        dataType: 'json',
        data: { idProducto: idProducto }, // Aseguramos la estructura del objeto
        success: function(data) {
            if (data.ok) {
                // Actualizamos todos los contadores que existan por si hay duplicados
                $('[id="cart-count"]').text(data.total);
				localStorage.setItem('carrito', data.total);
                console.log("Nuevo total en el carrito:", data.total);
            } else {
                console.error("Error del servidor:", data.msg);
            }
        },
        error: function(xhr) {
            console.error("Error crítico en la petición AJAX");
        }
    });
}

// Cargar categorías dinámicamente
function cargarCategorias() {
    $.getJSON(baseUrlProductos + 'categorias/list', function(res) {
        if (res.success && res.categorias) {
            let options = '<option value="">Todas las Categorías</option>';
            res.categorias.forEach(c => {
                options += `<option value="${c.NombreCategoria}">${c.NombreCategoria}</option>`;
            });
            $('#filterCategoria').html(options);
        }
    });
}

// Generar QR de pago
function generarQR(productoId) {
    productoSeleccionado = window.productosCargados.find(p => p.IdProducto == productoId);
    
    if (!productoSeleccionado) return;

    $('#qrcode').empty();

    $('#productoInfo').html(`
        <h5 style="color: #1A1A1A; margin-bottom: 10px;">${productoSeleccionado.NombreProducto}</h5>
        <p class="text-muted mb-2">${productoSeleccionado.DescripcionProducto || 'Sin descripción'}</p>
        <h4 style="color: #D4AF37; font-weight: bold;">$${parseFloat(productoSeleccionado.PrecioProducto).toFixed(2)}</h4>
    `);

    $('#cardNumber').text(pagoInfo.tarjeta);
    $('#whatsappNumber').text(pagoInfo.whatsapp);

    // Mostrar imagen del QR fijo o generarlo dinámicamente
    $('#qrcode').html(`
        <img src="${baseUrlProductos}ui/assets/img/transfermovil_qr.png" 
             alt="QR de Pago" 
             style="width:250px; height:250px; object-fit: contain;"
             onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:50px; background:#f0f0f0; border-radius:10px;\'><i class=\'zmdi zmdi-qr-code\' style=\'font-size:100px; color:#D4AF37;\'></i></div>'"/>
    `);

    $('#modalQRPago').modal('show');
}

// Confirmar compra
$('#btnComprado').on('click', function() {
    if (!productoSeleccionado) {
        Swal.fire('Error', 'No hay producto seleccionado', 'error');
        return;
    }

    // Deshabilitar el botón para evitar doble click
    $(this).prop('disabled', true);
    showLoading();

    $.ajax({
        url: baseUrlProductos + 'ventas/registrar_compra_tienda',
        type: 'POST',
        data: {
            IdProducto: productoSeleccionado.IdProducto
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            $('#btnComprado').prop('disabled', false);
            
            if (response.success) {
                $('#modalQRPago').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Compra Registrada!',
                    html: `
                        <p><strong>Producto:</strong> ${response.producto}</p>
                        <p><strong>Precio:</strong> $${parseFloat(response.precio).toFixed(2)}</p>
                        <p class="text-info mt-3"><i class="zmdi zmdi-info"></i> ${response.message}</p>
                    `,
                    confirmButtonColor: '#D4AF37',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    // Recargar productos para actualizar el stock
                    cargarProductos();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#D4AF37'
                });
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            $('#btnComprado').prop('disabled', false);
            
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo procesar la compra. Intente nuevamente.',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
});

// Inicializar
$(document).ready(function() {
    console.log("Iniciando módulo Tienda...");
    
    // Cargar categorías
    cargarCategorias();
    
    // Cargar productos iniciales
    cargarProductos();
    
    // Filtro de búsqueda
    $('#productoSearch').on('keyup', function() {
        const busqueda = $(this).val();
        const categoria = $('#filterCategoria').val();
        const precio = $('#filterPrecio').val();
        
        const filtros = { busqueda, categoria };
        
        if (precio) {
            if (precio === '100+') {
                filtros.precioMin = 100;
                filtros.precioMax = 9999;
            } else {
                const [min, max] = precio.split('-').map(Number);
                filtros.precioMin = min;
                filtros.precioMax = max;
            }
        }
        
        cargarProductos(filtros);
    });
    
    // Filtro de categoría
    $('#filterCategoria').on('change', function() {
        const categoria = $(this).val();
        const busqueda = $('#productoSearch').val();
        const precio = $('#filterPrecio').val();
        
        const filtros = { categoria, busqueda };
        
        if (precio) {
            if (precio === '100+') {
                filtros.precioMin = 100;
                filtros.precioMax = 9999;
            } else {
                const [min, max] = precio.split('-').map(Number);
                filtros.precioMin = min;
                filtros.precioMax = max;
            }
        }
        
        cargarProductos(filtros);
    });
    
    // Filtro de precio
    $('#filterPrecio').on('change', function() {
        const precio = $(this).val();
        const categoria = $('#filterCategoria').val();
        const busqueda = $('#productoSearch').val();
        
        const filtros = { categoria, busqueda };
        
        if (precio) {
            if (precio === '100+') {
                filtros.precioMin = 100;
                filtros.precioMax = 9999;
            } else {
                const [min, max] = precio.split('-').map(Number);
                filtros.precioMin = min;
                filtros.precioMax = max;
            }
        }
        
        cargarProductos(filtros);
    });
});
</script>
