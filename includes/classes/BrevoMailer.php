<?php
/**
 * Brevo (Sendinblue) Email Service Integration
 *
 * Simple integration with Brevo API for transactional emails
 * Requires only API key for authentication
 */
class BrevoMailer {
    private $apiKey;
    private $apiUrl = 'https://api.brevo.com/v3/smtp/email';
    private $fromEmail;
    private $fromName;

    public function __construct($apiKey, $fromEmail, $fromName = 'Guarani App Store') {
        $this->apiKey = $apiKey;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /**
     * Send transactional email via Brevo API
     *
     * @param string $toEmail Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $htmlContent HTML content of the email
     * @param string $textContent Plain text content (optional)
     * @return array Response with success status and message
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlContent, $textContent = null) {
        try {
            $data = [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $toName ?: $toEmail
                    ]
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ];

            if ($textContent) {
                $data['textContent'] = $textContent;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_get_info($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return [
                    'success' => false,
                    'message' => 'cURL Error: ' . $error
                ];
            }

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'message' => 'Email sent successfully',
                    'messageId' => $result['messageId'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send email',
                    'code' => $result['code'] ?? $httpCode
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send verification email to new subscriber
     */
    public function sendVerificationEmail($email, $name, $verificationToken) {
        $verificationUrl = SITE_URL . '/verify-subscription?token=' . urlencode($verificationToken);

        $htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>¡Bienvenido a Guarani App Store!</h1>
        </div>
        <div class='content'>
            <p>Hola " . htmlspecialchars($name ?: 'suscriptor') . ",</p>
            <p>Gracias por suscribirte a nuestro blog sobre aplicaciones web progresivas y tecnología.</p>
            <p>Para activar tu suscripción y comenzar a recibir nuestras actualizaciones, por favor confirma tu dirección de email:</p>
            <div style='text-align: center;'>
                <a href='" . htmlspecialchars($verificationUrl) . "' class='button'>Confirmar mi email</a>
            </div>
            <p style='font-size: 12px; color: #666; margin-top: 20px;'>
                Si no te suscribiste a este newsletter, puedes ignorar este email.
            </p>
            <p style='font-size: 12px; color: #666;'>
                O copia este enlace en tu navegador:<br>
                <a href='" . htmlspecialchars($verificationUrl) . "'>" . htmlspecialchars($verificationUrl) . "</a>
            </p>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " Guarani App Store. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>";

        $textContent = "Hola " . ($name ?: 'suscriptor') . ",\n\n" .
                      "Gracias por suscribirte a Guarani App Store.\n\n" .
                      "Para confirmar tu suscripción, visita este enlace:\n" .
                      $verificationUrl . "\n\n" .
                      "Si no te suscribiste, ignora este email.\n\n" .
                      "Saludos,\nGuarani App Store";

        return $this->sendEmail(
            $email,
            $name,
            '¡Confirma tu suscripción a Guarani App Store!',
            $htmlContent,
            $textContent
        );
    }

    /**
     * Send welcome email to verified subscriber
     */
    public function sendWelcomeEmail($email, $name) {
        $unsubscribeUrl = SITE_URL . '/unsubscribe?email=' . urlencode($email);

        $htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✓ ¡Suscripción Confirmada!</h1>
        </div>
        <div class='content'>
            <p>Hola " . htmlspecialchars($name ?: 'suscriptor') . ",</p>
            <p><strong>¡Tu email ha sido verificado exitosamente!</strong></p>
            <p>Ahora recibirás nuestras actualizaciones sobre:</p>
            <ul>
                <li>🚀 Nuevas Progressive Web Apps</li>
                <li>📱 Tutoriales y guías técnicas</li>
                <li>💡 Tendencias en desarrollo web</li>
                <li>🎨 Recursos y herramientas</li>
            </ul>
            <p>¡Gracias por unirte a nuestra comunidad!</p>
        </div>
        <div class='footer'>
            <p>
                <a href='" . htmlspecialchars($unsubscribeUrl) . "' style='color: #666;'>Cancelar suscripción</a>
            </p>
            <p>&copy; " . date('Y') . " Guarani App Store. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>";

        $textContent = "Hola " . ($name ?: 'suscriptor') . ",\n\n" .
                      "¡Tu suscripción ha sido confirmada!\n\n" .
                      "Recibirás actualizaciones sobre PWAs, tutoriales y tendencias en desarrollo web.\n\n" .
                      "Para cancelar tu suscripción: " . $unsubscribeUrl . "\n\n" .
                      "Saludos,\nGuarani App Store";

        return $this->sendEmail(
            $email,
            $name,
            '¡Bienvenido a Guarani App Store! 🎉',
            $htmlContent,
            $textContent
        );
    }

    /**
     * Send new blog post notification to active subscribers
     */
    public function sendBlogNotification($email, $name, $articleTitle, $articleExcerpt, $articleUrl) {
        $unsubscribeUrl = SITE_URL . '/unsubscribe?email=' . urlencode($email);

        $htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; }
        .article { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .button { display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>📝 Nuevo Artículo en el Blog</h1>
        </div>
        <div class='content'>
            <p>Hola " . htmlspecialchars($name ?: 'suscriptor') . ",</p>
            <p>Tenemos un nuevo artículo que podría interesarte:</p>
            <div class='article'>
                <h2 style='color: #667eea; margin-top: 0;'>" . htmlspecialchars($articleTitle) . "</h2>
                <p>" . htmlspecialchars($articleExcerpt) . "</p>
                <div style='text-align: center;'>
                    <a href='" . htmlspecialchars($articleUrl) . "' class='button'>Leer artículo completo</a>
                </div>
            </div>
        </div>
        <div class='footer'>
            <p>
                <a href='" . htmlspecialchars($unsubscribeUrl) . "' style='color: #666;'>Cancelar suscripción</a>
            </p>
            <p>&copy; " . date('Y') . " Guarani App Store. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>";

        $textContent = "Hola " . ($name ?: 'suscriptor') . ",\n\n" .
                      "Nuevo artículo: " . $articleTitle . "\n\n" .
                      $articleExcerpt . "\n\n" .
                      "Leer más: " . $articleUrl . "\n\n" .
                      "Cancelar suscripción: " . $unsubscribeUrl . "\n\n" .
                      "Saludos,\nGuarani App Store";

        return $this->sendEmail(
            $email,
            $name,
            '📝 ' . $articleTitle,
            $htmlContent,
            $textContent
        );
    }
}
