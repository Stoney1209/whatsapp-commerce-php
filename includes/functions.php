&lt;?php
/**
* Funciones auxiliares del sistema
*/

// Iniciar sesión si no está iniciada
function iniciarSesion() {
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
}

// Verificar si el usuario está autenticado
function estaAutenticado() {
iniciarSesion();
return isset($_SESSION['usuario_id']) &amp;&amp; isset($_SESSION['usuario_username']);
}

// Obtener datos del usuario actual
function usuarioActual() {
iniciarSesion();
if (estaAutenticado()) {
return [
'id' => $_SESSION['usuario_id'],
'username' => $_SESSION['usuario_username'],
'nombre' => $_SESSION['usuario_nombre'],
'rol' => $_SESSION['usuario_rol']
];
}
return null;
}

// Redirigir si no está autenticado
function requiereAuth() {
if (!estaAutenticado()) {
header('Location: /admin/login.php');
exit;
}
}

// Cerrar sesión
function cerrarSesion() {
iniciarSesion();
session_unset();
session_destroy();
header('Location: /admin/login.php');
exit;
}

// Sanitizar entrada de texto
function limpiar($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
return $data;
}

// Generar slug desde texto
function generarSlug($texto) {
// Convertir a minúsculas
$slug = mb_strtolower($texto, 'UTF-8');

// Reemplazar caracteres especiales
$slug = str_replace(
['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
['a', 'e', 'i', 'o', 'u', 'n', 'u'],
$slug
);

// Reemplazar espacios y caracteres no alfanuméricos con guiones
$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

// Eliminar guiones al inicio y final
$slug = trim($slug, '-');

return $slug;
}

// Formatear precio
function formatearPrecio($precio) {
return '$' . number_format($precio, 2, '.', ',');
}

// Subir imagen
function subirImagen($archivo, $carpeta = 'productos') {
$directorioDestino = __DIR__ . '/../uploads/' . $carpeta . '/';

// Crear directorio si no existe
if (!file_exists($directorioDestino)) {
mkdir($directorioDestino, 0777, true);
}

// Validar que sea una imagen
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($archivo['type'], $tiposPermitidos)) {
return ['error' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF y WEBP.'];
}

// Validar tamaño (máximo 5MB)
if ($archivo['size'] > 5 * 1024 * 1024) {
return ['error' => 'El archivo es demasiado grande. Máximo 5MB.'];
}

// Generar nombre único
$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
$nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
$rutaCompleta = $directorioDestino . $nombreArchivo;

// Mover archivo
if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
return ['success' => true, 'filename' => $nombreArchivo, 'path' => '/uploads/' . $carpeta . '/' . $nombreArchivo];
} else {
return ['error' => 'Error al subir el archivo.'];
}
}

// Eliminar imagen
function eliminarImagen($rutaImagen) {
$rutaCompleta = __DIR__ . '/../' . $rutaImagen;
if (file_exists($rutaCompleta)) {
return unlink($rutaCompleta);
}
return false;
}

// Redireccionar con mensaje
function redirigirConMensaje($url, $mensaje, $tipo = 'success') {
iniciarSesion();
$_SESSION['mensaje'] = $mensaje;
$_SESSION['mensaje_tipo'] = $tipo;
header('Location: ' . $url);
exit;
}

// Mostrar mensaje flash
function mostrarMensaje() {
iniciarSesion();
if (isset($_SESSION['mensaje'])) {
$mensaje = $_SESSION['mensaje'];
$tipo = $_SESSION['mensaje_tipo'] ?? 'info';
unset($_SESSION['mensaje']);
unset($_SESSION['mensaje_tipo']);

$colores = [
'success' => 'bg-green-100 border-green-400 text-green-700',
'error' => 'bg-red-100 border-red-400 text-red-700',
'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
'info' => 'bg-blue-100 border-blue-400 text-blue-700'
];

$color = $colores[$tipo] ?? $colores['info'];

echo "&lt;div class='border-l-4 p-4 mb-4 {$color}' role='alert'&gt;";
echo "&lt;p&gt;{$mensaje}&lt;/p&gt;";
echo "&lt;/div&gt;";
}
}

// Validar CSRF token
function generarCSRFToken() {
iniciarSesion();
if (!isset($_SESSION['csrf_token'])) {
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
return $_SESSION['csrf_token'];
}

function validarCSRFToken($token) {
iniciarSesion();
return isset($_SESSION['csrf_token']) &amp;&amp; hash_equals($_SESSION['csrf_token'], $token);
}

// Paginación
function paginar($totalItems, $itemsPorPagina = 10, $paginaActual = 1) {
$totalPaginas = ceil($totalItems / $itemsPorPagina);
$paginaActual = max(1, min($paginaActual, $totalPaginas));
$offset = ($paginaActual - 1) * $itemsPorPagina;

return [
'total_items' => $totalItems,
'items_por_pagina' => $itemsPorPagina,
'pagina_actual' => $paginaActual,
'total_paginas' => $totalPaginas,
'offset' => $offset,
'tiene_anterior' => $paginaActual > 1,
'tiene_siguiente' => $paginaActual < $totalPaginas ]; }