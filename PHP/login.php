<?php
// PHP/auth/login.php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

// Si ya hay sesión, redirige al listado
if (!empty($_SESSION['user_id'])) {
    header('Location: ../PHP/lista.php');
    exit;
}

// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) {
        http_response_code(419);
        exit('CSRF inválido.');
    }

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Usuario y contraseña son obligatorios';
    } else {
        $row = run('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1', [$username])->fetch();
        if (!$row || !password_verify($password, (string)$row['password_hash'])) {
            $error = 'Credenciales inválidas';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']  = (int)$row['id'];
            $_SESSION['username'] = (string)$row['username'];
            $_SESSION['flash']    = 'Inicio de sesión exitoso';
            header('Location: ../PHP/lista.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login — OZC Tech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<video autoplay muted loop id="myVideo" playsinline>
    <source src="../img/tech.mp4" type="video/mp4">
</video>

<header>
    <div class="header-container">
        <div class="logo"><img src="../img/logo.png" alt="OZC Tech Logo"></div>
        <h1 class="site-title">OZC Tech</h1>
        <nav class="menu">
            <ul>
                <li><a href="../index.html">INICIO</a></li>
                <li><a href="../servicios.html">SERVICIOS</a></li>
                <li><a href="../register.html">REGISTER</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="sobre-nosotros" style="max-width:min(90vw,560px);">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
        <div class="claim" style="margin-top:10px;background:rgba(255,120,120,.9);color:#000;">
            <p style="margin:0;text-align:center;font-weight:600;"><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="post" style="display:grid;gap:12px;margin-top:12px;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <label>Usuario
            <input type="text" name="username" required autofocus>
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <div style="display:flex;gap:10px;align-items:center;">
            <button type="submit" class="btn-primario">Entrar</button>
            <a href="register.php" class="btn-secundario" style="text-decoration:none;">Crear cuenta</a>
        </div>
    </form>
</main>

<footer>
    <div class="footer-container">
        <div class="footer-contacto">
            <a href="https://www.facebook.com/tuperfil" target="_blank" rel="noopener"><img src="../img/facebook.png" alt="Facebook"></a>
            <a href="https://wa.me/521XXXXXXXXXX" target="_blank" rel="noopener"><img src="../img/whatsapp.png" alt="WhatsApp"></a>
            <a href="mailto:tucorreo@gmail.com"><img src="../img/gmail.png" alt="Gmail"></a>
        </div>
        <div class="footer-texto"><p>OZC Tech &copy; since 2025</p></div>
    </div>
</footer>
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="claim" style="margin-top:10px;background:rgba(115,156,244,.85);color:#000;">
        <p style="margin:0;text-align:center;font-weight:600;">
            <?= htmlspecialchars($_SESSION['flash']) ?>
        </p>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>


<script src="../JS/header-nav.js" defer></script>
<script src="../JS/mobile.js" defer></script>
</body>
</html>
