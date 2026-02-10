&lt;?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Obtener productos activos
$db = Database::getInstance()->getConnection();

// Filtros
$categoriaFiltro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$busqueda = isset($_GET['buscar']) ? limpiar($_GET['buscar']) : '';
$precioMin = isset($_GET['precio_min']) ? (float)$_GET['precio_min'] : 0;
$precioMax = isset($_GET['precio_max']) ? (float)$_GET['precio_max'] : 999999;

// Construir query
$sql = "SELECT p.*, c.nombre as categoria_nombre
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE p.activo = 1 AND p.stock > 0";

$params = [];

if ($categoriaFiltro > 0) {
$sql .= " AND p.categoria_id = :categoria";
$params[':categoria'] = $categoriaFiltro;
}

if (!empty($busqueda)) {
$sql .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda)";
$params[':busqueda'] = '%' . $busqueda . '%';
}

$sql .= " AND p.precio BETWEEN :precio_min AND :precio_max";
$params[':precio_min'] = $precioMin;
$params[':precio_max'] = $precioMax;

$sql .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

// Obtener categorías para el filtro
$stmtCat = $db->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
$categorias = $stmtCat->fetchAll();
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="es"&gt;
&lt;head&gt;
&lt;meta charset="UTF-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
&lt;title&gt;LuxeBeauty - Cosmética Premium&lt;/title&gt;
&lt;meta name="description" content="Descubre nuestra colección de productos de belleza premium. Maquillaje, skincare,
perfumes y más."&gt;

&lt;!-- Tailwind CSS CDN --&gt;
&lt;script src="https://cdn.tailwindcss.com"&gt;&lt;/script&gt;

&lt;!-- Google Fonts --&gt;
&lt;link rel="preconnect" href="https://fonts.googleapis.com"&gt;
&lt;link rel="preconnect" href="https://fonts.gstatic.com" crossorigin&gt;
&lt;link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
rel="stylesheet"&gt;

&lt;!-- Custom CSS --&gt;
&lt;link rel="stylesheet" href="/assets/css/style.css"&gt;

&lt;style&gt;
body { font-family: 'Inter', sans-serif; }
&lt;/style&gt;
&lt;/head&gt;
&lt;body class="bg-gray-50"&gt;

&lt;!-- Header --&gt;
&lt;header class="bg-slate-900 text-white shadow-lg sticky top-0 z-50"&gt;
&lt;div class="container mx-auto px-4 py-4"&gt;
&lt;div class="flex items-center justify-between"&gt;
&lt;a href="/" class="text-2xl font-bold tracking-tight"&gt;
✨ LuxeBeauty
&lt;/a&gt;

&lt;nav class="hidden md:flex items-center space-x-6"&gt;
&lt;a href="/" class="hover:text-yellow-400 transition"&gt;Inicio&lt;/a&gt;
&lt;a href="#catalogo" class="hover:text-yellow-400 transition"&gt;Catálogo&lt;/a&gt;
&lt;a href="#contacto" class="hover:text-yellow-400 transition"&gt;Contacto&lt;/a&gt;
&lt;/nav&gt;

&lt;button id="cart-btn" class="bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-lg font-medium transition flex
items-center gap-2"&gt;
🛒 Carrito &lt;span id="cart-count" class="bg-white text-slate-900 px-2 py-0.5 rounded-full text-sm"&gt;0&lt;/span&gt;
&lt;/button&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/header&gt;

&lt;!-- Hero Section --&gt;
&lt;section class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white py-20"&gt;
&lt;div class="container mx-auto px-4 text-center"&gt;
&lt;h1 class="text-5xl md:text-6xl font-bold mb-4"&gt;Tu piel, pero mejor.&lt;/h1&gt;
&lt;p class="text-xl md:text-2xl mb-8 text-gray-300"&gt;Cosmética consciente para realzar tu belleza natural&lt;/p&gt;
&lt;a href="#catalogo" class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-8 py-4 rounded-lg text-lg
font-semibold transition transform hover:scale-105"&gt;
Descubrir la Colección
&lt;/a&gt;
&lt;/div&gt;
&lt;/section&gt;

