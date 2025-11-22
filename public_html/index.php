<?php
/**
 * ================================================
 * GUARANI APP STORE - INDEX FILE
 * ================================================
 * Punto de entrada principal de la aplicación
 */

// Definir constante de aplicación cargada
define('APP_LOADED', true);

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/config.php';

// Cargar funciones auxiliares
require_once INCLUDES_PATH . '/helpers/functions.php';

// Obtener la ruta solicitada
$route = $_GET['route'] ?? '';
$route = trim($route, '/');

// Buscar controlador según la ruta
global $APP_ROUTES;

// Inicializar variable
$controller_name = null;

// Verificar rutas dinámicas (ej: blog/article/slug)
$route_parts = explode("/", $route);

// Ruta: blog/article/slug
if (count($route_parts) >= 3 && $route_parts[0] === "blog" && $route_parts[1] === "article") {
    $_GET["slug"] = $route_parts[2];
    $controller_name = "blog_article";
}

// Si no se encontró una ruta dinámica, buscar en rutas estáticas
if ($controller_name === null) {
    if (array_key_exists($route, $APP_ROUTES)) {
        $controller_name = $APP_ROUTES[$route];
    } else {
        // Ruta no encontrada
        $controller_name = '404';
    }
}

// Cargar el controlador
$controller_file = INCLUDES_PATH . '/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
} else {
    // Si no existe el controlador, mostrar 404
    http_response_code(404);
    require_once INCLUDES_PATH . '/controllers/404.php';
}
