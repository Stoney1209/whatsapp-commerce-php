&lt;?php
require_once '../config/database.php';
require_once '../includes/functions.php';

iniciarSesion();

// Si ya está autenticado, redirigir al dashboard
if (estaAutenticado()) {
header('Location: /admin/index.php');
exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$username = limpiar($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
$error = 'Por favor completa todos los campos';
} else {
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM usuarios WHERE username = :username AND activo = 1");
$stmt->execute([':username' => $username]);
$usuario = $stmt->fetch();

if ($usuario &amp;&amp; password_verify($password, $usuario['password'])) {
// Login exitoso
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_username'] = $usuario['username'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_rol'] = $usuario['rol'];

header('Location: /admin/index.php');
exit;
} else {
$error = 'Usuario o contraseña incorrectos';
}
}
}
?&gt;
&lt;!DOCTYPE html&gt;
&lt;html lang="es"&gt;
&lt;head&gt;
&lt;meta charset="UTF-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
&lt;title&gt;Login - Panel Admin&lt;/title&gt;
&lt;script src="https://cdn.tailwindcss.com"&gt;&lt;/script&gt;
&lt;link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
rel="stylesheet"&gt;
&lt;style&gt;
body { font-family: 'Inter', sans-serif; }
&lt;/style&gt;
&lt;/head&gt;
&lt;body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center
justify-center p-4"&gt;

&lt;div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md"&gt;
&lt;div class="text-center mb-8"&gt;
&lt;h1 class="text-3xl font-bold text-slate-900 mb-2"&gt;Panel de Administración&lt;/h1&gt;
&lt;p class="text-gray-600"&gt;LuxeBeauty&lt;/p&gt;
&lt;/div&gt;

&lt;?php if ($error): ?&gt;
&lt;div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded"&gt;
&lt;p&gt;&lt;?= htmlspecialchars($error) ?&gt;&lt;/p&gt;
&lt;/div&gt;
&lt;?php endif; ?&gt;

&lt;form method="POST" action="" class="space-y-6"&gt;
&lt;div&gt;
&lt;label for="username" class="block text-sm font-medium text-gray-700 mb-2"&gt;
Usuario
&lt;/label&gt;
&lt;input type="text" id="username" name="username" required
class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
placeholder="admin"&gt;
&lt;/div&gt;

&lt;div&gt;
&lt;label for="password" class="block text-sm font-medium text-gray-700 mb-2"&gt;
Contraseña
&lt;/label&gt;
&lt;input type="password" id="password" name="password" required
class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
placeholder="••••••••"&gt;
&lt;/div&gt;

&lt;button type="submit"
class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg font-semibold transition transform
hover:scale-105"&gt;
Iniciar Sesión
&lt;/button&gt;
&lt;/form&gt;

&lt;div class="mt-6 text-center text-sm text-gray-600"&gt;
&lt;p&gt;Credenciales por defecto:&lt;/p&gt;
&lt;p class="font-mono bg-gray-100 p-2 rounded mt-2"&gt;
&lt;strong&gt;admin&lt;/strong&gt; / password123
&lt;/p&gt;
&lt;/div&gt;

&lt;div class="mt-6 text-center"&gt;
&lt;a href="/" class="text-yellow-600 hover:text-yellow-700 text-sm"&gt;
← Volver al sitio
&lt;/a&gt;
&lt;/div&gt;
&lt;/div&gt;

&lt;/body&gt;
&lt;/html&gt;