<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: lista.php'); exit; }

$row = run('SELECT * FROM clientes WHERE idC = ?', [$id])->fetch();
if (!$row) { header('Location: lista.php'); exit; }

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar cliente #<?= (int)$row['idC'] ?> — OZC Tech</title>
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
            <ul><li><a href="lista.php">LISTA</a></li></ul>
        </nav>
    </div>
</header>

<main class="sobre-nosotros" style="max-width: min(90vw, 800px);">
    <h2>Editar cliente #<?= (int)$row['idC'] ?></h2>

    <?php if (!empty($row['imageC'])): ?>
        <p>Imagen actual:</p>
        <img src="uploads/<?= htmlspecialchars($row['imageC']) ?>" alt="Imagen actual" style="width:120px;height:120px;object-fit:cover;border-radius:8px;">
    <?php endif; ?>

    <form action="update.php" method="post" enctype="multipart/form-data" style="display:grid;gap:12px;margin-top:12px;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="id" value="<?= (int)$row['idC'] ?>">
        <input type="hidden" name="current_image" value="<?= htmlspecialchars((string)$row['imageC']) ?>">

        <label>Nombre <input type="text" name="nameC" value="<?= htmlspecialchars($row['nameC']) ?>" required></label>
        <label>Email <input type="email" name="emailC" value="<?= htmlspecialchars($row['emailC']) ?>" required></label>
        <label>Teléfono <input type="text" name="telC" value="<?= htmlspecialchars($row['telC']) ?>" required></label>
        <label>Reemplazar imagen (opcional) <input type="file" name="imageC" accept="image/*"></label>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn-primario">Actualizar</button>
            <a href="lista.php" class="btn-secundario" style="text-decoration:none;">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
