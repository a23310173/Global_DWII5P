<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/con.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: lista.php'); exit; }
if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) { http_response_code(419); exit('CSRF inválido.'); }

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: lista.php'); exit; }

$name = trim($_POST['nameC'] ?? '');
$email = trim($_POST['emailC'] ?? '');
$tel = trim($_POST['telC'] ?? '');
$currentImage = trim($_POST['current_image'] ?? '');

$errors = [];
if ($name === '')  $errors[] = 'Nombre requerido';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
if ($tel === '')   $errors[] = 'Teléfono requerido';

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

$newImage = null;
if (!empty($_FILES['imageC']['name'])) {
    $tmp = $_FILES['imageC']['tmp_name'];
    $size = (int)($_FILES['imageC']['size'] ?? 0);
    $err  = (int)($_FILES['imageC']['error'] ?? 0);

    if ($err === UPLOAD_ERR_OK && is_uploaded_file($tmp)) {
        if ($size > 4 * 1024 * 1024) $errors[] = 'La imagen supera 4MB';
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($tmp) ?: 'application/octet-stream';
        $ext   = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default => null
        };
        if ($ext === null) $errors[] = 'Formato de imagen no permitido';

        if (!$errors) {
            $newImage = sprintf('cli_%s.%s', bin2hex(random_bytes(8)), $ext);
            if (!move_uploaded_file($tmp, $uploadDir . '/' . $newImage)) {
                $errors[] = 'No se pudo guardar la imagen';
                $newImage = null;
            }
        }
    } elseif ($err !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Error al subir la imagen';
    }
}

if ($errors) {
    $_SESSION['flash'] = implode(' • ', $errors);
    header("Location: edit.php?id={$id}");
    exit;
}

if ($newImage) {
    if ($currentImage && is_file($uploadDir . '/' . $currentImage)) {
        @unlink($uploadDir . '/' . $currentImage);
    }
    run('UPDATE clientes SET nameC = ?, emailC = ?, telC = ?, imageC = ? WHERE idC = ?', [
        $name, $email, $tel, $newImage, $id
    ]);
} else {
    run('UPDATE clientes SET nameC = ?, emailC = ?, telC = ? WHERE idC = ?', [
        $name, $email, $tel, $id
    ]);
}

$_SESSION['flash'] = 'Cliente actualizado';
header('Location: lista.php');
exit;
