<?php
require 'funcoes.php';
require 'conexao.php';
$erro = '';
$limites = ['nome'=>100, 'codigo'=>20, 'descricao'=>500, 'carga_horaria'=>5, 'turno'=>10];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verificarFormulario();
        $carga = lerCampo('carga_horaria', $limites['carga_horaria']);
        if (!ctype_digit($carga) || (int) $carga < 1 || (int) $carga > 20000) {
            throw new Exception('A carga horária deve ser um inteiro de 1 a 20000.');
        }
        $codigo = strtoupper(lerCampo('codigo', $limites['codigo']));
        // Curso com cinco campos.
        $cursos->insertOne([
            'nome' => lerCampo('nome', $limites['nome']),
            'codigo' => $codigo,
            'descricao' => lerCampo('descricao', $limites['descricao']),
            'carga_horaria' => (int) $carga,
            'turno' => validarOpcao(lerCampo('turno', $limites['turno']), TURNOS, 'Turno'),
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
            <?php if ($campo === 'turno'): ?>
                <select id="turno" name="turno" required>
                    <option value="">Selecione o turno</option>
                    <?php foreach (TURNOS as $turno): ?>
                        <option value="<?= $turno ?>" <?= ($_POST['turno'] ?? '') === $turno ? 'selected' : '' ?>><?= $turno ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input id="<?= $campo ?>" name="<?= $campo ?>" type="text" <?= $campo === 'carga_horaria' ? 'inputmode="numeric" pattern="[0-9]{1,5}" title="Informe um inteiro de 1 a 20000 horas."' : '' ?> value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="<?= $limites[$campo] ?>" required>
            <?php endif; ?>
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
