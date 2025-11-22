<?php
/**
 * Controlador: Unsubscribe - Desuscribirse del blog
 */

$db = Database::getInstance();
$email = $_GET['email'] ?? '';

if (empty($email)) {
    $_SESSION['error'] = 'Email inválido';
    redirect(get_url());
}

$subscriber = $db->fetchOne("
    SELECT * FROM blog_subscribers
    WHERE email = ?
", [$email]);

if (!$subscriber) {
    $_SESSION['error'] = 'No encontramos tu suscripción';
    redirect(get_url());
}

if ($subscriber['status'] === 'unsubscribed') {
    $_SESSION['info'] = 'Ya estabas desuscrito';
    redirect(get_url());
}

// Desuscribir
$db->update('blog_subscribers', [
    'status' => 'unsubscribed',
    'unsubscribed_at' => date('Y-m-d H:i:s')
], 'id = ?', [$subscriber['id']]);

$page_title = 'Desuscripción Exitosa';
include INCLUDES_PATH . '/views/landing/header.php';
?>

<section class="section" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container text-center" style="max-width: 600px;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">😢</div>
        <h1>Desuscripción Exitosa</h1>
        <p style="font-size: 1.25rem; margin: 2rem 0;">
            Lamentamos verte partir. Has sido desuscrito exitosamente de nuestro blog.
        </p>
        <p>
            Si cambias de opinión, siempre puedes volver a suscribirte desde nuestra página principal.
        </p>
        <div class="mt-4">
            <a href="<?php echo get_url(); ?>" class="btn btn-primary">
                Volver al Inicio
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
