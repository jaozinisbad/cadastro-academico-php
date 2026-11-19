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

const ESTADOS = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
const TURNOS = ['Matutino', 'Vespertino', 'Noturno', 'Integral'];

// O limite também é conferido no PHP, contando letras com acento corretamente.
function lerCampo($nome, $limite = 1000) {
    $valor = $_POST[$nome] ?? '';
    if (!is_string($valor) || trim($valor) === '') {
        throw new Exception('Preencha todos os campos corretamente.');
    }
    $valor = trim($valor);
    $quantidade = preg_match_all('/./us', $valor);
    if ($quantidade === false || $quantidade > $limite) {
        $rotulo = ucfirst(str_replace('_', ' ', $nome));
        throw new Exception("$rotulo: máximo de $limite caracteres.");
    }
    return $valor;
}

function lerCep() {
    $cep = lerCampo('cep', 9);
    if (!preg_match('/^[0-9]{5}-?[0-9]{3}$/', $cep)) {
        throw new Exception('O CEP deve conter exatamente 8 números.');
    }
    return str_replace('-', '', $cep);
}

function lerTelefone() {
    $telefone = lerCampo('telefone', 15);
    if (!preg_match('/^[0-9() -]+$/', $telefone)) {
        throw new Exception('Informe um telefone com DDD, usando 10 ou 11 números.');
    }
    $numeros = preg_replace('/[() -]/', '', $telefone);
    if (!preg_match('/^[0-9]{10,11}$/', $numeros)) {
        throw new Exception('O telefone deve conter DDD e 10 ou 11 números.');
    }
    return $numeros;
}

function validarOpcao($valor, $opcoes, $rotulo) {
    if (!in_array($valor, $opcoes, true)) {
        throw new Exception("Selecione um valor válido para $rotulo.");
    }
    return $valor;
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
