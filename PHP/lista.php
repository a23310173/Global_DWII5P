<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

/* CSRF básico para eliminar */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

/* Trae TODOS los clientes (sin created_at) */
$clientes = run('SELECT idC, nameC, emailC, telC, imageC FROM clientes ORDER BY idC DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Clientes — OZC Tech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        :root{ --azul:#739CF4; --negro:#000; --gris:rgba(217,217,217,.5); --grisOsc:rgba(0,0,0,.08); }
        *{ box-sizing:border-box }
        html,body{ margin:0; padding:0 }
        body{ font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--negro); line-height:1.45; }

        /* fondo video */
        #myVideo{ position:fixed; inset:0; width:100%; height:100%; object-fit:cover; z-index:-1; filter:brightness(.55); background:#000; }

        /* header */
        header .header-container{
            background:var(--azul); display:grid; grid-template-columns:auto 1fr auto;
            align-items:center; gap:14px; padding:10px 16px; border-radius:15px; margin:10px;
        }
        .logo img{ display:block; max-height:48px; border-radius:19px }
        .site-title{ margin:0; text-align:center; font-weight:700; font-size:22px; }
        .menu ul{ list-style:none; margin:0; padding:0; display:flex; gap:10px; }
        .menu a{ display:inline-block; padding:8px 10px; text-decoration:none; color:#000; border-radius:8px; }
        .menu a:hover{ background:var(--grisOsc); }

        /* contenedor principal */
        .wrap{ max-width:1100px; margin:18px auto; padding:16px; background:var(--gris); border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,.25); }

        /* toolbar y botones */
        .toolbar{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
        .btn{ display:inline-block; padding:8px 12px; border-radius:10px; text-decoration:none; background:var(--azul); color:#000; font-weight:600; cursor:pointer; border:none; }
        .btn.danger{ background:#ffb3b3; }

        /* flash */
        .flash{ margin:10px 0 16px; padding:12px 16px; border-radius:12px; background:rgba(115,156,244,.85); text-align:center; font-weight:600; }

        /* tabla */
        .table-wrap{ background:#fff; border-radius:12px; overflow:hidden; }
        table{ width:100%; border-collapse:collapse; color:#000; table-layout:auto; }
        thead th{ background:#f7f7f7; }
        th, td{ padding:10px 12px; border-bottom:1px solid #eee; text-align:left; vertical-align:middle; }
        tr:last-child td{ border-bottom:none; }
        .thumb{ width:48px; height:48px; object-fit:cover; border-radius:8px; background:#ddd; }
        .acciones{ display:flex; gap:8px; flex-wrap:wrap; }

        /* footer */
        footer{ background:rgba(217,217,217,.25); border-radius:15px; margin:10px; padding:12px 16px; backdrop-filter:blur(2px); }
        .footer-container{ display:flex; align-items:center; justify-content:center; gap:18px; flex-wrap:wrap; }
        .footer-contacto{ display:flex; align-items:center; gap:14px; }
        .footer-contacto img{ width:32px; height:32px; display:block; border-radius:8px; }
        .footer-texto p{ margin:0; }

        /* responsive */
        @media (max-width:700px){
            .site-title{ font-size:18px }
            .menu ul{ gap:6px; flex-wrap:wrap }
            th, td{ padding:8px 10px }
        }

        @media (max-width: 600px) {
            .table-wrap, table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            thead { display: none; }
            tr { margin-bottom: 18px; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
            td {
                padding: 10px 12px;
                border: none;
                position: relative;
            }
            td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 4px;
                color: #739CF4;
            }
            .acciones { flex-direction: column; gap: 6px; }
        }

    </style>
</head>
<body>
<!-- Video de fondo -->
<video autoplay muted loop id="myVideo" playsinline>
    <source src="../img/tech.mp4" type="video/mp4">
</video>

<!-- Header -->
<header>
    <div class="header-container">
        <div class="logo"><img src="../img/logo.png" alt="OZC Tech Logo"></div>
        <h1 class="site-title">OZC Tech</h1>
        <nav class="menu">
            <ul>
                <li><a href="../index.html">INICIO</a></li>
                <li><a href="../servicios.html">SERVICIOS</a></li>
                <li><a href="login.php">LOGIN</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Contenido -->
<main class="wrap">
    <div class="toolbar">
        <h2 style="margin:0;">Clientes (<?= is_array($clientes) ? count($clientes) : 0 ?>)</h2>
        <a class="btn" href="create.php">➕ Nuevo cliente</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash"><?= htmlspecialchars($_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th style="width:70px;">ID</th>
                <th style="width:80px;">Foto</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th style="width:220px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$clientes): ?>
                <tr><td colspan="6">No hay clientes aún.</td></tr>
            <?php else: ?>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= (int)$c['idC'] ?></td>
                        <td>
                            <?php if (!empty($c['imageC'])): ?>
                                <img class="thumb" src="uploads/<?= htmlspecialchars($c['imageC']) ?>" alt="Foto de <?= htmlspecialchars($c['nameC']) ?>">
                            <?php else: ?>
                                <span style="color:#666">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($c['nameC']) ?></td>
                        <td><?= htmlspecialchars($c['emailC']) ?></td>
                        <td><?= htmlspecialchars($c['telC']) ?></td>
                        <td>
                            <div class="acciones">
                                <a class="btn" href="edit.php?id=<?= (int)$c['idC'] ?>">✏️ Editar</a>
                                <form action="delete.php" method="post" onsubmit="return confirm('¿Eliminar al cliente #<?= (int)$c['idC'] ?>?');" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= (int)$c['idC'] ?>">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                                    <button type="submit" class="btn danger">🗑️ Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Footer -->
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
</body>
</html>
