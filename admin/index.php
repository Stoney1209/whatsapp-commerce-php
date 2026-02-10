&lt;?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requiereAuth();

$db = Database::getInstance()->getConnection();

// Obtener estadísticas
$stmt = $db->query("SELECT COUNT(*) as total FROM productos");
$totalProductos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE stock &lt; 5");
$stockBajo = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(precio * stock) as valor FROM productos WHERE activo = 1");
$valorInventario = $stmt->fetch()['valor'] ?? 0;

$stmt = $db->query("SELECT COUNT(*) as total FROM pedidos WHERE estado = 'pendiente'");
$pedidosPendientes = $stmt->fetch()['total'];

// Últimos pedidos
$stmt = $db->query("SELECT * FROM pedidos ORDER BY created_at DESC LIMIT 10");
$ultimosPedidos = $stmt->fetchAll();

$usuario = usuarioActual();
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="es"&gt;
&lt;head&gt;
&lt;meta charset="UTF-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
&lt;title&gt;Dashboard - Panel Admin&lt;/title&gt;
&lt;script src="https://cdn.tailwindcss.com"&gt;&lt;/script&gt;
&lt;link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
rel="stylesheet"&gt;
&lt;style&gt;
body { font-family: 'Inter', sans-serif; }
&lt;/style&gt;
&lt;/head&gt;
&lt;body class="bg-gray-100"&gt;

&lt;!-- Navbar --&gt;
&lt;nav class="bg-slate-900 text-white shadow-lg"&gt;
&lt;div class="container mx-auto px-4 py-4"&gt;
&lt;div class="flex items-center justify-between"&gt;
&lt;h1 class="text-2xl font-bold"&gt;✨ LuxeBeauty Admin&lt;/h1&gt;

&lt;div class="flex items-center gap-6"&gt;
&lt;a href="/" target="_blank" class="hover:text-yellow-400 transition"&gt;
Ver Sitio
&lt;/a&gt;
&lt;span class="text-gray-300"&gt;👤 &lt;?= htmlspecialchars($usuario['nombre']) ?&gt;&lt;/span&gt;
&lt;a href="/admin/logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition"&gt;
Cerrar Sesión
&lt;/a&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/nav&gt;

&lt;div class="container mx-auto px-4 py-8"&gt;
&lt;!-- Menú de navegación --&gt;
&lt;div class="bg-white rounded-lg shadow-md p-4 mb-6"&gt;
&lt;div class="flex flex-wrap gap-4"&gt;
&lt;a href="/admin/index.php" class="bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium"&gt;
📊 Dashboard
&lt;/a&gt;
&lt;a href="/admin/productos.php" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-medium
transition"&gt;
📦 Productos
&lt;/a&gt;
&lt;a href="/admin/categorias.php" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-medium
transition"&gt;
🏷️ Categorías
&lt;/a&gt;
&lt;a href="/admin/pedidos.php" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-medium
transition"&gt;
🛒 Pedidos
&lt;/a&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;?php mostrarMensaje(); ?&gt;

&lt;!-- Estadísticas --&gt;
&lt;div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"&gt;
&lt;div class="bg-white rounded-lg shadow-md p-6"&gt;
&lt;h3 class="text-gray-600 text-sm font-medium mb-2"&gt;Total Productos&lt;/h3&gt;
&lt;p class="text-4xl font-bold text-slate-900"&gt;&lt;?= $totalProductos ?&gt;&lt;/p&gt;
&lt;/div&gt;

&lt;div class="bg-white rounded-lg shadow-md p-6"&gt;
&lt;h3 class="text-gray-600 text-sm font-medium mb-2"&gt;Pedidos Pendientes&lt;/h3&gt;
&lt;p class="text-4xl font-bold text-yellow-600"&gt;&lt;?= $pedidosPendientes ?&gt;&lt;/p&gt;
&lt;/div&gt;

