# Biblioteca de Yuri Marinho — PHP completo

Backend em PHP seguindo a lógica de projeto com `api`, `public`, `src/Controllers`, `src/Core`, Composer, JWT e banco MySQL.

## O que foi implementado

- Login por e-mail ou `@username`.
- Cadastro com validação de username.
- Senha com `password_hash`/bcrypt.
- JWT com `firebase/php-jwt`.
- Perfis públicos com `@username`.
- Catálogo separado da biblioteca pessoal.
- Tabelas relacionais: usuários, autores, gêneros, séries e livros.
- Biblioteca individual por usuário.
- Status dos livros:
  - `READ` — lido
  - `READING` — lendo
  - `NEXT_READ` — próxima leitura
  - `WANT_FUTURE` — desejo ter futuramente
  - `DUSTY` — pegando poeira
  - `GIFT_ACCEPTED` — aceito presente
  - `ABANDONED` — abandonei
  - `REREADING` — relendo
  - `WANT_SPECIAL_EDITION` — quero edição especial
- Ordenação personalizada com `display_order`.
- Notas pessoais, avaliação e favorito.
- Seguidores.
- Feed dos usuários seguidos.
- Curtida em perfil.
- Comentários em livros.
- Aba Pix com cadastro de chave.
- Presentes via Pix: registro de intenção de pagamento e retorno da chave Pix cadastrada pelo destinatário.
- Seed com a biblioteca atualizada, incluindo autores, gêneros e 49 livros.

## Instalação

```bash
cd biblioteca-yuri-php-completo
composer install
cp .env.example .env
```

Crie um banco MySQL chamado `biblioteca_yuri` e configure o `.env`.

Rode a migration:

```bash
mysql -u root -p biblioteca_yuri < database/migration.sql
```

Rode o seed:

```bash
composer seed
```

Inicie o servidor:

```bash
composer start
```

API:

```text
http://localhost:8000/api
```

Frontend estático:

Abra os arquivos em `frontend/` no navegador ou sirva a pasta com Live Server.

## Rotas principais

### Auth

```text
POST /api/auth/register
POST /api/auth/login
GET  /api/auth/me
```

### Usuários

```text
GET  /api/users?q=
GET  /api/users/{username}
PUT  /api/users/profile
POST /api/users/{id}/like
POST /api/follow/{id}
DELETE /api/follow/{id}
```

### Feed

```text
GET /api/feed
```

### Livros

```text
GET    /api/books?q=&title=&author=&genre=
GET    /api/books/{id}
POST   /api/books
GET    /api/books/categories
GET    /api/books/authors
POST   /api/books/{bookId}/add
PUT    /api/user-books/{bookId}
DELETE /api/user-books/{bookId}
PUT    /api/user-books/reorder
POST   /api/books/{bookId}/comments
```

### Pix e presentes

```text
GET  /api/pix/key
POST /api/pix/key
POST /api/pix/gift/{bookId}
```

## Observação sobre Pix

Esta versão não realiza transferência financeira real. Ela salva a chave Pix do usuário e cria um registro de presente pendente. Para produção, integre com um PSP/banco autorizado, como Gerencianet/Efi, Mercado Pago, Banco do Brasil, Asaas, OpenPix ou outro provedor Pix.
