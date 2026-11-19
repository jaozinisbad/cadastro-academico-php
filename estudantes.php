<?php
require 'funcoes.php';
require 'conexao.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verificarFormulario();
        $email = strtolower(lerCampo('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Informe um e-mail válido.');
        }
        $nascimento = lerCampo('nascimento');
        validarData($nascimento);
        if ($nascimento > date('Y-m-d')) {
            throw new Exception('O nascimento não pode estar no futuro.');
        }

        // Documento com seis campos do estudante e seis campos de endereço.
        $estudantes->insertOne([
            'nome' => lerCampo('nome'),
            'email' => $email,
            'telefone' => lerCampo('telefone'),
            'nascimento' => $nascimento,
            'registro' => lerCampo('registro'),
            'turma' => lerCampo('turma'),
            'endereco' => [
                'cep' => lerCampo('cep'),
                'rua' => lerCampo('rua'),
                'numero' => lerCampo('numero'),
                'bairro' => lerCampo('bairro'),
                'cidade' => lerCampo('cidade'),
                'estado' => lerCampo('estado'),
            ],
        ]);
        // Redirecionar evita repetir o cadastro ao atualizar a página.
        header('Location: estudantes.php', true, 303);
        exit;
    } catch (Throwable $excecao) {
        $erro = mensagemErro($excecao);
    }
}
$lista = $estudantes->find();
require 'cabecalho.php';
?>

<h1>Estudantes</h1>
<form method="post" class="formulario">
    <input type="hidden" name="token" value="<?= escapar($_SESSION['token']) ?>">
    <h2>Dados do estudante</h2>
    <div class="campos">
    <?php
    $campos = ['nome'=>'Nome', 'email'=>'E-mail', 'telefone'=>'Telefone', 'nascimento'=>'Nascimento', 'registro'=>'Registro acadêmico', 'turma'=>'Turma'];
    foreach ($campos as $campo => $rotulo):
        $tipo = 'text';
        if ($campo === 'email') { $tipo = 'email'; }
        if ($campo === 'nascimento') { $tipo = 'date'; }
    ?>
        <div class="campo">
            <label for="<?= $campo ?>" ><?= $rotulo ?></label>
            <input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $tipo ?>"  value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="150" required>
        </div>
    <?php endforeach; ?>
    </div>
    <h2>Endereço</h2>
    <div class="campos">
    <?php
    $camposEndereco = ['cep'=>'CEP', 'rua'=>'Rua', 'numero'=>'Número', 'bairro'=>'Bairro', 'cidade'=>'Cidade', 'estado'=>'Estado'];
    foreach ($camposEndereco as $campo => $rotulo): ?>
        <div class="campo">
            <label for="<?= $campo ?>" ><?= $rotulo ?></label>
            <input id="<?= $campo ?>" name="<?= $campo ?>"  value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="150" required>
        </div>
    <?php endforeach; ?>
    </div>
    <button>Cadastrar estudante</button>
</form>

<h2>Estudantes cadastrados</h2>
<div class="tabela">
<table>
    <thead><tr><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Nascimento</th><th>Registro</th><th>Turma</th><th>Endereço</th></tr></thead>
    <tbody>
    <?php foreach ($lista as $estudante): ?>
        <tr>
            <td><?= escapar($estudante['nome']) ?></td>
            <td><?= escapar($estudante['email']) ?></td>
            <td><?= escapar($estudante['telefone']) ?></td>
            <td><?= escapar($estudante['nascimento']) ?></td>
            <td><?= escapar($estudante['registro']) ?></td>
            <td><?= escapar($estudante['turma']) ?></td>
            <td><?= escapar($estudante['endereco']['rua']) ?>, <?= escapar($estudante['endereco']['numero']) ?> — <?= escapar($estudante['endereco']['bairro']) ?>, <?= escapar($estudante['endereco']['cidade']) ?>/<?= escapar($estudante['endereco']['estado']) ?> — CEP <?= escapar($estudante['endereco']['cep']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require 'rodape.php'; ?>