&lt;div class="bg-white rounded-lg shadow-md p-6"&gt;
&lt;h3 class="text-gray-600 text-sm font-medium mb-2"&gt;Alerta Stock&lt;/h3&gt;
&lt;p class="text-4xl font-bold &lt;?= $stockBajo &gt; 0 ? 'text-red-600' : 'text-green-600' ?&gt;"&gt;
&lt;?= $stockBajo ?&gt;
&lt;/p&gt;
&lt;/div&gt;

&lt;div class="bg-white rounded-lg shadow-md p-6"&gt;
&lt;h3 class="text-gray-600 text-sm font-medium mb-2"&gt;Valor Inventario&lt;/h3&gt;
&lt;p class="text-4xl font-bold text-slate-900"&gt;&lt;?= formatearPrecio($valorInventario) ?&gt;&lt;/p&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;!-- Últimos Pedidos --&gt;
&lt;div class="bg-white rounded-lg shadow-md p-6"&gt;
&lt;h2 class="text-2xl font-bold text-slate-900 mb-4"&gt;Últimos Pedidos&lt;/h2&gt;

&lt;?php if (count($ultimosPedidos) &gt; 0): ?&gt;
&lt;div class="overflow-x-auto"&gt;
&lt;table class="w-full"&gt;
&lt;thead&gt;
&lt;tr class="border-b-2 border-gray-200 text-left"&gt;
&lt;th class="p-3"&gt;ID&lt;/th&gt;
&lt;th class="p-3"&gt;Cliente&lt;/th&gt;
&lt;th class="p-3"&gt;Total&lt;/th&gt;
&lt;th class="p-3"&gt;Fecha&lt;/th&gt;
&lt;th class="p-3"&gt;Estado&lt;/th&gt;
&lt;th class="p-3"&gt;Acciones&lt;/th&gt;
&lt;/tr&gt;
&lt;/thead&gt;
&lt;tbody&gt;
&lt;?php foreach ($ultimosPedidos as $pedido): ?&gt;
&lt;tr class="border-b border-gray-100 hover:bg-gray-50"&gt;
&lt;td class="p-3 font-mono text-sm"&gt;#&lt;?= $pedido['id'] ?&gt;&lt;/td&gt;
&lt;td class="p-3"&gt;&lt;?= htmlspecialchars($pedido['cliente_nombre']) ?&gt;&lt;/td&gt;
&lt;td class="p-3 font-semibold"&gt;&lt;?= formatearPrecio($pedido['total']) ?&gt;&lt;/td&gt;
&lt;td class="p-3 text-sm text-gray-600"&gt;
&lt;?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?&gt;
&lt;/td&gt;
&lt;td class="p-3"&gt;
&lt;span class="px-3 py-1 rounded-full text-sm font-medium
&lt;?php
switch($pedido['estado']) {
case 'pendiente':
echo 'bg-yellow-100 text-yellow-800';
break;
case 'procesando':
echo 'bg-blue-100 text-blue-800';
break;
case 'completado':
echo 'bg-green-100 text-green-800';
break;
case 'cancelado':
echo 'bg-red-100 text-red-800';
break;
}
?&gt;"&gt;
&lt;?= ucfirst($pedido['estado']) ?&gt;
&lt;/span&gt;
&lt;/td&gt;
&lt;td class="p-3"&gt;
&lt;a href="/admin/pedidos.php?id=&lt;?= $pedido['id'] ?&gt;"
class="text-blue-600 hover:text-blue-800 text-sm"&gt;
Ver detalles
&lt;/a&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;?php endforeach; ?&gt;
&lt;/tbody&gt;
&lt;/table&gt;
&lt;/div&gt;
&lt;?php else: ?&gt;
&lt;p class="text-gray-500 text-center py-8"&gt;No hay pedidos registrados&lt;/p&gt;
&lt;?php endif; ?&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;/body&gt;
&lt;/html&gt;