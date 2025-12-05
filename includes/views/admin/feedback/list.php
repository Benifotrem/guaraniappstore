<?php
/**
 * Vista: Lista de Feedback Reports
 * Panel de administración para gestionar feedback
 */

// Obtener filtros
$filter_type = $_GET['type'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_webapp = $_GET['webapp'] ?? '';

// Construir query
$where = [];
$params = [];

if ($filter_type) {
    $where[] = "fr.type = ?";
    $params[] = $filter_type;
}

if ($filter_status) {
    $where[] = "fr.status = ?";
    $params[] = $filter_status;
}

if ($filter_webapp) {
    $where[] = "fr.webapp_id = ?";
    $params[] = $filter_webapp;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Obtener feedback con información relacionada
$feedbacks = $db->fetchAll("
    SELECT
        fr.*,
        w.title as webapp_title,
        w.url as webapp_url,
        bt.name as tester_name,
        bt.email as tester_email,
        bt.contribution_level
    FROM feedback_reports fr
    LEFT JOIN webapps w ON fr.webapp_id = w.id
    LEFT JOIN beta_testers bt ON fr.beta_tester_id = bt.id
    $where_clause
    ORDER BY
        CASE fr.status
            WHEN 'new' THEN 1
            WHEN 'reviewing' THEN 2
            WHEN 'accepted' THEN 3
            WHEN 'implemented' THEN 4
            WHEN 'rejected' THEN 5
        END,
        fr.created_at DESC
", $params);

// Obtener estadísticas generales
$stats = $db->fetchOne("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN status = 'reviewing' THEN 1 ELSE 0 END) as reviewing_count,
        SUM(CASE WHEN type = 'bug' THEN 1 ELSE 0 END) as bugs_count,
        SUM(CASE WHEN type = 'feature' THEN 1 ELSE 0 END) as features_count,
        SUM(CASE WHEN type = 'review' THEN 1 ELSE 0 END) as reviews_count,
        AVG(CASE WHEN type = 'review' THEN rating END) as avg_rating
    FROM feedback_reports
");

// Obtener lista de webapps para filtro
$webapps = $db->fetchAll("SELECT id, title FROM webapps ORDER BY title");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Feedback - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">
    <style>
        .feedback-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filters select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .feedback-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .feedback-table th {
            background: #f9fafb;
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        .feedback-table td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .feedback-table tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-bug { background: #fee2e2; color: #991b1b; }
        .badge-feature { background: #dbeafe; color: #1e40af; }
        .badge-review { background: #fef3c7; color: #92400e; }

        .badge-new { background: #dbeafe; color: #1e40af; }
        .badge-reviewing { background: #fef3c7; color: #92400e; }
        .badge-accepted { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-implemented { background: #e0e7ff; color: #3730a3; }

        .severity {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .severity-critical { color: #dc2626; }
        .severity-high { color: #ea580c; }
        .severity-medium { color: #f59e0b; }
        .severity-low { color: #84cc16; }

        .rating-stars {
            color: #fbbf24;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-view {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-view:hover {
            background: #e5e7eb;
        }

        .tester-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 8px;
        }

        .tester-bronze { background: #fef3c7; color: #92400e; }
        .tester-silver { background: #e5e7eb; color: #374151; }
        .tester-gold { background: #fef3c7; color: #92400e; }
        .tester-platinum { background: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Gestión de Feedback</h1>

        <!-- Estadísticas -->
        <div class="feedback-stats">
            <div class="stat-card">
                <h3>Total Feedback</h3>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Pendientes</h3>
                <div class="stat-value" style="color: #3b82f6;"><?php echo $stats['new_count']; ?></div>
            </div>
            <div class="stat-card">
                <h3>En Revisión</h3>
                <div class="stat-value" style="color: #f59e0b;"><?php echo $stats['reviewing_count']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Bugs Reportados</h3>
                <div class="stat-value" style="color: #ef4444;"><?php echo $stats['bugs_count']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Features Solicitadas</h3>
                <div class="stat-value" style="color: #3b82f6;"><?php echo $stats['features_count']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Calificación Promedio</h3>
                <div class="stat-value" style="color: #fbbf24;">
                    <?php echo $stats['avg_rating'] ? number_format($stats['avg_rating'], 1) . '/5' : 'N/A'; ?>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
                <select name="type" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="bug" <?php echo $filter_type === 'bug' ? 'selected' : ''; ?>>Bugs</option>
                    <option value="feature" <?php echo $filter_type === 'feature' ? 'selected' : ''; ?>>Features</option>
                    <option value="review" <?php echo $filter_type === 'review' ? 'selected' : ''; ?>>Reviews</option>
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="new" <?php echo $filter_status === 'new' ? 'selected' : ''; ?>>Nuevos</option>
                    <option value="reviewing" <?php echo $filter_status === 'reviewing' ? 'selected' : ''; ?>>En revisión</option>
                    <option value="accepted" <?php echo $filter_status === 'accepted' ? 'selected' : ''; ?>>Aceptados</option>
                    <option value="implemented" <?php echo $filter_status === 'implemented' ? 'selected' : ''; ?>>Implementados</option>
                    <option value="rejected" <?php echo $filter_status === 'rejected' ? 'selected' : ''; ?>>Rechazados</option>
                </select>

                <select name="webapp" onchange="this.form.submit()">
                    <option value="">Todas las apps</option>
                    <?php foreach ($webapps as $webapp): ?>
                        <option value="<?php echo $webapp['id']; ?>" <?php echo $filter_webapp == $webapp['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($webapp['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($filter_type || $filter_status || $filter_webapp): ?>
                    <a href="?" class="btn-sm btn-view">Limpiar filtros</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabla de feedback -->
        <div class="feedback-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Webapp</th>
                        <th>Tester</th>
                        <th>Detalles</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #6b7280;">
                                No hay feedback que mostrar
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><strong>#<?php echo $feedback['id']; ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php echo $feedback['type']; ?>">
                                        <?php
                                        $type_labels = ['bug' => 'Bug', 'feature' => 'Feature', 'review' => 'Review'];
                                        echo $type_labels[$feedback['type']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($feedback['title']); ?></strong>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($feedback['webapp_url']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($feedback['webapp_title']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($feedback['tester_name']): ?>
                                        <div><?php echo htmlspecialchars($feedback['tester_name']); ?></div>
                                        <small style="color: #6b7280;"><?php echo htmlspecialchars($feedback['tester_email']); ?></small>
                                        <?php if ($feedback['contribution_level']): ?>
                                            <br><span class="tester-badge tester-<?php echo $feedback['contribution_level']; ?>">
                                                <?php echo ucfirst($feedback['contribution_level']); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #9ca3af;">Anónimo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($feedback['type'] === 'bug' && $feedback['severity']): ?>
                                        <span class="severity severity-<?php echo $feedback['severity']; ?>">
                                            ⚠ <?php echo ucfirst($feedback['severity']); ?>
                                        </span>
                                    <?php elseif ($feedback['type'] === 'review' && $feedback['rating']): ?>
                                        <span class="rating-stars">
                                            <?php echo str_repeat('★', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']); ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $feedback['status']; ?>">
                                        <?php
                                        $status_labels = [
                                            'new' => 'Nuevo',
                                            'reviewing' => 'Revisando',
                                            'accepted' => 'Aceptado',
                                            'rejected' => 'Rechazado',
                                            'implemented' => 'Implementado'
                                        ];
                                        echo $status_labels[$feedback['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?php echo date('d/m/Y H:i', strtotime($feedback['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo BASE_URL; ?>/admin/feedback/view/<?php echo $feedback['id']; ?>"
                                           class="btn-sm btn-view">Ver</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