&lt;!-- Catálogo --&gt;
&lt;section id="catalogo" class="py-16"&gt;
&lt;div class="container mx-auto px-4"&gt;
&lt;h2 class="text-4xl font-bold text-center mb-12 text-slate-900"&gt;Nuestro Catálogo&lt;/h2&gt;

&lt;!-- Filtros --&gt;
&lt;div class="bg-white rounded-lg shadow-md p-6 mb-8"&gt;
&lt;form method="GET" action="/" class="grid grid-cols-1 md:grid-cols-4 gap-4"&gt;
&lt;!-- Búsqueda --&gt;
&lt;div&gt;
&lt;label class="block text-sm font-medium text-gray-700 mb-2"&gt;Buscar&lt;/label&gt;
&lt;input type="text" name="buscar" value="&lt;?= htmlspecialchars($busqueda) ?&gt;"
placeholder="Nombre del producto..."
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500
focus:border-transparent"&gt;
&lt;/div&gt;

&lt;!-- Categoría --&gt;
&lt;div&gt;
&lt;label class="block text-sm font-medium text-gray-700 mb-2"&gt;Categoría&lt;/label&gt;
&lt;select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500
focus:border-transparent"&gt;
&lt;option value="0"&gt;Todas las categorías&lt;/option&gt;
&lt;?php foreach ($categorias as $cat): ?&gt;
&lt;option value="&lt;?= $cat['id'] ?&gt;" &lt;?= $categoriaFiltro == $cat['id'] ? 'selected' : '' ?&gt;&gt;
&lt;?= htmlspecialchars($cat['nombre']) ?&gt;
&lt;/option&gt;
&lt;?php endforeach; ?&gt;
&lt;/select&gt;
&lt;/div&gt;

&lt;!-- Precio Mínimo --&gt;
&lt;div&gt;
&lt;label class="block text-sm font-medium text-gray-700 mb-2"&gt;Precio Mínimo&lt;/label&gt;
&lt;input type="number" name="precio_min" value="&lt;?= $precioMin ?&gt;" min="0" step="10"
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500
focus:border-transparent"&gt;
&lt;/div&gt;

&lt;!-- Precio Máximo --&gt;
&lt;div&gt;
&lt;label class="block text-sm font-medium text-gray-700 mb-2"&gt;Precio Máximo&lt;/label&gt;
&lt;input type="number" name="precio_max" value="&lt;?= $precioMax == 999999 ? '' : $precioMax ?&gt;" min="0" step="10"
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500
focus:border-transparent"&gt;
&lt;/div&gt;

&lt;div class="md:col-span-4 flex gap-2"&gt;
&lt;button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-lg hover:bg-slate-800 transition"&gt;
🔍 Filtrar
&lt;/button&gt;
&lt;a href="/" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition"&gt;
Limpiar
&lt;/a&gt;
&lt;/div&gt;
&lt;/form&gt;
&lt;/div&gt;

&lt;!-- Grid de Productos --&gt;
&lt;div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"&gt;
&lt;?php if (count($productos) &gt; 0): ?&gt;
&lt;?php foreach ($productos as $producto): ?&gt;
&lt;div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition transform
hover:-translate-y-1"&gt;
&lt;!-- Imagen --&gt;
&lt;div class="h-64 bg-gray-200 overflow-hidden"&gt;
&lt;?php if ($producto['imagen']): ?&gt;
&lt;img src="/uploads/productos/&lt;?= htmlspecialchars($producto['imagen']) ?&gt;"
alt="&lt;?= htmlspecialchars($producto['nombre']) ?&gt;"
class="w-full h-full object-cover"&gt;
&lt;?php else: ?&gt;
&lt;div class="w-full h-full flex items-center justify-center text-6xl"&gt;
💄
&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;/div&gt;

&lt;!-- Contenido --&gt;
&lt;div class="p-4"&gt;
&lt;span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full mb-2"&gt;
&lt;?= htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría') ?&gt;
&lt;/span&gt;

&lt;h3 class="text-lg font-semibold text-slate-900 mb-2"&gt;
&lt;?= htmlspecialchars($producto['nombre']) ?&gt;
&lt;/h3&gt;

