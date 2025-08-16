<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: lista.php'); exit; }
if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) { http_response_code(419); exit('CSRF inválido.'); }

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: lista.php'); exit; }

// Borrar imagen del disco si existe
$row = run('SELECT imageC FROM clientes WHERE idC = ?', [$id])->fetch();
$uploadDir = __DIR__ . '/uploads';
if ($row && !empty($row['imageC'])) {
    $path = $uploadDir . '/' . $row['imageC'];
    if (is_file($path)) @unlink($path);
}

// Eliminar registro
run('DELETE FROM clientes WHERE idC = ?', [$id]);

$_SESSION['flash'] = "Cliente #{$id} eliminado";
header('Location: lista.php');
exit;
