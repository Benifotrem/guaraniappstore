<?php include INCLUDES_PATH . '/views/admin/layout/header.php'; ?>

<div class="admin-content">
    <div class="page-header">
        <h1>🚀 Beta Testers</h1>
        <p>Gestión de usuarios del programa beta</p>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 0.9rem; color: #718096; margin-bottom: 0.5rem;">Total Beta Testers</div>
            <div style="font-size: 2rem; font-weight: bold; color: #2d3748;"><?php echo $stats['total']; ?></div>
        </div>
        <div class="stat-card" style="background: #fef3c7; padding: 1.5rem; border-radius: 8px;">
            <div style="font-size: 0.9rem; color: #92400e; margin-bottom: 0.5rem;">⏳ Pendientes</div>
            <div style="font-size: 2rem; font-weight: bold; color: #92400e;"><?php echo $stats['pending']; ?></div>
        </div>
        <div class="stat-card" style="background: #d1fae5; padding: 1.5rem; border-radius: 8px;">
            <div style="font-size: 0.9rem; color: #065f46; margin-bottom: 0.5rem;">✅ Activos</div>
            <div style="font-size: 2rem; font-weight: bold; color: #065f46;"><?php echo $stats['active']; ?></div>
        </div>
        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 0.9rem; color: #718096; margin-bottom: 0.5rem;">🐛 Bugs Totales</div>
            <div style="font-size: 2rem; font-weight: bold; color: #2d3748;"><?php echo $stats['total_bugs']; ?></div>
        </div>
        <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 0.9rem; color: #718096; margin-bottom: 0.5rem;">💡 Sugerencias</div>
            <div style="font-size: 2rem; font-weight: bold; color: #2d3748;"><?php echo $stats['total_suggestions']; ?></div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="filters" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Estado</label>
                <select name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <option value="">Todos</option>
                    <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactive" <?php echo $filter_status === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nivel</label>
                <select name="level" style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <option value="">Todos</option>
                    <option value="bronze" <?php echo $filter_level === 'bronze' ? 'selected' : ''; ?>>Bronze</option>
                    <option value="silver" <?php echo $filter_level === 'silver' ? 'selected' : ''; ?>>Silver</option>
                    <option value="gold" <?php echo $filter_level === 'gold' ? 'selected' : ''; ?>>Gold</option>
                    <option value="platinum" <?php echo $filter_level === 'platinum' ? 'selected' : ''; ?>>Platinum</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Buscar</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nombre, email, telegram..." 
                       style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px;">
            </div>
            <div>
                <button type="submit" style="padding: 0.5rem 1.5rem; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    Filtrar
                </button>
                <a href="<?php echo get_url('admin/beta-testers'); ?>" 
                   style="display: inline-block; padding: 0.5rem 1rem; background: #e2e8f0; color: #2d3748; text-decoration: none; border-radius: 6px; margin-left: 0.5rem;">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Beta Testers -->
    <div class="table-container" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f7fafc;">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Usuario</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Contacto</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #2d3748;">Estado</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #2d3748;">Nivel</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #2d3748;">Contribuciones</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #2d3748;">Registro</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #2d3748;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($beta_testers)): ?>
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center; color: #718096;">
                            No se encontraron beta testers con los filtros seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($beta_testers as $tester): ?>
                        <tr style="border-top: 1px solid #e2e8f0;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #2d3748;"><?php echo htmlspecialchars($tester['name']); ?></div>
                                <div style="font-size: 0.875rem; color: #718096;">ID: <?php echo $tester['id']; ?></div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 0.875rem; color: #2d3748;"><?php echo htmlspecialchars($tester['email']); ?></div>
                                <?php if ($tester['telegram_username']): ?>
                                    <div style="font-size: 0.875rem; color: #718096;">@<?php echo htmlspecialchars($tester['telegram_username']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php
                                $status_colors = [
                                    'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => '⏳ Pendiente'],
                                    'active' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => '✅ Activo'],
                                    'inactive' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => '❌ Inactivo']
                                ];
                                $status = $status_colors[$tester['status']];
                                ?>
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; background: <?php echo $status['bg']; ?>; color: <?php echo $status['text']; ?>; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                                    <?php echo $status['label']; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php
                                $level_icons = ['bronze' => '🥉', 'silver' => '🥈', 'gold' => '🥇', 'platinum' => '💎'];
                                echo $level_icons[$tester['contribution_level']] . ' ' . ucfirst($tester['contribution_level']);
                                ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="font-weight: 600;"><?php echo $tester['total_contributions']; ?></div>
                                <div style="font-size: 0.75rem; color: #718096;">
                                    🐛 <?php echo $tester['bugs_reported']; ?> • 💡 <?php echo $tester['suggestions_accepted']; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center; font-size: 0.875rem; color: #718096;">
                                <?php echo date('d/m/Y', strtotime($tester['created_at'])); ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php if ($tester['status'] === 'pending'): ?>
                                    <button onclick="approveTester(<?php echo $tester['id']; ?>)" 
                                            style="padding: 0.375rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                        ✅ Aprobar
                                    </button>
                                <?php endif; ?>
                                <button onclick="changeLevelModal(<?php echo $tester['id']; ?>, '<?php echo $tester['contribution_level']; ?>')"
                                        style="padding: 0.375rem 0.75rem; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                    🏅 Cambiar Nivel
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function approveTester(id) {
    if (confirm('¿Activar esta cuenta de beta tester?')) {
        fetch('<?php echo get_url('admin/beta-testers/approve'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&csrf_token=' + '<?php echo generate_csrf_token(); ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Cuenta activada exitosamente');
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        });
    }
}

function changeLevelModal(id, currentLevel) {
    const levels = ['bronze', 'silver', 'gold', 'platinum'];
    const newLevel = prompt('Cambiar nivel de:\n\nBronze (🥉)\nSilver (🥈)\nGold (🥇)\nPlatinum (💎)\n\nNivel actual: ' + currentLevel, currentLevel);
    
    if (newLevel && levels.includes(newLevel.toLowerCase())) {
        fetch('<?php echo get_url('admin/beta-testers/change-level'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&level=' + newLevel.toLowerCase() + '&csrf_token=' + '<?php echo generate_csrf_token(); ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Nivel cambiado exitosamente');
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        });
    }
}
</script>

<?php include INCLUDES_PATH . '/views/admin/layout/footer.php'; ?>
