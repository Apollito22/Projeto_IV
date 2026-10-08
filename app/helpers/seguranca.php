<?php
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
}

function campo(string $nome): string
{
    return isset($_POST[$nome]) && is_string($_POST[$nome]) ? $_POST[$nome] : '';
}

function redirecionar(string $pagina): void
{
    header('Location: ' . $pagina, true, 303);
    exit;
}

function validar_csrf(): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], campo('csrf_token'));
}

function gerar_slug(string $texto): string
{
    $texto = strtr($texto, ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A', 'é' => 'e', 'ê' => 'e', 'è' => 'e', 'ë' => 'e', 'É' => 'E', 'Ê' => 'E', 'È' => 'E', 'Ë' => 'E', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'ç' => 'c', 'Ç' => 'C']);
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $texto === false ? '' : $texto)), '-');
}
