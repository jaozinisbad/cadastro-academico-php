<?php
// O Composer carrega a biblioteca oficial do MongoDB.
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Execute composer install na pasta do projeto antes de abrir.');
}
require_once __DIR__ . '/vendor/autoload.php';

$config = [
    'uri' => getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017',
    'banco' => getenv('MONGODB_DATABASE') ?: 'trabalho_backend_simples',
];
if (file_exists(__DIR__ . '/config-local.php')) {
    $config = require __DIR__ . '/config-local.php';
}

try {
    $cliente = new MongoDB\Client($config['uri'], ['serverSelectionTimeoutMS' => 3000]);
    $banco = $cliente->selectDatabase($config['banco']);

    // Estas são as três coleções usadas no trabalho.
    $estudantes = $banco->estudantes;
    $cursos = $banco->cursos;
    $matriculas = $banco->matriculas;

    // Os índices evitam registros repetidos.
    $estudantes->createIndex(['registro' => 1], ['unique' => true]);
    $estudantes->createIndex(['email' => 1], ['unique' => true]);
    $cursos->createIndex(['codigo' => 1], ['unique' => true]);
    $matriculas->createIndex(['estudante_id' => 1, 'curso_id' => 1], ['unique' => true]);
} catch (Throwable $erro) {
    http_response_code(503);
    exit('Não foi possível conectar. Verifique se o MongoDB está ligado e confira a configuração.');
}
