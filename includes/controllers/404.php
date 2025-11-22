<?php
/**
 * Controlador: 404 - Página no encontrada
 */

http_response_code(404);

$page_title = 'Página no encontrada - Guarani App Store';

?>
<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<section class="section" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div class="container text-center">
        <h1 style="font-size: 6rem; color: var(--guarani-primary); margin-bottom: 1rem;">404</h1>
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">Página no encontrada</h2>
        <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.8;">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="<?php echo get_url(); ?>" class="btn btn-primary">
                Volver al Inicio
            </a>
            <a href="<?php echo get_url('webapps'); ?>" class="btn btn-secondary">
                Ver Aplicaciones
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
