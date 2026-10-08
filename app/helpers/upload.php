<?php
class UploadImagem
{
    public static function diretorio(): string
    {
        return getenv('PORTAL_UPLOAD_DIR') ?: __DIR__ . '/../../public/assets/uploads';
    }

    public static function salvar(?array $arquivo): ?array
    {
        if ($arquivo === null) return null;
        if (!isset($arquivo['error']) || !is_int($arquivo['error'])) {
            throw new InvalidArgumentException('Envie uma única imagem.');
        }
        if ($arquivo['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Não foi possível receber a imagem. Confira o limite de tamanho do servidor.');
        }
        $tmp = $arquivo['tmp_name'] ?? '';
        if (!is_string($tmp) || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('Upload inválido.');
        }
        $tamanho = filesize($tmp);
        if ($tamanho === false || $tamanho === 0 || $tamanho > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('A imagem deve ter até 5 MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $formatos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($formatos[$mime])) {
            throw new InvalidArgumentException('Use uma imagem JPEG, PNG ou WebP.');
        }
        $dimensoes = @getimagesize($tmp);
        if (!$dimensoes || $dimensoes[0] > 6000 || $dimensoes[1] > 6000 || $dimensoes[0] * $dimensoes[1] > 12000000) {
            throw new InvalidArgumentException('A imagem deve ter até 6000 pixels por lado e 12 milhões de pixels no total.');
        }
        $dados = file_get_contents($tmp);
        $imagem = $dados === false ? false : @imagecreatefromstring($dados);
        if ($imagem === false) throw new InvalidArgumentException('Não foi possível ler a imagem.');
        $nome = bin2hex(random_bytes(16)) . '.' . $formatos[$mime];
        $destino = self::diretorio() . '/' . $nome;
        try {
            if (!is_dir(self::diretorio()) || !is_writable(self::diretorio())) {
                throw new InvalidArgumentException('A pasta de uploads não está disponível para gravação.');
            }
            imagealphablending($imagem, false);
            imagesavealpha($imagem, true);
            $ok = $mime === 'image/jpeg' ? imagejpeg($imagem, $destino, 90) : ($mime === 'image/png' ? imagepng($imagem, $destino) : imagewebp($imagem, $destino, 90));
            if (!$ok) throw new InvalidArgumentException('Não foi possível salvar a imagem.');
            chmod($destino, 0644);
            $original = isset($arquivo['name']) && is_string($arquivo['name']) ? basename(str_replace('\\', '/', $arquivo['name'])) : $nome;
            if (!mb_check_encoding($original, 'UTF-8')) $original = $nome;
            return ['nome_arquivo' => mb_substr($original, 0, 255, 'UTF-8'), 'caminho' => 'assets/uploads/' . $nome, 'tipo_mime' => $mime, 'tamanho_bytes' => filesize($destino)];
        } catch (Throwable $erro) {
            if (is_file($destino)) unlink($destino);
            throw $erro;
        } finally {
            imagedestroy($imagem);
        }
    }

    public static function remover(?string $caminho): void
    {
        if ($caminho === null || !preg_match('~^assets/uploads/([a-f0-9]{32}\.(?:jpg|png|webp))$~D', $caminho, $partes)) return;
        $arquivo = self::diretorio() . '/' . $partes[1];
        if (is_file($arquivo) && !unlink($arquivo)) error_log('Não foi possível remover a imagem: ' . $partes[1]);
    }
}

function imagem_local(?string $caminho): bool
{
    return is_string($caminho) && preg_match('~^assets/uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$~D', $caminho) === 1;
}

function link_video_valido(string $url): bool
{
    if ($url === '') return true;
    if (strlen($url) > 500 || !filter_var($url, FILTER_VALIDATE_URL)) return false;
    $partes = parse_url($url);
    return isset($partes['scheme'], $partes['host']) && in_array(strtolower($partes['scheme']), ['http', 'https'], true)
        && !isset($partes['user']) && !isset($partes['pass']);
}
