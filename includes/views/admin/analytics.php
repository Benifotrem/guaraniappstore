<div class="admin-content">
    <div class="admin-header">
        <h1>📊 Analytics Dashboard</h1>
        <div class="admin-header-actions">
            <select id="days-filter" class="admin-select" onchange="window.location.href='<?php echo get_url('admin/analytics'); ?>?days='+this.value">
                <option value="7" <?php echo $days == 7 ? 'selected' : ''; ?>>Últimos 7 días</option>
                <option value="30" <?php echo $days == 30 ? 'selected' : ''; ?>>Últimos 30 días</option>
                <option value="90" <?php echo $days == 90 ? 'selected' : ''; ?>>Últimos 90 días</option>
            </select>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="analytics-metrics-grid">
        <div class="analytics-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                📱
            </div>
            <div class="metric-content">
                <div class="metric-value"><?php echo number_format($stats['total_webapps']); ?></div>
                <div class="metric-label">Webapps Publicadas</div>
            </div>
        </div>

        <div class="analytics-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                👁️
            </div>
            <div class="metric-content">
                <div class="metric-value"><?php echo number_format($stats['total_views']); ?></div>
                <div class="metric-label">Total Vistas</div>
                <?php if ($views_change != 0): ?>
                    <div class="metric-change <?php echo $views_change > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $views_change > 0 ? '↑' : '↓'; ?>
                        <?php echo abs(round($views_change, 1)); ?>% vs anterior
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="analytics-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                🖱️
            </div>
            <div class="metric-content">
                <div class="metric-value"><?php echo number_format($stats['total_clicks']); ?></div>
                <div class="metric-label">Total Clicks</div>
                <div class="metric-sublabel">CTR: <?php echo $conversion_rate; ?>%</div>
            </div>
        </div>

        <div class="analytics-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                💬
            </div>
            <div class="metric-content">
                <div class="metric-value"><?php echo number_format($stats['total_feedback']); ?></div>
                <div class="metric-label">Reportes Feedback</div>
            </div>
        </div>

        <div class="analytics-metric-card">
            <div class="metric-icon" style="background: var(--gradient-primary);">
                ⚡
            </div>
            <div class="metric-content">
                <div class="metric-value"><?php echo number_format($stats['active_beta_testers']); ?></div>
                <div class="metric-label">Beta Testers Activos</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="analytics-charts-grid">
        <!-- Visitas por Día -->
        <div class="analytics-chart-card">
            <h3>📈 Visitas por Día</h3>
            <canvas id="dailyViewsChart"></canvas>
        </div>

        <!-- Top Webapps -->
        <div class="analytics-chart-card">
            <h3>🏆 Top 10 Webapps</h3>
            <canvas id="topWebappsChart"></canvas>
        </div>

        <!-- Feedback por Tipo -->
        <div class="analytics-chart-card">
            <h3>💬 Feedback por Tipo</h3>
            <canvas id="feedbackTypeChart"></canvas>
        </div>

        <!-- Beta Testers por Nivel -->
        <div class="analytics-chart-card">
            <h3>⚡ Beta Testers por Nivel</h3>
            <canvas id="betaTesterLevelChart"></canvas>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="analytics-recent-grid">
        <!-- Feedback Reciente -->
        <div class="analytics-recent-card">
            <h3>💬 Feedback Reciente</h3>
            <div class="recent-list">
                <?php foreach ($recent_feedback as $item): ?>
                    <div class="recent-item">
                        <span class="recent-badge recent-badge-<?php echo e($item['type']); ?>">
                            <?php echo e($item['type']); ?>
                        </span>
                        <div class="recent-info">
                            <div class="recent-title"><?php echo e($item['title']); ?></div>
                            <div class="recent-meta">
                                <?php echo format_date_es($item['created_at'], 'short'); ?>
                                · <span class="status-<?php echo e($item['status']); ?>"><?php echo e($item['status']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Beta Testers Recientes -->
        <div class="analytics-recent-card">
            <h3>⚡ Nuevos Beta Testers</h3>
            <div class="recent-list">
                <?php foreach ($recent_beta_signups as $item): ?>
                    <div class="recent-item">
                        <div class="recent-avatar">
                            <?php echo strtoupper(substr($item['full_name'], 0, 1)); ?>
                        </div>
                        <div class="recent-info">
                            <div class="recent-title"><?php echo e($item['full_name']); ?></div>
                            <div class="recent-meta">
                                <?php echo format_date_es($item['created_at'], 'short'); ?>
                                · <span class="status-<?php echo e($item['status']); ?>"><?php echo e($item['status']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Configuración global de Chart.js
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = '#666';

// Colores Guaraní
const guaraniColors = {
    primary: 'hsl(84, 40%, 35%)',
    primaryLight: 'hsl(84, 40%, 45%)',
    secondary: 'hsl(84, 30%, 45%)',
    accent: 'hsl(84, 50%, 25%)',
};

// 1. Gráfico de Visitas por Día (Línea)
const dailyViewsCtx = document.getElementById('dailyViewsChart').getContext('2d');
new Chart(dailyViewsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($daily_views_complete, 'date')); ?>,
        datasets: [{
            label: 'Vistas',
            data: <?php echo json_encode(array_column($daily_views_complete, 'views')); ?>,
            borderColor: guaraniColors.primary,
            backgroundColor: 'hsla(84, 40%, 35%, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: guaraniColors.primary,
                borderWidth: 1,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    }
});

// 2. Top Webapps (Barras Horizontales)
const topWebappsCtx = document.getElementById('topWebappsChart').getContext('2d');
new Chart(topWebappsCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($top_webapps, 'title')); ?>,
        datasets: [{
            label: 'Vistas',
            data: <?php echo json_encode(array_column($top_webapps, 'view_count')); ?>,
            backgroundColor: [
                'hsla(84, 40%, 35%, 0.8)',
                'hsla(84, 40%, 40%, 0.8)',
                'hsla(84, 40%, 45%, 0.8)',
                'hsla(84, 40%, 50%, 0.8)',
                'hsla(84, 40%, 55%, 0.8)',
                'hsla(84, 40%, 60%, 0.8)',
                'hsla(84, 40%, 65%, 0.8)',
                'hsla(84, 40%, 70%, 0.8)',
                'hsla(84, 40%, 75%, 0.8)',
                'hsla(84, 40%, 80%, 0.8)',
            ],
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
        },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// 3. Feedback por Tipo (Dona)
const feedbackTypeCtx = document.getElementById('feedbackTypeChart').getContext('2d');
new Chart(feedbackTypeCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($feedback_by_type, 'type')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($feedback_by_type, 'count')); ?>,
            backgroundColor: [
                '#f5576c',
                '#4facfe',
                '#fa709a',
                '#fee140',
                '#667eea'
            ],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 12 } }
            }
        }
    }
});