&lt;p class="text-gray-600 text-sm mb-3 line-clamp-2"&gt;
&lt;?= htmlspecialchars(substr($producto['descripcion'], 0, 100)) ?&gt;...
&lt;/p&gt;

&lt;div class="flex items-center justify-between mb-3"&gt;
&lt;span class="text-2xl font-bold text-slate-900"&gt;
&lt;?= formatearPrecio($producto['precio']) ?&gt;
&lt;/span&gt;
&lt;span class="text-sm text-gray-500"&gt;
Stock: &lt;span class="font-semibold &lt;?= $producto['stock'] &lt; 5 ? 'text-red-600' : 'text-green-600' ?&gt;"&gt;
&lt;?= $producto['stock'] ?&gt;
&lt;/span&gt;
&lt;/span&gt;
&lt;/div&gt;

&lt;button onclick="agregarAlCarrito(&lt;?= $producto['id'] ?&gt;, '&lt;?= htmlspecialchars($producto['nombre']) ?&gt;',
&lt;?= $producto['precio'] ?&gt;, &lt;?= $producto['stock'] ?&gt;)"
class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg font-medium transition"&gt;
🛒 Agregar al Carrito
&lt;/button&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;?php endforeach; ?&gt;
&lt;?php else: ?&gt;
&lt;div class="col-span-full text-center py-12"&gt;
&lt;p class="text-2xl text-gray-500"&gt;No se encontraron productos&lt;/p&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/section&gt;

&lt;!-- Contacto --&gt;
&lt;section id="contacto" class="bg-slate-900 text-white py-16"&gt;
&lt;div class="container mx-auto px-4 text-center"&gt;
&lt;h2 class="text-3xl font-bold mb-4"&gt;¿Tienes dudas?&lt;/h2&gt;
&lt;p class="text-xl mb-8"&gt;Contáctanos por WhatsApp y te ayudaremos&lt;/p&gt;
&lt;a href="https://wa.me/5551234567" target="_blank"
class="inline-block bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition
transform hover:scale-105"&gt;
📱 Chatear por WhatsApp
&lt;/a&gt;
&lt;/div&gt;
&lt;/section&gt;

&lt;!-- Footer --&gt;
&lt;footer class="bg-slate-800 text-white py-8"&gt;
&lt;div class="container mx-auto px-4 text-center"&gt;
&lt;p&gt;&amp;copy; 2026 LuxeBeauty. Todos los derechos reservados.&lt;/p&gt;
&lt;p class="text-sm text-gray-400 mt-2"&gt;
&lt;a href="/admin" class="hover:text-yellow-400"&gt;Panel Admin&lt;/a&gt;
&lt;/p&gt;
&lt;/div&gt;
&lt;/footer&gt;

&lt;!-- Modal Carrito --&gt;
&lt;div id="cart-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center
p-4"&gt;
&lt;div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"&gt;
&lt;div class="p-6"&gt;
&lt;div class="flex justify-between items-center mb-4"&gt;
&lt;h3 class="text-2xl font-bold text-slate-900"&gt;Tu Carrito&lt;/h3&gt;
&lt;button onclick="cerrarCarrito()" class="text-gray-500 hover:text-gray-700 text-2xl"&gt;&amp;times;&lt;/button&gt;
&lt;/div&gt;

&lt;div id="cart-items" class="space-y-4 mb-6"&gt;
&lt;!-- Items se cargan dinámicamente --&gt;
&lt;/div&gt;

&lt;div class="border-t pt-4"&gt;
&lt;div class="flex justify-between text-xl font-bold mb-4"&gt;
&lt;span&gt;Total:&lt;/span&gt;
&lt;span id="cart-total"&gt;$0.00&lt;/span&gt;
&lt;/div&gt;

&lt;button onclick="enviarPorWhatsApp()" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg
font-semibold transition"&gt;
📱 Enviar Pedido por WhatsApp
&lt;/button&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;!-- JavaScript --&gt;
&lt;script src="/assets/js/cart.js"&gt;&lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;