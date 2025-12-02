<?php
/**
 * API Controller para gestión de suscriptores
 *
 * Endpoints:
 * - POST /api/subscribers/approve - Aprobar suscriptor manualmente
 * - POST /api/subscribers/bulk-approve - Aprobar múltiples suscriptores
 * - POST /api/subscribers/delete - Eliminar suscriptor
 * - POST /api/subscribers/resend-verification - Reenviar email de verificación
 * - POST /api/subscribers/reactivate - Reactivar suscriptor
 * - GET  /api/subscribers/export - Exportar suscriptores a CSV
 */

// Verificar autenticación de admin
require_admin_auth();

$db = Database::getInstance();

// Obtener la acción del path
$path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '';
$action = basename($path);

// Helper para responder JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Helper para obtener datos JSON del body
function getJsonInput() {
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? [];
}

// ==================================================
// APROBAR SUSCRIPTOR MANUALMENTE
// ==================================================
if ($action === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(false, 'ID de suscriptor requerido');
    }

    // Obtener suscriptor
    $subscriber = $db->fetchOne(
        "SELECT * FROM blog_subscribers WHERE id = ?",
        [$id]
    );

    if (!$subscriber) {
        jsonResponse(false, 'Suscriptor no encontrado');
    }

    if ($subscriber['status'] === 'active') {
        jsonResponse(false, 'El suscriptor ya está activo');
    }

    // Actualizar a activo
    $updated = $db->update('blog_subscribers', [
        'status' => 'active',
        'verified_at' => date('Y-m-d H:i:s'),
        'verification_token' => null
    ], 'id = ?', [$id]);

    if ($updated) {
        // Enviar email de bienvenida
        if (EMAIL_ENABLED && BREVO_API_KEY) {
            require_once INCLUDES_PATH . '/classes/BrevoMailer.php';
            $mailer = new BrevoMailer(BREVO_API_KEY, EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);
            $result = $mailer->sendWelcomeEmail($subscriber['email'], $subscriber['name']);

            if (!$result['success']) {
                error_log("Failed to send welcome email to {$subscriber['email']}: {$result['message']}");
            }
        }

        jsonResponse(true, 'Suscriptor aprobado exitosamente');
    } else {
        jsonResponse(false, 'Error al aprobar suscriptor');
    }
}

// ==================================================
// APROBAR MÚLTIPLES SUSCRIPTORES
// ==================================================
if ($action === 'bulk-approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $ids = $input['ids'] ?? [];

    if (empty($ids) || !is_array($ids)) {
        jsonResponse(false, 'IDs de suscriptores requeridos');
    }

    // Validar que todos sean números
    $ids = array_filter($ids, 'is_numeric');
    $ids = array_map('intval', $ids);

    if (empty($ids)) {
        jsonResponse(false, 'IDs inválidos');
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Actualizar todos
    $updated = $db->query(
        "UPDATE blog_subscribers
         SET status = 'active',
             verified_at = NOW(),
             verification_token = NULL
         WHERE id IN ($placeholders) AND status = 'pending'",
        $ids
    );

    // Enviar emails de bienvenida
    if (EMAIL_ENABLED && BREVO_API_KEY) {
        $subscribers = $db->fetchAll(
            "SELECT email, name FROM blog_subscribers WHERE id IN ($placeholders)",
            $ids
        );

        require_once INCLUDES_PATH . '/classes/BrevoMailer.php';
        $mailer = new BrevoMailer(BREVO_API_KEY, EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);

        foreach ($subscribers as $sub) {
            $result = $mailer->sendWelcomeEmail($sub['email'], $sub['name']);
            if (!$result['success']) {
                error_log("Failed to send welcome email to {$sub['email']}: {$result['message']}");
            }
        }
    }

    jsonResponse(true, "Se aprobaron {$updated} suscriptor(es) exitosamente");
}

// ==================================================
// ELIMINAR SUSCRIPTOR
// ==================================================
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(false, 'ID de suscriptor requerido');
    }

    $deleted = $db->delete('blog_subscribers', 'id = ?', [$id]);

    if ($deleted) {
        jsonResponse(true, 'Suscriptor eliminado exitosamente');
    } else {
        jsonResponse(false, 'Error al eliminar suscriptor');
    }
}

