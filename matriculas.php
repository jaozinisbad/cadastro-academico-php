<?php
require 'funcoes.php';
require 'conexao.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verificarFormulario();
        $estudanteId = criarId(lerCampo('estudante_id'));
        $cursoId = criarId(lerCampo('curso_id'));

        // Antes de matricular, verificamos se os dois cadastros existem.
        if (!$estudantes->findOne(['_id' => $estudanteId]) || !$cursos->findOne(['_id' => $cursoId])) {
            throw new Exception('Escolha um estudante e um curso cadastrados.');
        }
        // Salvamos apenas as referências, sem copiar estudante e curso.
        $matriculas->insertOne([
            'estudante_id' => $estudanteId,
            'curso_id' => $cursoId,
            'data' => date('Y-m-d'),
        ]);
        header('Location: matriculas.php', true, 303);
        exit;
    } catch (Throwable $excecao) {
        $erro = mensagemErro($excecao);
    }
}
$listaEstudantes = $estudantes->find()->toArray();
$listaCursos = $cursos->find()->toArray();
$listaMatriculas = $matriculas->find();
require 'cabecalho.php';
?>

<h1>Matrículas</h1>
<p>Escolha um estudante e o curso em que ele vai se matricular.</p>
<?php if (count($listaEstudantes) === 0 || count($listaCursos) === 0): ?>
    <div class="aviso">Cadastre pelo menos um estudante e um curso antes de matricular.</div>
<?php else: ?>
<form method="post" class="formulario">
    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
    <div class="campos">
        <div class="campo">
            <label for="estudante_id" >Estudante</label>
            <select id="estudante_id" name="estudante_id"  required>
                <option value="">Selecione</option>
                <?php foreach ($listaEstudantes as $estudante): ?>
                    <option value="<?= escapar($estudante['_id']) ?>"><?= escapar($estudante['nome']) ?> — <?= escapar($estudante['registro']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="curso_id" >Curso</label>
            <select id="curso_id" name="curso_id"  required>
                <option value="">Selecione</option>
                <?php foreach ($listaCursos as $curso): ?>
                    <option value="<?= escapar($curso['_id']) ?>"><?= escapar($curso['nome']) ?> — <?= escapar($curso['codigo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button>Matricular</button>
</form>
<?php endif; ?>

<h2>Matrículas realizadas</h2>
<div class="tabela">
<table>
    <thead><tr><th>Estudante</th><th>Curso</th><th>Data</th></tr></thead>
    <tbody>
    <?php foreach ($listaMatriculas as $matricula):
        // Buscamos cada documento a partir do ObjectId salvo na matrícula.
        $estudante = $estudantes->findOne(['_id' => $matricula['estudante_id']]);
        $curso = $cursos->findOne(['_id' => $matricula['curso_id']]);
    ?>
        <tr>
            <td><?= escapar($estudante['nome'] ?? 'Não encontrado') ?></td>
            <td><?= escapar($curso['nome'] ?? 'Não encontrado') ?></td>
            <td><?= escapar($matricula['data']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'rodape.php'; ?>
