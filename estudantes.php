<?php
require 'funcoes.php';
require 'conexao.php';
$erro = '';
// Estes mesmos limites são usados para salvar e para montar o formulário.
$limites = ['nome'=>100, 'email'=>254, 'telefone'=>15, 'nascimento'=>10, 'registro'=>20, 'turma'=>30];
$limitesEndereco = ['cep'=>9, 'rua'=>100, 'numero'=>10, 'bairro'=>60, 'cidade'=>100, 'estado'=>2];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verificarFormulario();
        $email = strtolower(lerCampo('email', $limites['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Informe um e-mail válido.');
        }
        $nascimento = lerCampo('nascimento', $limites['nascimento']);
        validarData($nascimento);
        if ($nascimento > date('Y-m-d')) {
            throw new Exception('O nascimento não pode estar no futuro.');
        }

        // Documento com seis campos do estudante e seis campos de endereço.
        $estudantes->insertOne([
            'nome' => lerCampo('nome', $limites['nome']),
            'email' => $email,
            'telefone' => lerTelefone(),
            'nascimento' => $nascimento,
            'registro' => lerCampo('registro', $limites['registro']),
            'turma' => lerCampo('turma', $limites['turma']),
            'endereco' => [
                'cep' => lerCep(),
                'rua' => lerCampo('rua', $limitesEndereco['rua']),
                'numero' => lerCampo('numero', $limitesEndereco['numero']),
                'bairro' => lerCampo('bairro', $limitesEndereco['bairro']),
                'cidade' => lerCampo('cidade', $limitesEndereco['cidade']),
                'estado' => validarOpcao(strtoupper(lerCampo('estado', 2)), ESTADOS, 'Estado'),
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
        if ($campo === 'telefone') { $tipo = 'tel'; }
    ?>
        <div class="campo">
            <label for="<?= $campo ?>" ><?= $rotulo ?></label>
            <input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $tipo ?>" value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="<?= $limites[$campo] ?>" <?= $campo === 'nascimento' ? 'max="'.date('Y-m-d').'"' : '' ?> <?= $campo === 'telefone' ? 'inputmode="tel" pattern="[0-9]{10,11}|\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}" title="Informe o DDD e 10 ou 11 números." placeholder="(45) 99999-9999"' : '' ?> required>
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
            <?php if ($campo === 'estado'): ?>
                <select id="estado" name="estado" required>
                    <option value="">Selecione a UF</option>
                    <?php foreach (ESTADOS as $estado): ?>
                        <option value="<?= $estado ?>" <?= ($_POST['estado'] ?? '') === $estado ? 'selected' : '' ?>><?= $estado ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input id="<?= $campo ?>" name="<?= $campo ?>" value="<?= escapar(is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '') ?>" maxlength="<?= $limitesEndereco[$campo] ?>" <?= $campo === 'cep' ? 'inputmode="numeric" pattern="[0-9]{5}-?[0-9]{3}" title="Informe os 8 números do CEP." placeholder="85801-000"' : '' ?> required>
            <?php endif; ?>
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