// ==================================================
// REENVIAR EMAIL DE VERIFICACIÓN
// ==================================================
if ($action === 'resend-verification' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(false, 'ID de suscriptor requerido');
    }

    // Obtener suscriptor
    $subscriber = $db->fetchOne(
        "SELECT * FROM blog_subscribers WHERE id = ?",
        [$id]
    );

    if (!$subscriber) {
        jsonResponse(false, 'Suscriptor no encontrado');
    }

    if ($subscriber['status'] !== 'pending') {
        jsonResponse(false, 'El suscriptor no está pendiente de verificación');
    }

    // Generar nuevo token si no existe
    if (!$subscriber['verification_token']) {
        $token = bin2hex(random_bytes(32));
        $db->update('blog_subscribers', [
            'verification_token' => $token
        ], 'id = ?', [$id]);
        $subscriber['verification_token'] = $token;
    }

    // Enviar email
    if (!EMAIL_ENABLED || !BREVO_API_KEY) {
        jsonResponse(false, 'El envío de emails no está habilitado. Configure BREVO_API_KEY en config.php');
    }

    require_once INCLUDES_PATH . '/classes/BrevoMailer.php';
    $mailer = new BrevoMailer(BREVO_API_KEY, EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);
    $result = $mailer->sendVerificationEmail(
        $subscriber['email'],
        $subscriber['name'],
        $subscriber['verification_token']
    );

    if ($result['success']) {
        jsonResponse(true, 'Email de verificación enviado exitosamente');
    } else {
        error_log("Brevo API Error: " . json_encode($result));
        jsonResponse(false, 'Error al enviar email: ' . $result['message']);
    }
}

// ==================================================
// REACTIVAR SUSCRIPTOR
// ==================================================
if ($action === 'reactivate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(false, 'ID de suscriptor requerido');
    }

    $subscriber = $db->fetchOne(
        "SELECT * FROM blog_subscribers WHERE id = ?",
        [$id]
    );

    if (!$subscriber) {
        jsonResponse(false, 'Suscriptor no encontrado');
    }

    if ($subscriber['status'] !== 'unsubscribed') {
        jsonResponse(false, 'El suscriptor no está desuscrito');
    }

    $updated = $db->update('blog_subscribers', [
        'status' => 'active',
        'unsubscribed_at' => null,
        'verified_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);

    if ($updated) {
        jsonResponse(true, 'Suscriptor reactivado exitosamente');
    } else {
        jsonResponse(false, 'Error al reactivar suscriptor');
    }
}

// ==================================================
// EXPORTAR A CSV
// ==================================================
if ($action === 'export' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener filtros de la query string
    $searchQuery = $_GET['search'] ?? '';
    $statusFilter = $_GET['status'] ?? '';

    // Construir query
    $sql = "SELECT
                email,
                name,
                status,
                subscription_source,
                created_at,
                verified_at,
                unsubscribed_at
            FROM blog_subscribers
            WHERE 1=1";

    $params = [];

    if ($searchQuery) {
        $sql .= " AND (email LIKE ? OR name LIKE ?)";
        $searchTerm = "%{$searchQuery}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if ($statusFilter) {
        $sql .= " AND status = ?";
        $params[] = $statusFilter;
    }

    $sql .= " ORDER BY created_at DESC";

    $subscribers = $db->fetchAll($sql, $params);

    // Generar CSV
    $filename = 'subscribers_' . date('Y-m-d_His') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Encabezados
    fputcsv($output, [
        'Email',
        'Nombre',
        'Estado',
        'Origen',
        'Fecha Suscripción',
        'Fecha Verificación',
        'Fecha Desuscripción'
    ]);

    // Datos
    foreach ($subscribers as $sub) {
        fputcsv($output, [
            $sub['email'],
            $sub['name'] ?? '',
            $sub['status'],
            $sub['subscription_source'] ?? 'landing_page',
            $sub['created_at'],
            $sub['verified_at'] ?? '',
            $sub['unsubscribed_at'] ?? ''
        ]);
    }

    fclose($output);
    exit;
}

// Si llegamos aquí, la acción no es válida
jsonResponse(false, 'Acción no válida: ' . $action);
