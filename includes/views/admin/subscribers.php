<?php
/**
 * Vista del panel de administración de suscriptores
 */
?>

<!-- Alertas -->
<div id="alertContainer"></div>

<!-- Título y Acciones -->
<div class="flex justify-between items-center mb-4">
    <div>
        <h2 class="mb-1">Gestión de Suscriptores</h2>
        <p class="text-muted">Administra los suscriptores del blog y envía notificaciones</p>
    </div>
    <div class="flex gap-2">
        <button type="button" class="btn btn-secondary" onclick="exportSubscribers()">
            <i class="fas fa-download"></i> Exportar CSV
        </button>
        <button type="button" class="btn btn-secondary" onclick="refreshData()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
</div>

<!-- Estadísticas -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value"><?= format_number($stats['total']) ?></div>
                <div class="stat-card-label">Total</div>
            </div>
            <div class="stat-card-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value text-success"><?= format_number($stats['active']) ?></div>
                <div class="stat-card-label">Activos</div>
            </div>
            <div class="stat-card-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value" style="color: var(--warning);"><?= format_number($stats['pending']) ?></div>
                <div class="stat-card-label">Pendientes</div>
            </div>
            <div class="stat-card-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value" style="opacity: 0.6;"><?= format_number($stats['unsubscribed']) ?></div>
                <div class="stat-card-label">Desuscritos</div>
            </div>
            <div class="stat-card-icon" style="opacity: 0.6;">
                <i class="fas fa-user-slash"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card mb-4">
    <form method="GET" action="/admin/subscribers" class="grid grid-cols-5 gap-3">
        <div>
            <label class="form-label">Buscar</label>
            <input
                type="text"
                class="form-control"
                name="search"
                placeholder="Email o nombre..."
                value="<?= htmlspecialchars($searchQuery ?? '') ?>"
            >
        </div>

        <div>
            <label class="form-label">Estado</label>
            <select class="form-control" name="status">
                <option value="">Todos</option>
                <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Activos</option>
                <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                <option value="unsubscribed" <?= ($statusFilter ?? '') === 'unsubscribed' ? 'selected' : '' ?>>Desuscritos</option>
            </select>
        </div>

        <div>
            <label class="form-label">Mostrar</label>
            <select class="form-control" name="limit">
                <option value="25" <?= ($limit ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= ($limit ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= ($limit ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                <option value="500" <?= ($limit ?? 25) == 500 ? 'selected' : '' ?>>500</option>
            </select>
        </div>

        <div class="flex items-end gap-2" style="grid-column: span 2;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
            <a href="/admin/subscribers" class="btn btn-secondary">
                <i class="fas fa-times"></i> Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla de Suscriptores -->
<div class="card">
    <div class="card-header flex justify-between items-center">
        <h3 class="mb-0">
            Lista de Suscriptores
            <?php if ($searchQuery || $statusFilter): ?>
                <span class="badge badge-info"><?= count($subscribers) ?> resultados</span>
            <?php endif; ?>
        </h3>

        <?php if (count($subscribers) > 0 && $statusFilter === 'pending'): ?>
            <button class="btn btn-sm btn-success" onclick="bulkApprove()">
                <i class="fas fa-check-double"></i> Aprobar todos los filtrados
            </button>
        <?php endif; ?>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input
                            type="checkbox"
                            id="selectAll"
                            onchange="toggleSelectAll(this)"
                        >
                    </th>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Origen</th>
                    <th>Fecha Suscripción</th>
                    <th>Fecha Verificación</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscribers)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem;">
                            <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                            <span style="color: var(--text-muted);">
                                <?php if ($searchQuery || $statusFilter): ?>
                                    No se encontraron suscriptores con los filtros aplicados
                                <?php else: ?>
                                    No hay suscriptores registrados
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscribers as $subscriber): ?>
                        <tr id="subscriber-row-<?= $subscriber['id'] ?>">
                            <td>
                                <input
                                    type="checkbox"
                                    class="subscriber-checkbox"
                                    value="<?= $subscriber['id'] ?>"
                                >
                            </td>
                            <td><strong><?= e($subscriber['email']) ?></strong></td>
                            <td><?= e($subscriber['name'] ?: '-') ?></td>
                            <td>
                                <?php
                                $statusColors = [
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'unsubscribed' => 'danger'
                                ];
                                $statusLabels = [
                                    'active' => 'Activo',
                                    'pending' => 'Pendiente',
                                    'unsubscribed' => 'Desuscrito'
                                ];
                                $color = $statusColors[$subscriber['status']] ?? 'secondary';
                                $label = $statusLabels[$subscriber['status']] ?? $subscriber['status'];
                                ?>
                                <span class="badge badge-<?= $color ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td>
                                <small style="color: var(--text-muted);">
                                    <?= e($subscriber['subscription_source'] ?: 'landing_page') ?>
                                </small>
                            </td>
                            <td>
                                <small>
                                    <?= format_date_es($subscriber['created_at'], 'short') ?>
                                </small>
                            </td>
                            <td>
                                <small>
                                    <?= $subscriber['verified_at'] ? format_date_es($subscriber['verified_at'], 'short') : '-' ?>
                                </small>
                            </td>
                            <td style="text-align: center;">
                                <div class="flex gap-2 justify-center flex-wrap">
                                    <?php if ($subscriber['status'] === 'pending'): ?>
                                        <button
                                            class="btn btn-sm btn-success"
                                            onclick="approveSubscriber(<?= $subscriber['id'] ?>)"
                                            title="Aprobar suscriptor"
                                        >
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                        <button
                                            class="btn btn-sm btn-info"
                                            onclick="resendVerification(<?= $subscriber['id'] ?>)"
                                            title="Reenviar email de verificación"
                                        >
                                            <i class="fas fa-envelope"></i> Reenviar
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($subscriber['status'] === 'active'): ?>
                                        <span class="badge badge-success" style="padding: 8px 12px;">
                                            <i class="fas fa-check-circle"></i> Verificado
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($subscriber['status'] === 'unsubscribed'): ?>
                                        <button
                                            class="btn btn-sm btn-primary"
                                            onclick="reactivateSubscriber(<?= $subscriber['id'] ?>)"
                                            title="Reactivar suscriptor"
                                        >
                                            <i class="fas fa-redo"></i> Reactivar
                                        </button>
                                    <?php endif; ?>

                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="deleteSubscriber(<?= $subscriber['id'] ?>, '<?= e($subscriber['email']) ?>')"
                                        title="Eliminar suscriptor"
                                    >
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer flex justify-center">
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a class="pagination-link" href="?page=<?= $currentPage - 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                        Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a class="pagination-link <?= $i === $currentPage ? 'active' : '' ?>" href="?page=<?= $i ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a class="pagination-link" href="?page=<?= $currentPage + 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                        Siguiente
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript para funcionalidades AJAX -->
<script>
// Mostrar alertas
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type}" style="margin-bottom: 1rem;">
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alertHtml;

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 5000);

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Aprobar suscriptor
async function approveSubscriber(id) {
    if (!confirm('¿Aprobar este suscriptor manualmente?')) return;

    try {
        console.log('Aprobando suscriptor ID:', id);
        const response = await fetch('/api/subscribers/approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ id })
        });

        console.log('Response status:', response.status);
        const text = await response.text();
        console.log('Response text:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            showAlert('Error: La respuesta del servidor no es válida. Revisa la consola.', 'danger');
            return;
        }

        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    } catch (error) {
        console.error('Error en approveSubscriber:', error);
        showAlert('Error al aprobar suscriptor: ' + error.message, 'danger');
    }
}

