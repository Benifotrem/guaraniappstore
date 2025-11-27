<?php
/**
 * Vista del panel de administración de suscriptores
 *
 * Variables disponibles:
 * - $stats: estadísticas de suscriptores
 * - $subscribers: lista de suscriptores
 * - $currentPage: página actual
 * - $totalPages: total de páginas
 * - $searchQuery: término de búsqueda
 * - $statusFilter: filtro de estado
 */
include __DIR__ . '/layout/header.php';
?>

<!-- Título y Acciones Globales -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2">Gestión de Suscriptores</h1>
        <p class="text-muted mb-0">
            Administra los suscriptores del blog y envía notificaciones
        </p>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary" onclick="exportSubscribers()">
            <i class="fas fa-download"></i> Exportar CSV
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="refreshData()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
</div>

<!-- Alertas -->
<div id="alertContainer"></div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-start">
                        <h6 class="text-muted mb-1">Total</h6>
                        <h3 class="mb-0"><?= number_format($stats['total']) ?></h3>
                    </div>
                    <div class="text-primary" style="font-size: 2rem;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-start">
                        <h6 class="text-muted mb-1">Activos</h6>
                        <h3 class="mb-0 text-success"><?= number_format($stats['active']) ?></h3>
                    </div>
                    <div class="text-success" style="font-size: 2rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-start">
                        <h6 class="text-muted mb-1">Pendientes</h6>
                        <h3 class="mb-0 text-warning"><?= number_format($stats['pending']) ?></h3>
                    </div>
                    <div class="text-warning" style="font-size: 2rem;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-start">
                        <h6 class="text-muted mb-1">Desuscritos</h6>
                        <h3 class="mb-0 text-secondary"><?= number_format($stats['unsubscribed']) ?></h3>
                    </div>
                    <div class="text-secondary" style="font-size: 2rem;">
                        <i class="fas fa-user-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/subscribers" class="row g-3" id="filterForm">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input
                    type="text"
                    class="form-control"
                    id="search"
                    name="search"
                    placeholder="Email o nombre..."
                    value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                >
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Activos</option>
                    <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="unsubscribed" <?= ($statusFilter ?? '') === 'unsubscribed' ? 'selected' : '' ?>>Desuscritos</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="limit" class="form-label">Mostrar</label>
                <select class="form-select" id="limit" name="limit">
                    <option value="25" <?= ($limit ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($limit ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($limit ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                    <option value="500" <?= ($limit ?? 25) == 500 ? 'selected' : '' ?>>500</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="/admin/subscribers" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Suscriptores -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Lista de Suscriptores
                <?php if ($searchQuery || $statusFilter): ?>
                    <span class="badge bg-info"><?= count($subscribers) ?> resultados</span>
                <?php endif; ?>
            </h5>

            <?php if (count($subscribers) > 0 && $statusFilter === 'pending'): ?>
                <button class="btn btn-sm btn-success" onclick="bulkApprove()">
                    <i class="fas fa-check-double"></i> Aprobar todos los filtrados
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">
                            <input
                                type="checkbox"
                                class="form-check-input"
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
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <?php if ($searchQuery || $statusFilter): ?>
                                    No se encontraron suscriptores con los filtros aplicados
                                <?php else: ?>
                                    No hay suscriptores registrados
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subscribers as $subscriber): ?>
                            <tr id="subscriber-row-<?= $subscriber['id'] ?>">
                                <td class="ps-4">
                                    <input
                                        type="checkbox"
                                        class="form-check-input subscriber-checkbox"
                                        value="<?= $subscriber['id'] ?>"
                                    >
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($subscriber['email']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($subscriber['name'] ?: '-') ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'unsubscribed' => 'secondary'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Activo',
                                        'pending' => 'Pendiente',
                                        'unsubscribed' => 'Desuscrito'
                                    ];
                                    $color = $statusColors[$subscriber['status']] ?? 'secondary';
                                    $label = $statusLabels[$subscriber['status']] ?? $subscriber['status'];
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($subscriber['subscription_source'] ?: 'landing_page') ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <?= date('d/m/Y H:i', strtotime($subscriber['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <?= $subscriber['verified_at'] ? date('d/m/Y H:i', strtotime($subscriber['verified_at'])) : '-' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if ($subscriber['status'] === 'pending'): ?>
                                            <button
                                                class="btn btn-outline-success"
                                                onclick="approveSubscriber(<?= $subscriber['id'] ?>)"
                                                title="Aprobar"
                                            >
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button
                                                class="btn btn-outline-info"
                                                onclick="resendVerification(<?= $subscriber['id'] ?>)"
                                                title="Reenviar verificación"
                                            >
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($subscriber['status'] === 'unsubscribed'): ?>
                                            <button
                                                class="btn btn-outline-primary"
                                                onclick="reactivateSubscriber(<?= $subscriber['id'] ?>)"
                                                title="Reactivar"
                                            >
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        <?php endif; ?>

                                        <button
                                            class="btn btn-outline-danger"
                                            onclick="deleteSubscriber(<?= $subscriber['id'] ?>, '<?= htmlspecialchars($subscriber['email']) ?>')"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white border-top">
            <nav aria-label="Navegación de suscriptores">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                                Anterior
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                                Siguiente
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript para funcionalidades AJAX -->
<script>
// Mostrar alertas
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alertHtml;

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Aprobar suscriptor
async function approveSubscriber(id) {
    if (!confirm('¿Aprobar este suscriptor manualmente?')) return;

    try {
        const response = await fetch('/api/subscribers/approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
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
        showAlert('Error al aprobar suscriptor', 'danger');
    }
}

// Eliminar suscriptor
async function deleteSubscriber(id, email) {
    if (!confirm(`¿Eliminar permanentemente a ${email}?`)) return;

    try {
        const response = await fetch('/api/subscribers/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
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
        const response = await fetch('/api/subscribers/resend-verification', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        showAlert('Error al reenviar verificación', 'danger');
    }
}

// Reactivar suscriptor
async function reactivateSubscriber(id) {
    if (!confirm('¿Reactivar este suscriptor?')) return;

    try {
        const response = await fetch('/api/subscribers/reactivate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
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

<?php include __DIR__ . '/layout/footer.php'; ?>