// 4. Beta Testers por Nivel (Barras)
const betaTesterLevelCtx = document.getElementById('betaTesterLevelChart').getContext('2d');
new Chart(betaTesterLevelCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_map('ucfirst', array_column($beta_testers_by_level, 'level'))); ?>,
        datasets: [{
            label: 'Beta Testers',
            data: <?php echo json_encode(array_column($beta_testers_by_level, 'count')); ?>,
            backgroundColor: [
                'rgba(192, 192, 192, 0.8)', // Platinum (plata/blanco)
                'rgba(255, 215, 0, 0.8)',   // Gold
                'rgba(192, 192, 192, 0.6)', // Silver
                'rgba(205, 127, 50, 0.8)',  // Bronze
            ],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>

<style>
/* Analytics Metrics Grid */
.analytics-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.analytics-metric-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s, box-shadow 0.2s;
}

.analytics-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.metric-content {
    flex: 1;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--foreground);
    line-height: 1;
}

.metric-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.25rem;
}

.metric-sublabel {
    font-size: 0.75rem;
    color: #999;
    margin-top: 0.25rem;
}

.metric-change {
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.metric-change.positive { color: #10b981; }
.metric-change.negative { color: #ef4444; }

/* Analytics Charts Grid */
.analytics-charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.analytics-chart-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.analytics-chart-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: var(--foreground);
}

.analytics-chart-card canvas {
    height: 300px !important;
}

/* Recent Activity Grid */
.analytics-recent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.analytics-recent-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.analytics-recent-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: var(--foreground);
}

.recent-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius);
    background: #f9fafb;
    transition: background 0.2s;
}

.recent-item:hover {
    background: #f3f4f6;
}

.recent-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    flex-shrink: 0;
}

.recent-badge-bug { background: #fee2e2; color: #991b1b; }
.recent-badge-feature { background: #dbeafe; color: #1e40af; }
.recent-badge-improvement { background: #fef3c7; color: #92400e; }
.recent-badge-question { background: #f3e8ff; color: #6b21a8; }

.recent-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gradient-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.recent-info {
    flex: 1;
    min-width: 0;
}

.recent-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--foreground);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.recent-meta {
    font-size: 0.75rem;
    color: #666;
    margin-top: 0.25rem;
}

.status-new { color: #2563eb; font-weight: 600; }
.status-in_progress { color: #f59e0b; font-weight: 600; }
.status-resolved { color: #10b981; font-weight: 600; }
.status-active { color: #10b981; font-weight: 600; }
.status-pending { color: #f59e0b; font-weight: 600; }

/* Responsive */
@media (max-width: 768px) {
    .analytics-charts-grid,
    .analytics-recent-grid {
        grid-template-columns: 1fr;
    }
}
</style>
