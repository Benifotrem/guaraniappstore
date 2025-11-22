<?php
/**
 * Controlador: Admin Subscribers - Gestión de suscriptores
 */

require_admin_auth();

$db = Database::getInstance();

// Obtener estadísticas
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers"),
    'active' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'active'"),
    'pending' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'pending'"),
    'unsubscribed' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'unsubscribed'"),
];

// Obtener suscriptores
$subscribers = $db->fetchAll("
    SELECT * FROM blog_subscribers
    ORDER BY created_at DESC
");

$page_title = 'Gestión de Suscriptores';
include INCLUDES_PATH . '/views/admin/layout/header.php';
?>

<h2 class="mb-4">Suscriptores del Blog</h2>

<!-- Stats -->
<div class="grid grid-cols-4 gap-3 mb-4">
    <div class="card">
        <div class="stat-card-value"><?php echo format_number($stats['total']); ?></div>
        <div class="stat-card-label">Total</div>
    </div>
    <div class="card">
        <div class="stat-card-value text-success"><?php echo format_number($stats['active']); ?></div>
        <div class="stat-card-label">Activos</div>
    </div>
    <div class="card">
        <div class="stat-card-value" style="color: var(--warning);"><?php echo format_number($stats['pending']); ?></div>
        <div class="stat-card-label">Pendientes</div>
    </div>
    <div class="card">
        <div class="stat-card-value" style="opacity: 0.5;"><?php echo format_number($stats['unsubscribed']); ?></div>
        <div class="stat-card-label">Desuscritos</div>
    </div>
</div>

<!-- Subscribers Table -->
<?php if (empty($subscribers)): ?>
    <div class="card text-center" style="padding: 3rem;">
        <h3>No hay suscriptores aún</h3>
        <p>Los suscriptores aparecerán aquí cuando se registren desde la landing page</p>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Origen</th>
                    <th>Fecha de Suscripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber): ?>
                    <tr>
                        <td><?php echo e($subscriber['email']); ?></td>
                        <td><?php echo e($subscriber['name'] ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-<?php
                                echo $subscriber['status'] === 'active' ? 'success' :
                                     ($subscriber['status'] === 'pending' ? 'warning' : 'danger');
                            ?>">
                                <?php echo ucfirst($subscriber['status']); ?>
                            </span>
                        </td>
                        <td><?php echo e($subscriber['subscription_source'] ?? '-'); ?></td>
                        <td><?php echo format_date_es($subscriber['created_at'], 'short'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include INCLUDES_PATH . '/views/admin/layout/footer.php'; ?>
