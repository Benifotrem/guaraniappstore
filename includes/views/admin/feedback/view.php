<?php
/**
 * Vista: Detalle de Feedback
 * Permite ver y actualizar el estado del feedback
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback #<?php echo $feedback['id']; ?> - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">
    <style>
        .feedback-detail {
            max-width: 1200px;
            margin: 0 auto;
        }

        .feedback-header {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .feedback-main, .feedback-sidebar {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-section {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .feedback-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .feedback-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }

        .feedback-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
        }

        .meta-value {
            font-size: 14px;
            color: #111827;
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

        .severity {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }

        .severity-critical { color: #dc2626; }
        .severity-high { color: #ea580c; }
        .severity-medium { color: #f59e0b; }
        .severity-low { color: #84cc16; }

        .rating-stars {
            color: #fbbf24;
            font-size: 20px;
        }

        .description-box {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .screenshot-preview {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .tester-card {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .tester-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 8px;
            margin-top: 8px;
        }

        .tester-bronze { background: #fef3c7; color: #92400e; }
        .tester-silver { background: #e5e7eb; color: #374151; }
        .tester-gold { background: #fef3c7; color: #92400e; }
        .tester-platinum { background: #e0e7ff; color: #3730a3; }

        .update-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-update {
            background: var(--guarani-primary, #10b981);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }

        .btn-update:hover {
            background: var(--guarani-primary-dark, #059669);
        }

        .btn-back {
            display: inline-block;
            padding: 8px 16px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-back:hover {
            background: #e5e7eb;
        }

        @media (max-width: 768px) {
            .feedback-content {
                grid-template-columns: 1fr;
            }

            .feedback-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="feedback-detail">
            <!-- Header -->
            <div class="feedback-header">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <a href="<?php echo BASE_URL; ?>/admin/feedback" class="btn-back">← Volver a la lista</a>
                    </div>
                    <div>
                        <span style="color: #6b7280; font-size: 14px;">Feedback #<?php echo $feedback['id']; ?></span>
                    </div>
                </div>

                <h1 style="margin: 0; font-size: 24px; color: #111827;">
                    <?php echo htmlspecialchars($feedback['title']); ?>
                </h1>

                <div style="margin-top: 12px; display: flex; gap: 8px;">
                    <span class="badge badge-<?php echo $feedback['type']; ?>">
                        <?php
                        $type_labels = ['bug' => 'Bug', 'feature' => 'Feature', 'review' => 'Review'];
                        echo $type_labels[$feedback['type']];
                        ?>
                    </span>

                    <?php if ($feedback['type'] === 'bug' && $feedback['severity']): ?>
                        <span class="severity severity-<?php echo $feedback['severity']; ?>">
                            ⚠ <?php echo ucfirst($feedback['severity']); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($feedback['type'] === 'review' && $feedback['rating']): ?>
                        <span class="rating-stars">
                            <?php echo str_repeat('★', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="feedback-content">
                <!-- Main Column -->
                <div class="feedback-main">
                    <!-- Webapp Info -->
                    <div class="feedback-section">
                        <h3>Aplicación</h3>
                        <div>
                            <a href="<?php echo get_url('webapp/' . $feedback['webapp_slug']); ?>" target="_blank" style="font-size: 16px; font-weight: 600;">
                                <?php echo htmlspecialchars($feedback['webapp_title']); ?> →
                            </a>
                            <div style="margin-top: 4px;">
                                <a href="<?php echo htmlspecialchars($feedback['webapp_url']); ?>" target="_blank" style="font-size: 12px; color: #6b7280;">
                                    <?php echo htmlspecialchars($feedback['webapp_url']); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="feedback-section">
                        <h3>Descripción</h3>
                        <div class="description-box">
                            <?php echo nl2br(htmlspecialchars($feedback['description'])); ?>
                        </div>
                    </div>

                    <!-- Screenshot -->
                    <?php if ($feedback['screenshot_url']): ?>
                        <div class="feedback-section">
                            <h3>Captura de Pantalla</h3>
                            <a href="<?php echo $feedback['screenshot_url']; ?>" target="_blank">
                                <img src="<?php echo $feedback['screenshot_url']; ?>" alt="Screenshot" class="screenshot-preview">
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Browser Info -->
                    <?php if ($feedback['browser_info']): ?>
                        <div class="feedback-section">
                            <h3>Información Técnica</h3>
                            <div class="meta-item">
                                <span class="meta-label">Navegador</span>
                                <span class="meta-value"><?php echo htmlspecialchars($feedback['browser_info']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Notes -->
                    <?php if ($feedback['admin_notes'] && $feedback['status'] !== 'new'): ?>
                        <div class="feedback-section">
                            <h3>Notas del Administrador</h3>
                            <div class="description-box">
                                <?php echo nl2br(htmlspecialchars($feedback['admin_notes'])); ?>
                            </div>
                            <?php if ($feedback['reviewed_by_username']): ?>
                                <small style="color: #6b7280; display: block; margin-top: 8px;">
                                    Revisado por <?php echo htmlspecialchars($feedback['reviewed_by_username']); ?>
                                    el <?php echo date('d/m/Y H:i', strtotime($feedback['reviewed_at'])); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="feedback-sidebar">
                    <!-- Metadata -->
                    <div class="feedback-section">
                        <h3>Información</h3>
                        <div class="feedback-meta">
                            <div class="meta-item">
                                <span class="meta-label">Estado</span>
                                <span class="meta-value">
                                    <?php
                                    $status_labels = [
                                        'new' => 'Nuevo',
                                        'reviewing' => 'En revisión',
                                        'accepted' => 'Aceptado',
                                        'rejected' => 'Rechazado',
                                        'implemented' => 'Implementado'
                                    ];
                                    echo $status_labels[$feedback['status']];
                                    ?>
                                </span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">Fecha</span>
                                <span class="meta-value"><?php echo date('d/m/Y H:i', strtotime($feedback['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tester Info -->
                    <?php if ($feedback['tester_name']): ?>
                        <div class="feedback-section">
                            <h3>Beta Tester</h3>
                            <div class="tester-card">
                                <div style="font-weight: 600; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($feedback['tester_name']); ?>
                                </div>
                                <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;">
                                    <?php echo htmlspecialchars($feedback['tester_email']); ?>
                                </div>

                                <?php if ($feedback['contribution_level']): ?>
                                    <span class="tester-badge tester-<?php echo $feedback['contribution_level']; ?>">
                                        <?php echo ucfirst($feedback['contribution_level']); ?>
                                    </span>
                                <?php endif; ?>

                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Contribuciones:</div>
                                    <div style="font-size: 13px;">
                                        🐛 <?php echo $feedback['bugs_reported']; ?> bugs reportados<br>
                                        💡 <?php echo $feedback['suggestions_accepted']; ?> sugerencias aceptadas
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="feedback-section">
                            <h3>Usuario</h3>
                            <div style="color: #9ca3af;">Usuario anónimo</div>
                        </div>
                    <?php endif; ?>

                    <!-- Update Form -->
                    <div class="update-form">
                        <h3>Actualizar Feedback</h3>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="form-group">
                                <label for="status">Estado</label>
                                <select id="status" name="status" required>
                                    <option value="new" <?php echo $feedback['status'] === 'new' ? 'selected' : ''; ?>>Nuevo</option>
                                    <option value="reviewing" <?php echo $feedback['status'] === 'reviewing' ? 'selected' : ''; ?>>En revisión</option>
                                    <option value="accepted" <?php echo $feedback['status'] === 'accepted' ? 'selected' : ''; ?>>Aceptado</option>
                                    <option value="rejected" <?php echo $feedback['status'] === 'rejected' ? 'selected' : ''; ?>>Rechazado</option>
                                    <option value="implemented" <?php echo $feedback['status'] === 'implemented' ? 'selected' : ''; ?>>Implementado</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin_notes">Notas del Administrador</label>
                                <textarea id="admin_notes" name="admin_notes" placeholder="Agrega notas sobre este feedback..."><?php echo htmlspecialchars($feedback['admin_notes'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn-update">Actualizar Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
