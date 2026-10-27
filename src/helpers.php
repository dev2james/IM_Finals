<?php
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function peso(float|string $n): string { $n = (float)$n; return ($n < 0 ? '-' : '') . '₱' . number_format(abs($n), 2); }
function fdate(string $d): string { return date('M j, Y', strtotime($d)); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function flash(string $msg, string $type = 'ok'): void { $_SESSION['flash'] = [$msg, $type]; }
function redirect(string $url): never { header("Location: $url"); exit; }
