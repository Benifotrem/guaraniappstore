<?php
/**
 * Clase Auth - Gestión de autenticación y 2FA
 */

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Intentar login de administrador
     */
    public function login($username, $password) {
        // Verificar si la cuenta está bloqueada
        $user = $this->getUserByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Credenciales incorrectas'];
        }

        // Verificar si está bloqueado
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $minutes_left = ceil((strtotime($user['locked_until']) - time()) / 60);
            return [
                'success' => false,
                'message' => "Cuenta bloqueada. Intenta nuevamente en {$minutes_left} minutos."
            ];
        }

        // Verificar contraseña
        if (!verify_password($password, $user['password_hash'])) {
            $this->incrementLoginAttempts($user['id']);
            return ['success' => false, 'message' => 'Credenciales incorrectas'];
        }

        // Resetear intentos de login
        $this->resetLoginAttempts($user['id']);

        // Si tiene 2FA activado, requerir verificación
        if ($user['two_fa_enabled']) {
            $_SESSION['pending_2fa_user_id'] = $user['id'];
            return [
                'success' => false,
                'requires_2fa' => true,
                'message' => 'Ingresa el código de autenticación'
            ];
        }

        // Login exitoso sin 2FA
        return $this->completeLogin($user);
    }

    /**
     * Verificar código 2FA
     */
    public function verify2FA($code) {
        if (!isset($_SESSION['pending_2fa_user_id'])) {
            return ['success' => false, 'message' => 'Sesión no válida'];
        }

        $user_id = $_SESSION['pending_2fa_user_id'];
        $user = $this->getUserById($user_id);

        if (!$user || !$user['two_fa_enabled']) {
            return ['success' => false, 'message' => 'Usuario no válido'];
        }

        // Verificar código TOTP
        if ($this->verifyTOTP($user['two_fa_secret'], $code)) {
            unset($_SESSION['pending_2fa_user_id']);
            return $this->completeLogin($user);
        }

        return ['success' => false, 'message' => 'Código incorrecto'];
    }

    /**
     * Completar proceso de login
     */
    private function completeLogin($user) {
        // Actualizar último login
        $this->db->update('admin_users',
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$user['id']]
        );

        // Crear sesión
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_full_name'] = $user['full_name'];

        // Crear token de sesión en BD
        $session_token = generate_token(64);
        $expires_at = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

        $this->db->insert('admin_sessions', [
            'admin_user_id' => $user['id'],
            'session_token' => $session_token,
            'ip_address' => get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'expires_at' => $expires_at
        ]);

        $_SESSION['session_token'] = $session_token;

        return [
            'success' => true,
            'message' => 'Login exitoso'
        ];
    }

    /**
     * Logout
     */
    public function logout() {
        if (isset($_SESSION['session_token'])) {
            $this->db->delete('admin_sessions',
                'session_token = ?',
                [$_SESSION['session_token']]
            );
        }

        session_destroy();
        session_start();
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword($user_id, $old_password, $new_password) {
        $user = $this->getUserById($user_id);

        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        if (!verify_password($old_password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
        }

        $new_hash = hash_password($new_password);
        $this->db->update('admin_users',
            ['password_hash' => $new_hash],
            'id = ?',
            [$user_id]
        );

        return ['success' => true, 'message' => 'Contraseña actualizada'];
    }

    /**
     * Activar 2FA
     */
    public function enable2FA($user_id) {
        $secret = $this->generateTOTPSecret();

        $this->db->update('admin_users', [
            'two_fa_secret' => $secret,
            'two_fa_method' => 'google_authenticator'
        ], 'id = ?', [$user_id]);

        return [
            'success' => true,
            'secret' => $secret,
            'qr_code_url' => $this->getQRCodeURL($secret)
        ];
    }

    /**
     * Confirmar activación de 2FA
     */
    public function confirm2FA($user_id, $code) {
        $user = $this->getUserById($user_id);

        if (!$user || !$user['two_fa_secret']) {
            return ['success' => false, 'message' => 'Configuración 2FA no encontrada'];
        }

        if ($this->verifyTOTP($user['two_fa_secret'], $code)) {
            $this->db->update('admin_users',
                ['two_fa_enabled' => 1],
                'id = ?',
                [$user_id]
            );

            return ['success' => true, 'message' => '2FA activado correctamente'];
        }

        return ['success' => false, 'message' => 'Código incorrecto'];
    }

    /**
     * Desactivar 2FA
     */
    public function disable2FA($user_id) {
        $this->db->update('admin_users', [
            'two_fa_enabled' => 0,
            'two_fa_secret' => null,
            'two_fa_method' => 'none'
        ], 'id = ?', [$user_id]);

        return ['success' => true, 'message' => '2FA desactivado'];
    }

    /**
     * Generar secreto TOTP
     */
    private function generateTOTPSecret() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Verificar código TOTP
     */
    private function verifyTOTP($secret, $code) {
        $time = floor(time() / 30);

        // Verificar código actual y ±1 ventana de tiempo (30 segundos antes/después)
        for ($i = -1; $i <= 1; $i++) {
            if ($this->generateTOTP($secret, $time + $i) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generar código TOTP
     */
    private function generateTOTP($secret, $time) {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decodificar base32
     */
    private function base32Decode($secret) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $decoded = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos($chars, $secret[$i]);
            if ($val === false) continue;

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $decoded .= chr(($buffer >> ($bitsLeft - 8)) & 0xff);
                $bitsLeft -= 8;
            }
        }

        return $decoded;
    }

    /**
     * Obtener URL del código QR para Google Authenticator
     */
    private function getQRCodeURL($secret) {
        $issuer = urlencode(TWO_FA_ISSUER);
        $email = urlencode($_SESSION['admin_email'] ?? 'admin');
        $otpauth = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}";

        // Usar servicio de generación de QR de Google
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth);
    }

    /**
     * Incrementar intentos de login
     */
    private function incrementLoginAttempts($user_id) {
        $user = $this->getUserById($user_id);
        $attempts = $user['login_attempts'] + 1;

        $update_data = ['login_attempts' => $attempts];

        // Bloquear después de MAX_LOGIN_ATTEMPTS
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $update_data['locked_until'] = date('Y-m-d H:i:s', time() + LOCKOUT_TIME);
        }

        $this->db->update('admin_users', $update_data, 'id = ?', [$user_id]);
    }

    /**
     * Resetear intentos de login
     */
    private function resetLoginAttempts($user_id) {
        $this->db->update('admin_users', [
            'login_attempts' => 0,
            'locked_until' => null
        ], 'id = ?', [$user_id]);
    }

    /**
     * Obtener usuario por username
     */
    private function getUserByUsername($username) {
        return $this->db->fetchOne(
            "SELECT * FROM admin_users WHERE username = ? LIMIT 1",
            [$username]
        );
    }

    /**
     * Obtener usuario por ID
     */
    private function getUserById($id) {
        return $this->db->fetchOne(
            "SELECT * FROM admin_users WHERE id = ? LIMIT 1",
            [$id]
        );
    }

    /**
     * Limpiar sesiones expiradas
     */
    public function cleanExpiredSessions() {
        $this->db->query("DELETE FROM admin_sessions WHERE expires_at < NOW()");
    }
}
