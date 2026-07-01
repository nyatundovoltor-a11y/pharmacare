<?php
require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? ($_SESSION['user'] ?? null ? 'dashboard' : 'login');

$router = new Router();
$router->dispatch($action);