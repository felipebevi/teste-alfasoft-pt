# Contact Management - Laravel 10

Aplicacao CRUD para gestao de contatos 

## Como executar

```bash
composer install
php artisan migrate:fresh --seed
```

OBS: sempre que rodar os testes um novo seed precisa ser feito pois eu nao instalei driver novo para rodar na memoria em um SQLite.

## Login Admin

- Email: admin@admin.com
- Password: 123456

## O que fiz e por que

### 1. Migration e Model
Criei a tabela contacts com migration e o Model Contact com SoftDeletes para que nao apague o registro de verdade

### 2. Controller e Rotas
Criei o ContactController com --resource para gerar os 7 metodos CRUD do teste. Registrei as rotas com Route::resource. Usei validate() nativo do Laravel para validar nome (min 6), contact (9 digitos, unico) e email (valido, unico)

### 3. Layout e Listagem
Criei um layout base com Bootstrap padrão via CDN para ter visual limpo sem dependencias extras. A pagina index lista os contatos em tabela com links para visualizar e botao para editar e deletar

### 4. Formulario de Criar
Criei o formulario com validacao visual usando diretivas @error do Blade e old() para repopular campos apos erro sempre validando na controller para evitar injection ou hacks

### 5. Pagina de Detalhes
Criei uma pagina standalone (nao popup como o teste solicitava) mostrando os 4 campos do contato com botao de editar e deletar apenas quando está logado

### 6. Formulario de Editar
Criei o formulario de edicao reutilizando a mesma estrutura do create. Usei @method('PUT') e old() com valor padrao do registro atual. A regra unique ignora o proprio registro na edicao

### 7. Autenticacao
Criei um AuthController simples com login/logout usando Auth::attempt() nativo. Separei as rotas: index e show que sao publicas, o resto exige login. Criei um seeder para o usuario admin e 3 users de teste para facilitar o uso inicial

### 8. Testes Automatizados
User uma Feature Tests com PHPUnit para validacao de formularios (create e update), soft delete e protecao de rotas por autenticacao

## Sobre os testes

Os testes usam o mesmo banco MySQL porque o ambiente nao possui o driver SQLite. Por isso, ao rodar os testes o banco sera limpo. Apos executar os testes, rode novamente:

```bash
php artisan migrate:fresh --seed
```

Para rodar os testes:

```bash
php artisan test
```
