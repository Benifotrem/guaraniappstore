<?php
/**
 * Controller: Beta Dashboard

// Prevenir caché de la página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

 * Ruta: /beta/dashboard
 * Dashboard personal para beta testers autenticados
 */

// Inicializar base de datos
$db = Database::getInstance();

// Verificar autenticación por sesión o token
$beta_tester = null;

// Prioridad 1: Sesión activa
if (isset($_SESSION['beta_user_id'])) {
    $beta_tester = $db->fetchOne("
        SELECT * FROM beta_testers 
        WHERE id = ? AND status = 'active'
    ", [$_SESSION['beta_user_id']]);
}

// Prioridad 2: Token en URL (para compatibilidad con emails)
if (!$beta_tester && isset($_GET['token'])) {
    $token = $_GET['token'];
    $beta_tester = $db->fetchOne("
        SELECT * FROM beta_testers 
        WHERE access_token = ? AND status = 'active'
    ", [$token]);
    
    // Si encuentra usuario con token, crear sesión
    if ($beta_tester) {
        $_SESSION['beta_user_id'] = $beta_tester['id'];
        $_SESSION['beta_user_name'] = $beta_tester['name'];
        $_SESSION['beta_user_email'] = $beta_tester['email'];
        $_SESSION['beta_token'] = $token;
    }
}

// Si no está autenticado, redirigir al login
if (!$beta_tester) {
    redirect('beta');
}

// Obtener estadísticas del beta tester
$stats = [
    'bugs_reported' => $beta_tester['bugs_reported'] ?? 0,
    'suggestions_accepted' => $beta_tester['suggestions_accepted'] ?? 0,
    'contribution_level' => $beta_tester['contribution_level'] ?? 'bronze',
    'total_contributions' => ($beta_tester['bugs_reported'] ?? 0) + ($beta_tester['suggestions_accepted'] ?? 0)
];

// Obtener posición en el ranking
$ranking_position = $db->fetchOne("
    SELECT COUNT(*) + 1 as position
    FROM beta_testers
    WHERE status = 'active' 
    AND (bugs_reported + suggestions_accepted) > ?
", [$stats['total_contributions']]);
$stats['ranking_position'] = $ranking_position['position'] ?? 0;

// Obtener leaderboard (top 10)
$leaderboard = $db->fetchAll("
    SELECT 
        id,
        name,
        contribution_level,
        bugs_reported,
        suggestions_accepted,
        (bugs_reported + suggestions_accepted) as total
    FROM beta_testers
    WHERE status = 'active'
    ORDER BY total DESC, created_at ASC
    LIMIT 10
");

// Obtener apps disponibles
$available_apps = $db->fetchAll("
    SELECT id, title, slug, short_description, app_url, category, logo_url
    FROM webapps
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 6
");

// Obtener historial de feedback del usuario
$my_feedback = $db->fetchAll("
    SELECT 
        fr.id,
        fr.type,
        fr.title,
        fr.status,
        fr.created_at,
        w.title as webapp_title
    FROM feedback_reports fr
    LEFT JOIN webapps w ON fr.webapp_id = w.id
    WHERE fr.beta_tester_id = ?
    ORDER BY fr.created_at DESC
    LIMIT 10
", [$beta_tester['id']]);

// Variables para la vista (compatibilidad con el dashboard original)
$personal_stats = $stats;
$personal_stats['contribution_level'] = $beta_tester['contribution_level'];
$total_contributions = $stats['total_contributions'];
$leaderboard_position = $stats['ranking_position'];

// Calcular siguiente nivel
$level_thresholds = [
    'bronze' => 10,
    'silver' => 25,
    'gold' => 50,
    'platinum' => 999
];

$current_level = $beta_tester['contribution_level'];
$next_level = null;
if ($current_level === 'bronze') $next_level = 'silver';
elseif ($current_level === 'silver') $next_level = 'gold';
elseif ($current_level === 'gold') $next_level = 'platinum';

$contributions_to_next = $next_level ? $level_thresholds[$next_level] - $total_contributions : 0;

// Renderizar vista
include INCLUDES_PATH . '/views/beta/dashboard.php';
