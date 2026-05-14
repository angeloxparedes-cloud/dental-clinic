<?php
require 'config.php';
$id = intval($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM project_charter WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit;
