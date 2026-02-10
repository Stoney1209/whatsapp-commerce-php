/**
 * Sistema de Carrito de Compras
 * Almacenamiento en localStorage
 */

// Inicializar carrito
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

// Actualizar contador del carrito
function actualizarContador() {
    const contador = document.getElementById('cart-count');
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    contador.textContent = totalItems;
}

// Agregar producto al carrito
function agregarAlCarrito(id, nombre, precio, stockDisponible) {
    // Buscar si ya existe en el carrito
    const itemExistente = carrito.find(item => item.id === id);

    if (itemExistente) {
        // Verificar stock
        if (itemExistente.cantidad >= stockDisponible) {
            alert('No hay suficiente stock disponible');
            return;
        }
        itemExistente.cantidad++;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            stock: stockDisponible
        });
    }

    // Guardar en localStorage
    localStorage.setItem('carrito', JSON.stringify(carrito));

    // Actualizar UI
    actualizarContador();

    // Mostrar notificación
    mostrarNotificacion(`✅ ${nombre} agregado al carrito`);
}

// Eliminar producto del carrito
function eliminarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    localStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarContador();
    renderizarCarrito();
}

// Actualizar cantidad
function actualizarCantidad(id, nuevaCantidad) {
    const item = carrito.find(item => item.id === id);
    if (item) {
        if (nuevaCantidad <= 0) {
            eliminarDelCarrito(id);
            return;
        }

        if (nuevaCantidad > item.stock) {
            alert('No hay suficiente stock disponible');
            return;
        }

        item.cantidad = nuevaCantidad;
        localStorage.setItem('carrito', JSON.stringify(carrito));
        renderizarCarrito();
    }
}

// Renderizar carrito
function renderizarCarrito() {
    const contenedor = document.getElementById('cart-items');
    const totalElement = document.getElementById('cart-total');

    if (carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <p class="text-4xl mb-2">🛒</p>
                <p>Tu carrito está vacío</p>
            </div>
        `;
        totalElement.textContent = '$0.00';
        return;
    }

    let html = '';
    let total = 0;

    carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;

        html += `
            <div class="flex items-center gap-4 border-b pb-4">
                <div class="flex-1">
                    <h4 class="font-semibold text-slate-900">${item.nombre}</h4>
                    <p class="text-sm text-gray-600">$${item.precio.toFixed(2)} c/u</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <button onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})" 
                            class="bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded flex items-center justify-center">
                        -
                    </button>
                    <span class="w-12 text-center font-semibold">${item.cantidad}</span>
                    <button onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})" 
                            class="bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded flex items-center justify-center">
                        +
                    </button>
                </div>
                
                <div class="text-right">
                    <p class="font-bold text-slate-900">$${subtotal.toFixed(2)}</p>
                    <button onclick="eliminarDelCarrito(${item.id})" 
                            class="text-red-600 hover:text-red-700 text-sm">
                        Eliminar
                    </button>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
    totalElement.textContent = '$' + total.toFixed(2);
    actualizarContador();
}

// Abrir modal del carrito
function abrirCarrito() {
    document.getElementById('cart-modal').classList.remove('hidden');
    renderizarCarrito();
}

// Cerrar modal del carrito
function cerrarCarrito() {
    document.getElementById('cart-modal').classList.add('hidden');
}

// Enviar pedido por WhatsApp
function enviarPorWhatsApp() {
    if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    // Construir mensaje
    let mensaje = '*Nuevo Pedido - LuxeBeauty*\n\n';
    mensaje += 'Hola, quisiera realizar el siguiente pedido:\n\n';

    let total = 0;
    carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        mensaje += `• ${item.cantidad} x ${item.nombre} - $${subtotal.toFixed(2)}\n`;
    });

    mensaje += `\n*Total: $${total.toFixed(2)}*\n\n`;
    mensaje += 'Gracias!';

    // Número de WhatsApp (cambiar por el número real)
    const numeroWhatsApp = '5551234567';

    // Crear URL de WhatsApp
    const url = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensaje)}`;

    // Abrir WhatsApp
    window.open(url, '_blank');

    // Opcional: Limpiar carrito después de enviar
    // carrito = [];
    // localStorage.setItem('carrito', JSON.stringify(carrito));
    // cerrarCarrito();
}

// Mostrar notificación
function mostrarNotificacion(mensaje) {
    // Crear elemento de notificación
    const notif = document.createElement('div');
    notif.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
    notif.textContent = mensaje;

    document.body.appendChild(notif);

    // Eliminar después de 3 segundos
    setTimeout(() => {
        notif.classList.add('animate-fade-out');
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function () {
    // Actualizar contador al cargar
    actualizarContador();

    // Botón del carrito
    document.getElementById('cart-btn').addEventListener('click', abrirCarrito);

    // Cerrar modal al hacer click fuera
    document.getElementById('cart-modal').addEventListener('click', function (e) {
        if (e.target === this) {
            cerrarCarrito();
        }
    });
});

// Animaciones CSS (agregar al style.css)
const style = document.createElement('style');
style.textContent = `
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fade-out {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    .animate-fade-out {
        animation: fade-out 0.3s ease-out;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
