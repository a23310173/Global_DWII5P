<?php
// PHP/auth/register.php
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
$ok    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) {
    http_response_code(419);
    exit('CSRF inválido.');
  }

  $username = trim((string)($_POST['username'] ?? ''));
  $password = (string)($_POST['password'] ?? '');
  $confirm  = (string)($_POST['confirm']  ?? '');

  if ($username === '' || $password === '' || $confirm === '') {
    $error = 'Todos los campos son obligatorios';
  } elseif (strlen($username) < 3) {
    $error = 'El usuario debe tener al menos 3 caracteres';
  } elseif (strlen($password) < 6) {
    $error = 'La contraseña debe tener al menos 6 caracteres';
  } elseif ($password !== $confirm) {
    $error = 'Las contraseñas no coinciden';
  } else {
    // ¿existe ya?
    $exists = run('SELECT id FROM users WHERE username = ? LIMIT 1', [$username])->fetch();
    if ($exists) {
      $error = 'El usuario ya existe';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      run('INSERT INTO users (username, password_hash) VALUES (?, ?)', [$username, $hash]);
      // O bien auto-login:
      // $id = (int)db()->lastInsertId(); session_regenerate_id(true); $_SESSION['user_id']=$id; $_SESSION['username']=$username;
      $ok = 'Cuenta creada. Ahora puedes iniciar sesión.';
      // Redirige al login con mensaje flash
      $_SESSION['flash'] = $ok;
      header('Location: login.php');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro — OZC Tech</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../CSS/style2.css">
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
          <li><a href="../src/index.html">INICIO</a></li>
          <li><a href="../src/servicios.html">SERVICIOS</a></li>
          <li><a href="../src/login.html">LOGIN</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="sobre-nosotros" style="max-width:min(90vw,560px);">
    <h2>Crear cuenta</h2>

    <?php if ($error): ?>
      <div class="claim" style="margin-top:10px;background:rgba(255,120,120,.9);color:#000;">
        <p style="margin:0;text-align:center;font-weight:600;"><?= htmlspecialchars($error) ?></p>
      </div>
    <?php endif; ?>

    <form action="" method="post" style="display:grid;gap:12px;margin-top:12px;">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label>Usuario
        <input type="text" name="username" required minlength="3">
      </label>

      <label>Contraseña
        <input type="password" name="password" required minlength="6">
      </label>

      <label>Confirmar contraseña
        <input type="password" name="confirm" required minlength="6">
      </label>

      <div style="display:flex;gap:10px;align-items:center;">
        <button type="submit" class="btn-primario">Registrar</button>
        <a href="login.php" class="btn-secundario" style="text-decoration:none;">Ya tengo cuenta</a>
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

  <script src="../JS/header-nav.js" defer></script>
  <script src="../JS/mobile.js" defer></script>
</body>
</html>
