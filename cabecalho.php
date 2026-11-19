<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro acadêmico</title>
    <!-- Todas as cores, fontes e espaçamentos ficam neste arquivo. -->
    <link href="estilo.css" rel="stylesheet">
</head>
<body>
<!-- Altere aqui o título e os links do menu de todas as páginas. -->
<header class="cabecalho">
    <div class="container">
        <a class="titulo-site" href="index.php">Cadastro acadêmico</a>
        <nav class="menu" aria-label="Menu principal">
            <a href="estudantes.php">Estudantes</a>
            <a href="cursos.php">Cursos</a>
            <a href="matriculas.php">Matrículas</a>
        </nav>
    </div>
</header>
<main class="container">
<?php if (!empty($erro)): ?>
    <div class="aviso erro" role="alert"><?= escapar($erro) ?></div>
<?php endif; ?>
