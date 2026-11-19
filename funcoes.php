<?php
// Pequenas funções compartilhadas pelas três páginas de cadastro.
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Evita interpretar os dados cadastrados como código HTML.
function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Lê um campo obrigatório do formulário.
function lerCampo($nome) {
    $valor = $_POST[$nome] ?? '';
    if (!is_string($valor) || trim($valor) === '' || strlen($valor) > 1000) {
        throw new Exception('Preencha todos os campos corretamente.');
    }
    return trim($valor);
}

function verificarFormulario() {
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['token'], $token)) {
        throw new Exception('Recarregue a página e tente novamente.');
    }
}

function criarId($texto) {
    if (!is_string($texto) || !preg_match('/^[a-f0-9]{24}$/i', $texto)) {
        throw new Exception('Identificador inválido.');
    }
    return new MongoDB\BSON\ObjectId($texto);
}

function validarData($texto) {
    $data = DateTime::createFromFormat('!Y-m-d', $texto);
    if (!$data || $data->format('Y-m-d') !== $texto) {
        throw new Exception('Informe uma data válida.');
    }
}

// Em erros de duplicidade, não exibimos detalhes internos do banco.
function mensagemErro($erro) {
    if ($erro instanceof MongoDB\Driver\Exception\BulkWriteException) {
        return 'Registro repetido. Confira e-mail, registro, código ou matrícula.';
    }
    if ($erro instanceof MongoDB\Driver\Exception\Exception) {
        return 'Não foi possível salvar no banco. Confira a conexão.';
    }
    return $erro->getMessage();
}
