<?php
/**
 * Controller: Admin Feedback View/Edit
 * Ruta: /admin/feedback/view/:id
 */

// Verificar autenticación
require_admin_auth();

$db = Database::getInstance();

// Obtener ID del feedback
$feedback_id = $_GET['id'] ?? null;

if (!$feedback_id) {
    $_SESSION['error'] = 'ID de feedback no especificado';
    redirect('admin/feedback');
    exit;
}

// Procesar actualización del feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Token CSRF inválido';
        redirect('admin/feedback/view?id=' . $feedback_id);
        exit;
    }

    $status = $_POST['status'] ?? '';
    $admin_notes = trim($_POST['admin_notes'] ?? '');

    // Validar estado
    $valid_statuses = ['new', 'reviewing', 'accepted', 'rejected', 'implemented'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = 'Estado inválido';
    } else {
        // Actualizar feedback
        $update_data = [
            'status' => $status,
            'admin_notes' => $admin_notes,
            'reviewed_by' => $_SESSION['admin_id'],
            'reviewed_at' => date('Y-m-d H:i:s')
        ];

        $result = $db->update('feedback_reports', $update_data, ['id' => $feedback_id]);

        if ($result) {
            // Si se acepta una sugerencia de un beta tester, incrementar contador
            if ($status === 'accepted') {
                $feedback = $db->fetchOne("SELECT beta_tester_id, type FROM feedback_reports WHERE id = ?", [$feedback_id]);
                if ($feedback && $feedback['beta_tester_id'] && $feedback['type'] === 'feature') {
                    $db->query("UPDATE beta_testers SET suggestions_accepted = suggestions_accepted + 1 WHERE id = ?", [$feedback['beta_tester_id']]);

                    // Actualizar nivel de contribución
                    update_contribution_level($feedback['beta_tester_id']);
                }
            }

            $_SESSION['success'] = 'Feedback actualizado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar el feedback';
        }
    }

    redirect('admin/feedback/view?id=' . $feedback_id);
    exit;
}

// Obtener feedback con información relacionada
$feedback = $db->fetchOne("
    SELECT
        fr.*,
        w.title as webapp_title,
        w.url as webapp_url,
        w.slug as webapp_slug,
        bt.name as tester_name,
        bt.email as tester_email,
        bt.contribution_level,
        bt.bugs_reported,
        bt.suggestions_accepted,
        admin.username as reviewed_by_username
    FROM feedback_reports fr
    LEFT JOIN webapps w ON fr.webapp_id = w.id
    LEFT JOIN beta_testers bt ON fr.beta_tester_id = bt.id
    LEFT JOIN admin_users admin ON fr.reviewed_by = admin.id
    WHERE fr.id = ?
", [$feedback_id]);

if (!$feedback) {
    $_SESSION['error'] = 'Feedback no encontrado';
    redirect('admin/feedback');
    exit;
}

// Renderizar vista
render_view('admin/feedback/view', ['feedback' => $feedback]);

/**
 * Actualizar nivel de contribución del beta tester
 */
function update_contribution_level($beta_tester_id) {
    global $db;

    $stats = $db->fetchOne("
        SELECT bugs_reported, suggestions_accepted
        FROM beta_testers
        WHERE id = ?
    ", [$beta_tester_id]);

    if (!$stats) return;

    $total_contributions = $stats['bugs_reported'] + $stats['suggestions_accepted'];

    $new_level = 'bronze';
    if ($total_contributions >= 50) {
        $new_level = 'platinum';
    } elseif ($total_contributions >= 25) {
        $new_level = 'gold';
    } elseif ($total_contributions >= 10) {
        $new_level = 'silver';
    }

    $db->query("UPDATE beta_testers SET contribution_level = ? WHERE id = ?", [$new_level, $beta_tester_id]);
}
