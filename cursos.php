<?php
require 'funcoes.php';
require 'conexao.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verificarFormulario();
        $carga = lerCampo('carga_horaria');
        if (!ctype_digit($carga) || (int) $carga < 1 || (int) $carga > 20000) {
            throw new Exception('A carga horária deve ser um inteiro de 1 a 20000.');
        }
        // Curso com cinco campos.
        $cursos->insertOne([
            'nome' => lerCampo('nome'),
            'codigo' => strtoupper(lerCampo('codigo')),
            'descricao' => lerCampo('descricao'),
            'carga_horaria' => (int) $carga,
            'turno' => lerCampo('turno'),
        ]);
        header('Location: cursos.php', true, 303);
        exit;
    } catch (Throwable $excecao) {
        $erro = mensagemErro($excecao);
    }
}
$lista = $cursos->find();
require 'cabecalho.php';
?>

<h1>Cursos</h1>
<form method="post" class="formulario">
    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
    <div class="campos">
    <?php
    $campos = ['nome'=>'Nome do curso', 'codigo'=>'Código', 'descricao'=>'Descrição', 'carga_horaria'=>'Carga horária', 'turno'=>'Turno'];
    foreach ($campos as $campo => $rotulo): ?>
        <div class="campo">
            <label for="<?= $campo ?>" ><?= $rotulo ?></label>
            <input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $campo === 'carga_horaria' ? 'number' : 'text' ?>" <?= $campo === 'carga_horaria' ? 'min="1" max="20000" step="1"' : '' ?>  value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="150" required>
        </div>
    <?php endforeach; ?>
    </div>
    <button>Cadastrar curso</button>
</form>

<h2>Cursos cadastrados</h2>
<div class="tabela">
<table>
    <thead><tr><th>Nome</th><th>Código</th><th>Descrição</th><th>Carga horária</th><th>Turno</th></tr></thead>
    <tbody>
    <?php foreach ($lista as $curso): ?>
        <tr>
            <td><?= escapar($curso['nome']) ?></td>
            <td><?= escapar($curso['codigo']) ?></td>
            <td><?= escapar($curso['descricao']) ?></td>
            <td><?= escapar($curso['carga_horaria']) ?> horas</td>
            <td><?= escapar($curso['turno']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'rodape.php'; ?>