// Eliminar suscriptor
async function deleteSubscriber(id, email) {
    if (!confirm(`¿Eliminar permanentemente a ${email}?`)) return;

    try {
        const response = await fetch('/api/subscribers/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ id })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById(`subscriber-row-${id}`).remove();
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        showAlert('Error al eliminar suscriptor', 'danger');
    }
}

// Reenviar email de verificación
async function resendVerification(id) {
    try {
        console.log('Reenviando verificación para ID:', id);
        const response = await fetch('/api/subscribers/resend-verification', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ id })
        });

        console.log('Response status:', response.status);
        const text = await response.text();
        console.log('Response text:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            showAlert('Error: La respuesta del servidor no es válida. Revisa la consola.', 'danger');
            return;
        }

        if (data.success) {
            showAlert(data.message, 'success');
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    } catch (error) {
        console.error('Error en resendVerification:', error);
        showAlert('Error al reenviar verificación: ' + error.message, 'danger');
    }
}

// Reactivar suscriptor
async function reactivateSubscriber(id) {
    if (!confirm('¿Reactivar este suscriptor?')) return;

    try {
        const response = await fetch('/api/subscribers/reactivate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ id })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        showAlert('Error al reactivar suscriptor', 'danger');
    }
}

// Exportar a CSV
async function exportSubscribers() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '/api/subscribers/export?' + params.toString();
}

// Refrescar datos
function refreshData() {
    location.reload();
}

// Seleccionar/deseleccionar todos
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.subscriber-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

// Aprobar en lote
async function bulkApprove() {
    const selected = Array.from(document.querySelectorAll('.subscriber-checkbox:checked'))
        .map(cb => parseInt(cb.value));

    if (selected.length === 0) {
        showAlert('Selecciona al menos un suscriptor', 'warning');
        return;
    }

    if (!confirm(`¿Aprobar ${selected.length} suscriptor(es)?`)) return;

    try {
        const response = await fetch('/api/subscribers/bulk-approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ ids: selected })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        showAlert('Error al aprobar suscriptores', 'danger');
    }
}
</script>
