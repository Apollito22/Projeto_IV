<?php
require_once __DIR__ . '/../config/bootstrap.php';
$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
(new HomeController())->index(mb_strlen($slug, 'UTF-8') <= 150 ? $slug : '');
