<?php
// PHP/404.php — Redirige a la página inicial cuando ocurre un 404
declare(strict_types=1);

/*
 * Para la rúbrica:
 * - Este script se ejecuta cuando Apache no encuentra un recurso (ErrorDocument 404).
 * - Redirigimos a la portada manteniendo trazabilidad en logs si lo deseas.
 */

// (Opcional) registra el intento en error_log:
error_log(sprintf('[404] %s %s', $_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '-'));

// Redirección inmediata a la página inicial
header('Location: ../src/index.html', true, 302);
exit;

/* Fallback (si headers ya fueron enviados)
<!DOCTYPE html>
<html lang="es">
  <meta charset="utf-8">
  <meta http-equiv="refresh" content="0;url=/src/index.html">
  <title>Redireccionando…</title>
  <p>Redireccionando a <a href="/src/index.html">/src/index.html</a></p>
</html>
*/
