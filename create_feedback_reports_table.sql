-- Tabla para almacenar feedback de beta testers sobre webapps
CREATE TABLE IF NOT EXISTS feedback_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Relaciones
    webapp_id INT NOT NULL,
    beta_tester_id INT NULL, -- NULL permite feedback anónimo

    -- Tipo de feedback
    type ENUM('bug', 'feature', 'review') NOT NULL,

    -- Contenido
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,

    -- Para bugs: severidad
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT NULL,

    -- Para reviews: calificación
    rating TINYINT NULL CHECK (rating >= 1 AND rating <= 5),

    -- Estado y gestión
    status ENUM('new', 'reviewing', 'accepted', 'rejected', 'implemented') DEFAULT 'new',
    admin_notes TEXT NULL,
    reviewed_by INT NULL, -- ID del admin que revisó
    reviewed_at TIMESTAMP NULL,

    -- Metadatos
    user_agent TEXT NULL,
    browser_info VARCHAR(255) NULL,
    screenshot_url VARCHAR(500) NULL,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Índices para búsquedas rápidas
    INDEX idx_webapp_id (webapp_id),
    INDEX idx_beta_tester_id (beta_tester_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),

    -- Foreign keys
    FOREIGN KEY (webapp_id) REFERENCES webapps(id) ON DELETE CASCADE,
    FOREIGN KEY (beta_tester_id) REFERENCES beta_testers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista para estadísticas de feedback por webapp
CREATE OR REPLACE VIEW feedback_stats AS
SELECT
    w.id as webapp_id,
    w.title as webapp_title,
    COUNT(DISTINCT fr.id) as total_feedback,
    SUM(CASE WHEN fr.type = 'bug' THEN 1 ELSE 0 END) as bugs_reported,
    SUM(CASE WHEN fr.type = 'feature' THEN 1 ELSE 0 END) as features_requested,
    SUM(CASE WHEN fr.type = 'review' THEN 1 ELSE 0 END) as reviews_count,
    AVG(CASE WHEN fr.type = 'review' THEN fr.rating END) as average_rating,
    SUM(CASE WHEN fr.status = 'new' THEN 1 ELSE 0 END) as pending_review,
    SUM(CASE WHEN fr.status = 'accepted' THEN 1 ELSE 0 END) as accepted_count,
    SUM(CASE WHEN fr.status = 'implemented' THEN 1 ELSE 0 END) as implemented_count
FROM webapps w
LEFT JOIN feedback_reports fr ON w.id = fr.webapp_id
GROUP BY w.id, w.title;
