# Trabalho simples — PHP e MongoDB

Sistema para **cadastrar e listar estudantes, cursos e matrículas**.
PHP sem framework, páginas separadas e CSS básico no arquivo `estilo.css`.
Esta versão faz o cadastro solicitado no enunciado, sem telas de edição e exclusão.

## O que foi pedido

- Estudante com 6 campos: nome, e-mail, telefone, nascimento, registro e turma.
- Endereço com 6 campos: CEP, rua, número, bairro, cidade e estado.
- Curso com 5 campos: nome, código, descrição, carga horária e turno.
- Um estudante em vários cursos, e vários estudantes em um curso.
- Tudo salvo no MongoDB, com estudantes e cursos em coleções separadas.

## Como abrir

Você precisa de PHP 8.2 ou superior, Composer, extensão PHP MongoDB 2.5
(série 2.x) e MongoDB local ligado. O visual usa um arquivo CSS local.

1. Abra o terminal dentro desta pasta.
2. Execute `composer install` para baixar a biblioteca.
3. Execute `php -S localhost:8000`.
4. Abra **http://localhost:8000** no navegador.

O banco e os índices são criados ao abrir uma página de cadastro.
O endereço padrão do MongoDB é `mongodb://127.0.0.1:27017`.

Se precisar instalar a extensão no Windows, use `php --ini` para localizar
o `php.ini`. Baixe a DLL da sua versão PHP, modo TS/NTS e arquitetura no
[diretório oficial](https://downloads.php.net/~windows/pecl/releases/mongodb/),
copie `php_mongodb.dll` para `ext` e adicione `extension=mongodb` ao `php.ini`.
Confira com `php -m`. Reinicie o servidor depois da alteração.

Para Atlas ou outra conexão, copie `config-exemplo.php` como `config-local.php`
e altere a URI. No Atlas também é necessário criar um usuário de banco e
permitir o IP do computador. Não publique `config-local.php` com sua senha.

## Como usar

1. Clique em **Estudantes** e cadastre os dados e o endereço.
2. Clique em **Cursos** e cadastre um curso.
3. Clique em **Matrículas** e selecione os dois.
4. Para mostrar o relacionamento, cadastre dois estudantes e dois cursos.
   Matricule o primeiro estudante nos dois cursos e o segundo em um deles.

## Entendendo os arquivos

| Arquivo | Para que serve |
| --- | --- |
| `index.php` | Página inicial com o menu |
| `conexao.php` | Conecta no MongoDB e cria índices para evitar duplicação |
| `estudantes.php` | Recebe o formulário, salva e lista estudantes |
| `cursos.php` | Recebe o formulário, salva e lista cursos |
| `matriculas.php` | Liga o estudante ao curso e lista as matrículas |
| `estilo.css` | Cores, fontes, botões, campos e tabelas de todas as páginas |
| `cabecalho.php` | Começo do HTML e menu compartilhado |
| `rodape.php` | Final do HTML |
| `funcoes.php` | Funções pequenas para ler campos e proteger os formulários |

Em cada página de cadastro, o PHP no começo verifica se o formulário foi
enviado (`POST`). O `insertOne` salva um documento. O `find` consulta os
documentos. O `foreach` percorre os resultados e monta as linhas da tabela.

## Relacionamento explicado de forma simples

Existem três coleções: `estudantes`, `cursos` e `matriculas`.
O MongoDB cria um `_id` para cada documento. Na matrícula guardamos
`estudante_id` e `curso_id` como ObjectId, apontando para os outros cadastros.

```text
Estudante Ana ── matrícula 1 ── Curso PHP
Estudante Ana ── matrícula 2 ── Curso MongoDB
Estudante João ─ matrícula 3 ── Curso PHP
```

Assim Ana faz dois cursos e PHP tem dois estudantes.
O índice do par `(estudante_id, curso_id)` impede matrícula repetida.
Somente o endereço fica embutido no estudante. Cursos não ficam embutidos.

As consultas por `findOne` dentro da listagem de matrículas são fáceis de
acompanhar para este trabalho pequeno. Um sistema maior pode juntar as
consultas para reduzir o número de acessos ao banco.


Referências: [MongoDB com PHP](https://www.mongodb.com/pt-br/docs/languages/php/)
e [manual PHP](https://www.php.net/manual/en/book.mongodb.php).
