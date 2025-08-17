<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo cliente — OZC Tech</title>
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
            <ul><li><a href="lista.php">LISTA</a></li></ul>
        </nav>
    </div>
</header>

<main class="sobre-nosotros" style="max-width: min(90vw, 800px);">
    <h2>Nuevo cliente</h2>

    <form action="insert.php" method="post" enctype="multipart/form-data" style="display:grid;gap:12px;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <label>Nombre <input type="text" name="nameC" required></label>
        <label>Email <input type="email" name="emailC" required></label>
        <label>Teléfono <input type="text" name="telC" required></label>
        <label>Imagen (opcional) <input type="file" name="imageC" accept="image/*"></label>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn-primario">Guardar</button>
            <a href="lista.php" class="btn-secundario" style="text-decoration:none;">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
