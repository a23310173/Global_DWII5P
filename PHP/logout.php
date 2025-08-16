<?php
// PHP/auth/logout.php
declare(strict_types=1);
session_start();

/* Limpia todos los datos de la sesión */
$_SESSION = [];

/* Borra la cookie de sesión (si existe) */
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),        // nombre de la cookie
        '',                    // valor vacío
        time() - 42000,        // expirada en el pasado
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/* Destruye la sesión */
session_destroy();

/* (Opcional) Inicia una nueva sesión para mostrar un mensaje flash en login */
session_start();
$_SESSION['flash'] = 'Sesión cerrada correctamente.';

/* Redirige al login */
header('Location: login.php');
exit;
