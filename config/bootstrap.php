<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../app/helpers/seguranca.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/primeiro_admin.php';
require_once __DIR__ . '/../app/helpers/upload.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Categoria.php';
require_once __DIR__ . '/../app/models/Conteudo.php';
require_once __DIR__ . '/../app/models/Midia.php';
require_once __DIR__ . '/../app/models/Auditoria.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/ConteudoController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/session.php';
